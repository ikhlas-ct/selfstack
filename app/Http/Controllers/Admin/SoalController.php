<?php

namespace App\Http\Controllers\Admin;

use App\Exports\SoalTemplateExport;
use App\Http\Controllers\Controller;
use App\Imports\SoalImport;
use App\Models\GrupKosakata;
use App\Models\GrupSoal;
use App\Models\LevelTocfl;
use App\Models\Paragraf;
use App\Models\PolaTataBahasa;
use App\Models\Soal;
use App\Support\LabelSoal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Semua urusan soal dalam satu controller:
 *
 *   PAKET SOAL (GrupSoal)   index, show, create, store, edit, update, destroy, toggleAktif
 *   SOAL di dalam paket     createSoal, storeSoal, editSoal, updateSoal, destroySoal, ubahStatus
 *   IMPORT EXCEL            import, importStore, template
 *
 * Catatan: GrupSoal::soals() sudah punya orderBy(urutan, id). Query agregat dengan
 * GROUP BY di sini memakai reorder() supaya tidak kena ONLY_FULL_GROUP_BY di MySQL.
 */
class SoalController extends Controller
{
    private const PAKET_PER_HALAMAN = 12;
    private const SOAL_PER_HALAMAN  = 25;

    /** Tipe karakter yang wajib terhubung ke karakter. */
    private const TIPE_BUTUH_KARAKTER = [
        'karakter_ke_arti', 'arti_ke_karakter', 'karakter_ke_radikal', 'arti_karakter_dalam_kata',
    ];

    /* ================================================================ PAKET: INDEX */

    public function index(Request $request)
    {
        $cari  = trim((string) $request->query('search', ''));
        $level = $request->filled('level') ? (int) $request->query('level') : null;
        $jenis = $request->query('jenis');
        $aktif = $request->query('aktif');

        if (! in_array($jenis, [GrupSoal::JENIS_LATIHAN, GrupSoal::JENIS_UJIAN], true)) {
            $jenis = null;
        }
        if (! in_array($aktif, ['1', '0'], true)) {
            $aktif = null;
        }

        // Angka di pil jenis: total keseluruhan, tidak ikut filter.
        $ringkas = [
            'semua'   => GrupSoal::count(),
            'latihan' => GrupSoal::latihan()->count(),
            'ujian'   => GrupSoal::ujian()->count(),
        ];

        $paket = GrupSoal::query()
            ->with(['levelTocfl', 'grupKosakata'])
            ->withCount([
                'soals',
                'soals as perlu_dicek_count' => fn ($q) => $q->where('status_validasi', Soal::STATUS_PERLU_DICEK),
                'hasilUjians as percobaan_count' => fn ($q) => $q->whereNotNull('selesai_pada'),
            ])
            ->when($cari !== '', fn ($q) => $q->where('judul', 'like', "%{$cari}%"))
            ->when($jenis, fn ($q) => $q->where('jenis', $jenis))
            ->when($level, fn ($q) => $q->where('level_tocfl_id', $level))
            ->when($aktif !== null, fn ($q) => $q->where('aktif', $aktif === '1'))
            ->orderByDesc('id')
            ->paginate(self::PAKET_PER_HALAMAN)
            ->withQueryString();

        $levels = LevelTocfl::orderBy('tingkat')->get();

        return view('pages.grup-soal.index', compact('paket', 'levels', 'ringkas', 'cari', 'level', 'jenis', 'aktif'));
    }

    /* ================================================================ PAKET: SHOW */

