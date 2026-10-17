<?php

namespace App\Http\Controllers\Admin;

use App\Exports\KosakataTemplateExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\KosakataRequest;
use App\Imports\KosakataImport;
use App\Models\Kategori;
use App\Models\Kosakata;
use App\Models\KosakataRelasi;
use App\Models\LevelTocfl;
use App\Models\Soal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class KosakataController extends Controller
{
    private const PER_HALAMAN = 20;

    /* ================================================================ INDEX */

    public function index(Request $request): View|string
    {
        $kosakatas = Kosakata::query()
            ->with(['kategori:id,nama', 'levelTocfl:id,nama,tingkat'])
            ->withCount(['contohKalimats', 'soals'])
            ->cari($request->input('search'))
            ->when($request->filled('kategori_id'), fn ($q) => $this->filterKolom($q, 'kategori_id', $request->input('kategori_id')))
            ->when($request->filled('level_tocfl_id'), fn ($q) => $this->filterKolom($q, 'level_tocfl_id', $request->input('level_tocfl_id')))
            ->when($request->filled('jenis_kata'), fn ($q) => $this->filterKolom($q, 'jenis_kata', $request->input('jenis_kata')))
            ->when($request->filled('lengkap'), fn ($q) => $this->filterKelengkapan($q, $request->input('lengkap')))
            ->urut() // Level 1 -> 6 (tanpa level di akhir), lalu urutan, lalu id
            ->paginate(self::PER_HALAMAN)
            ->withQueryString();

        // Request AJAX (cari / filter / pagination): kirim potongan tabel saja
        // (@fragment('tabel') di index.blade.php). Statistik & dropdown tidak dihitung.
        if ($request->ajax()) {
            return view('pages.kosakata.index', [
                'kosakatas' => $kosakatas,
                'stats'     => ['total' => 0, 'dengan_contoh' => 0, 'tanpa_contoh' => 0, 'tanpa_arti' => 0],
                'kategoris' => collect(),
                'levels'    => collect(),
                'jenisKata' => [],
            ])->fragment('tabel');
        }

        // Statistik: satu query (sebelumnya empat query count terpisah).
        $s = Kosakata::query()->selectRaw("
            COUNT(*) AS total,
            COALESCE(SUM(EXISTS(SELECT 1 FROM contoh_kalimats c WHERE c.kosakata_id = kosakatas.id)), 0) AS dengan_contoh,
            COALESCE(SUM(arti_indonesia IS NULL OR arti_indonesia = ''), 0) AS tanpa_arti
        ")->first();

        $stats = [
            'total'         => (int) $s->total,
            'dengan_contoh' => (int) $s->dengan_contoh,
            'tanpa_contoh'  => (int) $s->total - (int) $s->dengan_contoh,
            'tanpa_arti'    => (int) $s->tanpa_arti,
        ];

        return view('pages.kosakata.index', [
            'kosakatas' => $kosakatas,
            'stats'     => $stats,
            'kategoris' => Kategori::orderBy('nama')->get(['id', 'nama']),
            'levels'    => LevelTocfl::orderBy('tingkat')->get(['id', 'nama', 'tingkat']),
            'jenisKata' => Kosakata::JENIS_KATA,
        ]);
    }

    /** Nilai "kosong" berarti cari yang kolomnya belum diisi. */
    private function filterKolom(Builder $query, string $kolom, string $nilai): Builder
    {
        return $nilai === 'kosong'
            ? $query->whereNull($kolom)
            : $query->where($kolom, $nilai);
    }

    private function filterKelengkapan(Builder $query, string $pilihan): Builder
    {
        return match ($pilihan) {
            'ada_contoh'    => $query->has('contohKalimats'),
            'tanpa_contoh'  => $query->doesntHave('contohKalimats'),
            'tanpa_arti'    => $this->tanpaArti($query),
            'tanpa_pecahan' => $query->doesntHave('pecahan'),
            default         => $query,
        };
    }

    private function tanpaArti(Builder $query): Builder
    {
        return $query->where(fn ($q) => $q->whereNull('arti_indonesia')->orWhere('arti_indonesia', ''));
    }

    /* ================================================================ CREATE */

    public function create(): View
    {
        return view('pages.kosakata.create', $this->dataForm());
    }

    public function store(KosakataRequest $request): RedirectResponse
    {
        $data   = $request->validated();
        $contoh = $data['contoh_kalimat'] ?? [];
        unset($data['contoh_kalimat']);

        $kosakata = DB::transaction(function () use ($data, $contoh) {
            // urutan di database NOT NULL; kosong = taruh di akhir.
            $data['urutan'] = $data['urutan'] ?? ((int) Kosakata::max('urutan') + 1);

            $kosakata = Kosakata::create($data);

            foreach ($contoh as $baris) {
                $kosakata->contohKalimats()->create($this->isiContoh($baris));
            }

            return $kosakata;
        });

        return redirect()
            ->route('admin.kosakata.index')
            ->with('success', "Kosakata \"{$kosakata->hanzi}\" berhasil ditambahkan.");
    }

    /* ================================================================ SHOW */

    public function show(Kosakata $kosakata): View
    {
        $kosakata->load([
            'kategori',
            'levelTocfl',
            'contohKalimats' => fn ($q) => $q->orderBy('id'),
            'pecahan.karakter',
            'grupKosakatas',
        ]);

        // Relasi tidak otomatis dua arah, jadi baca dari kedua sisi.
        $relasi = $kosakata->relasis()->with('terkait')->get()
            ->map(fn ($r) => ['jenis' => $r->jenis, 'kata' => $r->terkait, 'masuk' => false])
            ->concat(
                KosakataRelasi::where('kosakata_terkait_id', $kosakata->id)->with('kosakata')->get()
                    ->map(fn ($r) => ['jenis' => $r->jenis, 'kata' => $r->kosakata, 'masuk' => true])
            )
            ->filter(fn ($r) => $r['kata'] !== null)
            ->values();

        return view('pages.kosakata.show', compact('kosakata', 'relasi'));
    }

    /* ================================================================ EDIT */

    public function edit(Kosakata $kosakata): View
    {
        $kosakata->load(['contohKalimats' => fn ($q) => $q->orderBy('id')])
            ->loadCount('pecahan');

        return view('pages.kosakata.edit', $this->dataForm() + ['kosakata' => $kosakata]);
    }

    public function update(KosakataRequest $request, Kosakata $kosakata): RedirectResponse
    {
        $data   = $request->validated();
        $contoh = $data['contoh_kalimat'] ?? [];
        unset($data['contoh_kalimat']);

        DB::transaction(function () use ($kosakata, $data, $contoh) {
            // Urutan dikosongkan di form = pakai urutan yang lama (kolomnya NOT NULL).
            $data['urutan'] = $data['urutan'] ?? $kosakata->urutan;

            $kosakata->update($data);
            $this->sinkronContoh($kosakata, $contoh);
        });

        return redirect()
            ->route('admin.kosakata.index')
            ->with('success', "Kosakata \"{$kosakata->hanzi}\" berhasil diperbarui.");
    }

    /* ================================================================ DESTROY */

    /**
     * Yang ikut terhapus otomatis (FK CASCADE): contoh kalimat, pecahan karakter,
     * relasi kata, keanggotaan grup, progres hafalan.
     * Yang TIDAK terhapus (FK SET NULL): soal dan riwayat review. Soalnya sengaja
     * ditolak di sini supaya tidak beredar tanpa kosakata asalnya.
     */
    public function destroy(Kosakata $kosakata): RedirectResponse
    {
        $hanzi = $kosakata->hanzi;

        try {
            DB::transaction(function () use ($kosakata, $hanzi) {
                $kosakata->soals()
                    ->where('status_validasi', '!=', Soal::STATUS_DITOLAK)
                    ->update([
                        'status_validasi'  => Soal::STATUS_DITOLAK,
                        'catatan_validasi' => "Kosakata sumber ({$hanzi}) sudah dihapus.",
                        'dicek_pada'       => now(),
                    ]);

                $kosakata->delete();
            });
        } catch (QueryException $e) {
            report($e);

            return redirect()
                ->route('admin.kosakata.index')
                ->with('error', "Kosakata \"{$hanzi}\" tidak bisa dihapus karena masih dipakai data lain.");
        }

        return redirect()
            ->route('admin.kosakata.index')
            ->with('success', "Kosakata \"{$hanzi}\" berhasil dihapus.");
    }

    /* ================================================================ IMPORT */

    public function import(): View
    {
        return view('pages.kosakata.import', [
            'levels'      => LevelTocfl::orderBy('tingkat')->get(),
            'jenisKata'   => Kosakata::JENIS_KATA,
            'subJenis'    => Kosakata::SUB_JENIS,
            'jenisRelasi' => KosakataRelasi::JENIS,
        ]);
    }

    public function importStore(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
            'mode' => ['required', Rule::in(['perbarui', 'lewati'])],
        ], [
            'file.required' => 'Pilih file Excel dulu.',
            'file.mimes'    => 'File harus berformat .xlsx atau .xls.',
            'file.max'      => 'Ukuran file maksimal 5 MB.',
            'mode.*'        => 'Pilih salah satu mode import.',
        ]);

        $import = new KosakataImport($request->input('mode') === 'perbarui');

        try {
            Excel::import($import, $request->file('file'));
            $import->selesai();
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('admin.kosakata.import')
                ->with('error', 'File tidak bisa dibaca: ' . $e->getMessage());
        }

        if (! $import->adaIsi()) {
            return redirect()
                ->route('admin.kosakata.import')
                ->with('error', 'Tidak ada baris data yang terbaca. Pastikan nama sheet dan judul kolom di baris 1 sama persis dengan template.');
        }

        return redirect()
            ->route('admin.kosakata.import')
            ->with('hasil', $import->ringkasan());
    }

    public function template()
    {
        return Excel::download(new KosakataTemplateExport, 'template-kosakata.xlsx');
    }

    /* ================================================================ HELPER */

    private function dataForm(): array
    {
        return [
            'kategoris' => Kategori::orderBy('nama')->get(),
            'levels'    => LevelTocfl::orderBy('tingkat')->get(),
            'jenisKata' => Kosakata::JENIS_KATA,
            'subJenis'  => Kosakata::SUB_JENIS,
        ];
    }

    private function isiContoh(array $baris): array
    {
        return [
            'hanzi'               => $baris['hanzi'],
            'pinyin'              => $baris['pinyin'],
            'arti_indonesia'      => $baris['arti_indonesia'],
            'catatan_tata_bahasa' => $baris['catatan_tata_bahasa'] ?? null,
        ];
    }

    /**
     * Samakan contoh kalimat di database dengan isi form:
     * baris ber-id diperbarui, baris tanpa id dibuat baru, baris yang hilang dari form dihapus.
     */
    private function sinkronContoh(Kosakata $kosakata, array $contoh): void
    {
        $ada           = $kosakata->contohKalimats()->get()->keyBy('id');
        $dipertahankan = [];
        $perluDicek    = []; // contoh yang berubah / dihapus, soal turunannya perlu diperiksa ulang

        foreach ($contoh as $baris) {
            // id dari form hanya dipercaya kalau memang milik kosakata ini.
            $model = isset($baris['id']) ? $ada->get((int) $baris['id']) : null;

            if ($model) {
                $model->fill($this->isiContoh($baris));

                // Kalimatnya berubah: pemecahan kata, kata kunci, dan pengecoh lama
                // sudah tidak cocok lagi, jadi dikosongkan supaya dibuat/diverifikasi ulang.
                if ($model->isDirty('hanzi')) {
                    $model->fill([
                        'susunan'             => null,
                        'pengecoh'            => null,
                        'kunci_alternatif'    => null,
                        'pengecoh_dicek_pada' => null,
                    ]);
                    $perluDicek[] = $model->id;
                }

                $model->save();
                $dipertahankan[] = $model->id;
                continue;
            }

            $dipertahankan[] = $kosakata->contohKalimats()->create($this->isiContoh($baris))->id;
        }

        $dihapus    = $ada->keys()->diff($dipertahankan)->values()->all();
        $perluDicek = array_merge($perluDicek, $dihapus);

        // Harus sebelum baris contoh dihapus: soals.contoh_kalimat_id akan jadi NULL (FK SET NULL).
        if ($perluDicek) {
            Soal::whereIn('contoh_kalimat_id', $perluDicek)
                ->where('status_validasi', '!=', Soal::STATUS_DITOLAK)
                ->update([
                    'status_validasi'  => Soal::STATUS_PERLU_DICEK,
                    'catatan_validasi' => 'Contoh kalimat sumber diubah atau dihapus. Periksa ulang soal ini.',
                ]);
        }

        if ($dihapus) {
            $kosakata->contohKalimats()->whereIn('id', $dihapus)->delete();
        }
    }
}
