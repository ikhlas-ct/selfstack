<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GrupSoal;
use App\Models\Paragraf;
use App\Models\PolaTataBahasa;
use App\Models\Soal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * CRUD soal pilihan ganda di dalam sebuah paket (GrupSoal).
 * Daftar soal tampil di halaman detail paket, jadi tidak ada index/show di sini.
 */
class SoalController extends Controller
{
    /** Tipe karakter yang wajib terhubung ke karakter. */
    private const TIPE_BUTUH_KARAKTER = [
        'karakter_ke_arti', 'arti_ke_karakter', 'karakter_ke_radikal', 'arti_karakter_dalam_kata',
    ];

    // ------------------------------------------------------------ tambah

    public function create(GrupSoal $grupSoal)
    {
        $soal = new Soal([
            'bagian'          => Soal::BAGIAN_KOSAKATA,
            'tingkat'         => $grupSoal->levelTocfl?->tingkat ?? 1,
            'poin'            => 1,
            // Tanpa orderBy relasi: max() dengan ORDER BY bisa ditolak MySQL strict.
            'urutan'          => ((int) Soal::where('grup_soal_id', $grupSoal->id)->max('urutan')) + 1,
            'status_validasi' => Soal::STATUS_DISETUJUI,
        ]);

        return view('pages.soal.create', array_merge(compact('grupSoal', 'soal'), $this->dataForm()));
    }

    public function store(Request $request, GrupSoal $grupSoal): RedirectResponse
    {
        $data = $this->validasi($request);

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

    // ------------------------------------------------------------ ubah

    public function edit(Soal $soal)
    {
        $soal->load(['grupSoal', 'kosakata', 'contohKalimat', 'karakter', 'radikal']);
        $grupSoal = $soal->grupSoal;

        return view('pages.soal.edit', array_merge(compact('grupSoal', 'soal'), $this->dataForm()));
    }

    public function update(Request $request, Soal $soal): RedirectResponse
    {
        $data = $this->validasi($request);
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

        return back()->with('sukses', 'Status soal diubah menjadi "' . \App\Support\LabelSoal::STATUS[$data['status_validasi']] . '".');
    }

    // ------------------------------------------------------------ hapus

    /** Jawaban di riwayat ujian & laporan untuk soal ini ikut terhapus (cascade di database). */
    public function destroy(Soal $soal): RedirectResponse
    {
        $idPaket = $soal->grup_soal_id;
        $soal->delete();

        return redirect()
            ->route('admin.grup-soal.show', $idPaket)
            ->with('sukses', 'Soal dihapus.');
    }

    // ------------------------------------------------------------ helper

    private function dataForm(): array
    {
        return [
            'polaTataBahasas' => PolaTataBahasa::orderBy('level_tocfl_id')->orderBy('urutan')->get(['id', 'judul', 'level_tocfl_id']),
            'paragrafs'       => Paragraf::orderBy('id')->get(['id', 'judul']),
        ];
    }

    private function validasi(Request $request): array
    {
        $tipeValid = array_keys(\App\Support\LabelSoal::TIPE);

        $data = $request->validate([
            'bagian'              => ['required', Rule::in(Soal::BAGIAN)],
            'tipe'                => ['nullable', Rule::in($tipeValid)],
            'tingkat'             => ['required', 'integer', 'between:1,6'],
            'poin'                => ['required', 'integer', 'between:1,255'],
            'urutan'              => ['required', 'integer', 'between:0,65535'],
            'status_validasi'     => ['required', Rule::in(array_keys(\App\Support\LabelSoal::STATUS))],
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

        $this->periksaIsi($data);

        return $data;
    }

    /** Pemeriksaan yang tidak bisa dinyatakan lewat rule biasa. */
    private function periksaIsi(array $data): void
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