    public function show(Request $request, GrupSoal $grupSoal)
    {
        $grupSoal->load(['levelTocfl', 'grupKosakata']);

        $cari   = trim((string) $request->query('search', ''));
        $bagian = $request->query('bagian');
        $status = $request->query('status');

        if (! in_array($bagian, Soal::BAGIAN, true)) {
            $bagian = null;
        }
        if (! in_array($status, array_keys(LabelSoal::STATUS), true)) {
            $status = null;
        }

        $soals = $grupSoal->soals()
            ->when($cari !== '', fn ($q) => $q->where('pertanyaan', 'like', "%{$cari}%"))
            ->when($bagian, fn ($q) => $q->where('bagian', $bagian))
            ->when($status, fn ($q) => $q->where('status_validasi', $status))
            ->paginate(self::SOAL_PER_HALAMAN)
            ->withQueryString();

        $perStatus = $grupSoal->soals()->reorder()
            ->selectRaw('status_validasi, COUNT(*) as jml')
            ->groupBy('status_validasi')
            ->pluck('jml', 'status_validasi');

        $perBagian = $grupSoal->soals()->reorder()
            ->selectRaw('bagian, COUNT(*) as jml')
            ->groupBy('bagian')
            ->pluck('jml', 'bagian');

        $totalSoal = (int) $perStatus->sum();
        $percobaan = $grupSoal->hasilUjians()->selesai()->count();
        $rataSkor  = $percobaan > 0 ? round((float) $grupSoal->hasilUjians()->selesai()->avg('skor'), 1) : null;

        return view('pages.grup-soal.show', compact(
            'grupSoal', 'soals', 'perStatus', 'perBagian', 'totalSoal', 'percobaan', 'rataSkor',
            'cari', 'bagian', 'status'
        ));
    }

    /* ================================================================ PAKET: CREATE / EDIT */

    public function create()
    {
        $grupSoal = new GrupSoal([
            'jenis' => GrupSoal::JENIS_LATIHAN,
            'aktif' => true,
        ]);

        return view('pages.grup-soal.create', array_merge(['grupSoal' => $grupSoal], $this->dataFormPaket()));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validasiPaket($request);
        $data['sumber'] = GrupSoal::SUMBER_MANUAL;

        $paket = GrupSoal::create($data);

        return redirect()
            ->route('admin.grup-soal.show', $paket)
            ->with('sukses', 'Paket soal dibuat. Sekarang tambahkan soalnya.');
    }

    public function edit(GrupSoal $grupSoal)
    {
        return view('pages.grup-soal.edit', array_merge(['grupSoal' => $grupSoal], $this->dataFormPaket()));
    }

    public function update(Request $request, GrupSoal $grupSoal): RedirectResponse
    {
        $grupSoal->update($this->validasiPaket($request));

        return redirect()
            ->route('admin.grup-soal.show', $grupSoal)
            ->with('sukses', 'Paket soal diperbarui.');
    }

    /** Tombol aktif / nonaktif di daftar (menyembunyikan tanpa menghapus). */
    public function toggleAktif(GrupSoal $grupSoal): RedirectResponse
    {
        $grupSoal->update(['aktif' => ! $grupSoal->aktif]);

        return back()->with('sukses', $grupSoal->aktif ? 'Paket diaktifkan.' : 'Paket dinonaktifkan.');
    }

    /** Menghapus paket ikut menghapus soal, riwayat percobaan, dan jawabannya (cascade di database). */
    public function destroy(GrupSoal $grupSoal): RedirectResponse
    {
        $judul = $grupSoal->judul;
        $grupSoal->delete();

        return redirect()
            ->route('admin.grup-soal.index')
            ->with('sukses', 'Paket "' . $judul . '" dihapus.');
    }

    /* ================================================================ SOAL: CREATE / EDIT */

    public function createSoal(GrupSoal $grupSoal)
    {
        $soal = new Soal([
            'bagian'          => Soal::BAGIAN_KOSAKATA,
            'tingkat'         => $grupSoal->levelTocfl?->tingkat ?? 1,
            'poin'            => 1,
            // Tanpa orderBy relasi: max() dengan ORDER BY bisa ditolak MySQL strict.
            'urutan'          => ((int) Soal::where('grup_soal_id', $grupSoal->id)->max('urutan')) + 1,
            'status_validasi' => Soal::STATUS_DISETUJUI,
        ]);

        return view('pages.soal.create', array_merge(compact('grupSoal', 'soal'), $this->dataFormSoal()));
    }

    public function storeSoal(Request $request, GrupSoal $grupSoal): RedirectResponse
    {
        $data = $this->validasiSoal($request);

        $data['grup_soal_id'] = $grupSoal->id;
        $data['sumber']       = Soal::SUMBER_MANUAL;
        $this->isiDicekPada($data, null);

        Soal::create($data);

        if ($request->input('aksi') === 'tambah_lagi') {
            return redirect()
                ->route('admin.grup-soal.soal.create', $grupSoal)
                ->with('sukses', 'Soal disimpan. Silakan tambah soal berikutnya.');
        }

        return redirect()
            ->route('admin.grup-soal.show', $grupSoal)
            ->with('sukses', 'Soal ditambahkan.');
    }

    public function editSoal(Soal $soal)
    {
        $soal->load(['grupSoal', 'kosakata', 'contohKalimat', 'karakter', 'radikal']);
        $grupSoal = $soal->grupSoal;

        return view('pages.soal.edit', array_merge(compact('grupSoal', 'soal'), $this->dataFormSoal()));
    }

    public function updateSoal(Request $request, Soal $soal): RedirectResponse
    {
        $data = $this->validasiSoal($request);
        $this->isiDicekPada($data, $soal);

        $soal->update($data);

        return redirect()
            ->route('admin.grup-soal.show', $soal->grup_soal_id)
            ->with('sukses', 'Soal diperbarui.');
    }

    /** Tombol cepat "Setujui / Tolak / Perlu dicek" di daftar soal. */
    public function ubahStatus(Request $request, Soal $soal): RedirectResponse
    {
        $data = $request->validate([
            'status_validasi' => ['required', Rule::in([
                Soal::STATUS_DISETUJUI, Soal::STATUS_DITOLAK, Soal::STATUS_PERLU_DICEK,
            ])],
        ]);

        $soal->update([
            'status_validasi' => $data['status_validasi'],
            'dicek_pada'      => $data['status_validasi'] === Soal::STATUS_PERLU_DICEK ? $soal->dicek_pada : now(),
        ]);

        return back()->with('sukses', 'Status soal diubah menjadi "' . LabelSoal::STATUS[$data['status_validasi']] . '".');
    }

    /** Jawaban di riwayat ujian & laporan untuk soal ini ikut terhapus (cascade di database). */
    public function destroySoal(Soal $soal): RedirectResponse
    {
        $idPaket = $soal->grup_soal_id;
        $soal->delete();

        return redirect()
            ->route('admin.grup-soal.show', $idPaket)
            ->with('sukses', 'Soal dihapus.');
    }

    /* ================================================================ IMPORT EXCEL */

    public function import(Request $request): View
    {
        return view('pages.soal.import', [
            'pakets'       => GrupSoal::orderByDesc('id')->get(['id', 'judul', 'jenis']),
            'paketDipilih' => $request->integer('paket') ?: null,
            'levels'       => LevelTocfl::orderBy('tingkat')->get(),
            'bagian'       => LabelSoal::BAGIAN,
            'tipe'         => LabelSoal::TIPE,
            'jenisPaket'   => array_keys(LabelSoal::JENIS),
        ]);
    }

    public function importStore(Request $request): RedirectResponse
    {
        $request->validate([
            'file'        => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
            'paket_id'    => ['nullable', 'integer', 'exists:grup_soals,id'],
            'status_awal' => ['required', Rule::in([Soal::STATUS_PERLU_DICEK, Soal::STATUS_DISETUJUI])],
            'sumber'      => ['required', Rule::in([Soal::SUMBER_AI, Soal::SUMBER_MANUAL])],
        ], [
            'file.required'   => 'Pilih file Excel dulu.',
            'file.mimes'      => 'File harus berformat .xlsx atau .xls.',
            'file.max'        => 'Ukuran file maksimal 5 MB.',
            'paket_id.exists' => 'Paket tujuan tidak ditemukan.',
            'status_awal.*'   => 'Pilih status awal soal.',
            'sumber.*'        => 'Pilih asal soal.',
        ]);

        $paket  = $request->filled('paket_id') ? GrupSoal::find($request->input('paket_id')) : null;
        $import = new SoalImport($paket, $request->input('sumber'), $request->input('status_awal'));

        $kembali = redirect()->route('admin.soal.import', $paket ? ['paket' => $paket->id] : []);

        try {
            Excel::import($import, $request->file('file'));
            $import->selesai();
        } catch (\Throwable $e) {
            report($e);

            return $kembali->with('error', 'File tidak bisa dibaca: ' . $e->getMessage());
        }

        if (! $import->adaIsi()) {
            return $kembali->with('error', 'Tidak ada baris data yang terbaca. Pastikan nama sheet (Soal, Paket, Bacaan) dan judul kolom di baris 1 sama persis dengan template.');
        }

        return $kembali->with('hasil', $import->ringkasan());
    }

    public function template()
    {
        return Excel::download(new SoalTemplateExport, 'template-soal.xlsx');
    }

    /* ================================================================ HELPER: PAKET */

    private function dataFormPaket(): array
    {
        return [
            'levels'        => LevelTocfl::orderBy('tingkat')->get(),
            'grupKosakatas' => GrupKosakata::orderBy('nama')->get(['id', 'nama']),
        ];
    }

    private function validasiPaket(Request $request): array
    {
        $data = $request->validate([
            'judul'            => ['required', 'string', 'max:500'],
            'deskripsi'        => ['nullable', 'string', 'max:5000'],
            'jenis'            => ['required', Rule::in([GrupSoal::JENIS_LATIHAN, GrupSoal::JENIS_UJIAN])],
            'level_tocfl_id'   => ['nullable', 'integer', 'exists:level_tocfls,id'],
            'grup_kosakata_id' => ['nullable', 'integer', 'exists:grup_kosakatas,id'],
            'durasi_menit'     => ['nullable', 'integer', 'min:1', 'max:1440'],
            'nilai_lulus'      => ['nullable', 'integer', 'min:0', 'max:100'],
            'maks_percobaan'   => ['nullable', 'integer', 'min:1', 'max:255'],
        ], [], [
            'judul'            => 'judul',
            'level_tocfl_id'   => 'level TOCFL',
            'grup_kosakata_id' => 'grup kosakata',
            'durasi_menit'     => 'durasi',
            'nilai_lulus'      => 'nilai lulus',
            'maks_percobaan'   => 'maksimal percobaan',
        ]);

        $data['aktif'] = $request->boolean('aktif');

        // Nilai lulus hanya dipakai untuk ujian (lihat HasilUjian::selesaikan()).
        if ($data['jenis'] !== GrupSoal::JENIS_UJIAN) {
            $data['nilai_lulus'] = null;
        }

        return $data;
    }

    /* ================================================================ HELPER: SOAL */

    private function dataFormSoal(): array
    {
        return [
            'polaTataBahasas' => PolaTataBahasa::orderBy('level_tocfl_id')->orderBy('urutan')->get(['id', 'judul', 'level_tocfl_id']),
            'paragrafs'       => Paragraf::orderBy('id')->get(['id', 'judul']),
        ];
    }

    private function validasiSoal(Request $request): array
    {
        $data = $request->validate([
            'bagian'              => ['required', Rule::in(Soal::BAGIAN)],
            'tipe'                => ['nullable', Rule::in(array_keys(LabelSoal::TIPE))],
            'tingkat'             => ['required', 'integer', 'between:1,6'],
            'poin'                => ['required', 'integer', 'between:1,255'],
            'urutan'              => ['required', 'integer', 'between:0,65535'],
            'status_validasi'     => ['required', Rule::in(array_keys(LabelSoal::STATUS))],
            'catatan_validasi'    => ['nullable', 'string', 'max:2000'],

            'paragraf_id'         => ['nullable', 'integer', 'exists:paragrafs,id'],
            'paragraf'            => ['nullable', 'string'],
            'audio'               => ['nullable', 'string', 'max:1000'],

            'pertanyaan'          => ['required', 'string'],
            'pilihan_a'           => ['required', 'string', 'max:1000'],
            'pilihan_b'           => ['required', 'string', 'max:1000'],
            'pilihan_c'           => ['required', 'string', 'max:1000'],
            'pilihan_d'           => ['required', 'string', 'max:1000'],
            'jawaban_benar'       => ['required', Rule::in(['A', 'B', 'C', 'D'])],
            'penjelasan'          => ['nullable', 'string'],

            'kosakata_id'         => ['nullable', 'integer', 'exists:kosakatas,id'],
            'pola_tata_bahasa_id' => ['nullable', 'integer', 'exists:pola_tata_bahasas,id'],
            'contoh_kalimat_id'   => ['nullable', 'integer', 'exists:contoh_kalimats,id'],
            'karakter_id'         => ['nullable', 'integer', 'exists:karakters,id'],
            'radikal_id'          => ['nullable', 'integer', 'exists:radikals,id'],
        ], [
            'exists' => 'ID :attribute tidak ditemukan.',
        ], [
            'pertanyaan'          => 'pertanyaan',
            'pilihan_a'           => 'pilihan A',
            'pilihan_b'           => 'pilihan B',
            'pilihan_c'           => 'pilihan C',
            'pilihan_d'           => 'pilihan D',
            'jawaban_benar'       => 'jawaban benar',
            'kosakata_id'         => 'kosakata',
            'pola_tata_bahasa_id' => 'pola tata bahasa',
            'contoh_kalimat_id'   => 'contoh kalimat',
            'karakter_id'         => 'karakter',
            'radikal_id'          => 'radikal',
            'paragraf_id'         => 'paragraf',
        ]);

        $this->periksaIsiSoal($data);

        return $data;
    }

    /** Pemeriksaan yang tidak bisa dinyatakan lewat rule biasa. */
    private function periksaIsiSoal(array $data): void
    {
        $galat = [];

        // Empat pilihan harus berbeda, kalau tidak soalnya ambigu.
        $pilihan = collect(['pilihan_a', 'pilihan_b', 'pilihan_c', 'pilihan_d'])
            ->map(fn ($k) => mb_strtolower(trim($data[$k])));
        if ($pilihan->unique()->count() < 4) {
            $galat['pilihan_a'] = 'Keempat pilihan harus berbeda satu sama lain.';
        }

        // Hafalan hanya ikut diperbarui kalau soal terhubung ke itemnya (lihat Soal::jenisItemReview()).
        $tipe = $data['tipe'] ?? null;
        if (in_array($tipe, Soal::TIPE_KOSAKATA, true) && empty($data['kosakata_id'])) {
            $galat['kosakata_id'] = 'Tipe soal ini wajib terhubung ke sebuah kosakata supaya hafalannya ikut diperbarui.';
        }
        if (in_array($tipe, self::TIPE_BUTUH_KARAKTER, true) && empty($data['karakter_id'])) {
            $galat['karakter_id'] = 'Tipe soal ini wajib terhubung ke sebuah karakter supaya hafalannya ikut diperbarui.';
        }
        if ($tipe === 'radikal_ke_arti' && empty($data['radikal_id'])) {
            $galat['radikal_id'] = 'Tipe soal ini wajib terhubung ke sebuah radikal supaya hafalannya ikut diperbarui.';
        }

        if ($galat) {
            throw ValidationException::withMessages($galat);
        }
    }

    /** dicek_pada diisi saat soal baru disetujui / ditolak, atau statusnya berubah ke salah satunya. */
    private function isiDicekPada(array &$data, ?Soal $lama): void
    {
        $berubah = $lama === null || $lama->status_validasi !== $data['status_validasi'];
        $final   = in_array($data['status_validasi'], [Soal::STATUS_DISETUJUI, Soal::STATUS_DITOLAK], true);

        if ($berubah && $final) {
            $data['dicek_pada'] = now();
        }
    }
}
