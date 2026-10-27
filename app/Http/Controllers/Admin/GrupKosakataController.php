<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GrupKosakata;
use App\Models\Kosakata;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * CRUD grup kosakata (satu grup = satu buku) dan pengelolaan anggotanya.
 * Tiap anggota punya nomor pelajaran di tabel pivot grup_kosakata_kosakata.pelajaran.
 */
class GrupKosakataController extends Controller
{
    private const PIVOT = 'grup_kosakata_kosakata';

    private const PESAN = [
        'required' => 'Kolom :attribute wajib diisi.',
        'max'      => 'Kolom :attribute terlalu panjang (maks. :max karakter).',
        'unique'   => 'Sudah ada grup dengan nama ini.',
        'integer'  => 'Kolom :attribute harus berupa angka bulat.',
        'min'      => 'Kolom :attribute tidak boleh kurang dari :min.',
    ];

    /* ------------------------------------------------------------ CRUD grup */

    public function index(Request $request)
    {
        $cari = trim((string) $request->query('search', ''));
        $isi  = $request->query('isi');

        // Jumlah pelajaran berbeda di tiap grup (pelajaran NULL tidak dihitung oleh COUNT DISTINCT).
        $jumlahPelajaran = DB::table(self::PIVOT)
            ->selectRaw('COUNT(DISTINCT pelajaran)')
            ->whereColumn(self::PIVOT . '.grup_kosakata_id', 'grup_kosakatas.id');

        $grups = GrupKosakata::query()
            ->select('grup_kosakatas.*')
            ->selectSub($jumlahPelajaran, 'jumlah_pelajaran')
            ->withCount(['kosakatas', 'grupSoals'])
            ->when($cari !== '', function ($q) use ($cari) {
                $q->where(fn ($w) => $w->where('nama', 'like', "%{$cari}%")
                    ->orWhere('keterangan', 'like', "%{$cari}%"));
            })
            ->when($isi === 'kosong', fn ($q) => $q->doesntHave('kosakatas'))
            ->when($isi === 'berisi', fn ($q) => $q->has('kosakatas'))
            ->orderBy('nama')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total'              => GrupKosakata::count(),
            'kosakata_tergabung' => DB::table(self::PIVOT)->distinct()->count('kosakata_id'),
            'kosong'             => GrupKosakata::doesntHave('kosakatas')->count(),
            'tanpa_pelajaran'    => DB::table(self::PIVOT)->whereNull('pelajaran')->count(),
        ];

        $view = view('pages.grup-kosakata.index', compact('grups', 'stats'));

        // Pencarian / pagination lewat AJAX hanya butuh potongan tabel.
        return $request->ajax() ? $view->fragment('tabel') : $view;
    }

    public function create()
    {
        return view('pages.grup-kosakata.create');
    }

    public function store(Request $request)
    {
        $data = $this->validasiGrup($request);

        $grup = GrupKosakata::create($data);

        return redirect()
            ->route('admin.grup-kosakata.show', $grup)
            ->with('success', "Grup \"{$grup->nama}\" dibuat. Sekarang tambahkan kosakatanya.");
    }

    public function show(Request $request, GrupKosakata $grupKosakata)
    {
        $grup   = $grupKosakata;
        $filter = $request->query('pelajaran');

        // Ringkasan jumlah kata per pelajaran (untuk tab), pelajaran kosong di akhir.
        $ringkasan = DB::table(self::PIVOT)
            ->where('grup_kosakata_id', $grup->id)
            ->select('pelajaran', DB::raw('COUNT(*) as jumlah'))
            ->groupBy('pelajaran')
            ->orderByRaw('pelajaran IS NULL')
            ->orderBy('pelajaran')
            ->get();

        $totalKata = (int) $ringkasan->sum('jumlah');

        $anggota = $grup->kosakatas()
            ->with('levelTocfl')
            ->withCount('contohKalimats')
            ->when($filter === 'kosong', fn ($q) => $q->wherePivotNull('pelajaran'))
            ->when(is_numeric($filter), fn ($q) => $q->wherePivot('pelajaran', (int) $filter))
            ->orderByRaw(self::PIVOT . '.pelajaran IS NULL')
            ->orderBy(self::PIVOT . '.pelajaran')
            ->orderBy('kosakatas.urutan')
            ->orderBy('kosakatas.id')
            ->paginate(100)
            ->withQueryString();

        // Nomor pelajaran yang sedang dibuka; kalau tidak ada, pelajaran terakhir yang dipakai.
        $pelajaranBawaan = is_numeric($filter)
            ? (int) $filter
            : max(1, (int) $ringkasan->whereNotNull('pelajaran')->max('pelajaran'));

        $jumlahPaketSoal = $grup->grupSoals()->count();

        $view = view('pages.grup-kosakata.show', compact(
            'grup', 'anggota', 'ringkasan', 'totalKata', 'filter', 'pelajaranBawaan', 'jumlahPaketSoal'
        ));

        // Setelah tambah / pindah / keluarkan, JS hanya memuat ulang bagian daftar anggota.
        return $request->ajax() ? $view->fragment('anggota') : $view;
    }

    public function edit(GrupKosakata $grupKosakata)
    {
        $grupKosakata->loadCount(['kosakatas', 'grupSoals']);

        return view('pages.grup-kosakata.edit', ['grup' => $grupKosakata]);
    }

    public function update(Request $request, GrupKosakata $grupKosakata)
    {
        $grupKosakata->update($this->validasiGrup($request, $grupKosakata));

        return redirect()
            ->route('admin.grup-kosakata.show', $grupKosakata)
            ->with('success', 'Grup kosakata berhasil diperbarui.');
    }

    public function destroy(GrupKosakata $grupKosakata)
    {
        $nama = $grupKosakata->nama;

        // Keanggotaan ikut terhapus (cascade); kosakatanya sendiri tidak.
        // Paket soal yang memakai grup ini tidak terhapus, hanya grup_kosakata_id-nya jadi NULL.
        $grupKosakata->delete();

        return redirect()
            ->route('admin.grup-kosakata.index')
            ->with('success', "Grup \"{$nama}\" dihapus. Kosakata di dalamnya tetap ada.");
    }

    /* ------------------------------------------------------ anggota (AJAX/JSON) */

    /** Cari kosakata untuk ditambahkan; menandai yang sudah jadi anggota grup ini. */
    public function cari(Request $request, GrupKosakata $grupKosakata): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));

        if ($q === '') {
            return response()->json([]);
        }

        $hasil = Kosakata::cari($q)->urut()->with('levelTocfl')->limit(30)->get();

        // kosakata_id => pelajaran (null kalau belum ditentukan) untuk yang sudah jadi anggota.
        $sudah = DB::table(self::PIVOT)
            ->where('grup_kosakata_id', $grupKosakata->id)
            ->whereIn('kosakata_id', $hasil->pluck('id'))
            ->pluck('pelajaran', 'kosakata_id');

        return response()->json($hasil->map(fn (Kosakata $k) => [
            'id'        => $k->id,
            'hanzi'     => $k->hanzi,
            'pinyin'    => $k->pinyin,
            'arti'      => $k->arti_pilihan ?: $k->english,
            'level'     => $k->levelTocfl?->nama,
            'sudah'     => $sudah->has($k->id),
            'pelajaran' => $sudah->get($k->id),
        ])->values());
    }

    /** Tambah satu atau beberapa kosakata ke grup pada pelajaran tertentu. */
    public function tambahKosakata(Request $request, GrupKosakata $grupKosakata): JsonResponse
    {
        $data = $request->validate([
            'kosakata_ids'   => ['required', 'array', 'min:1', 'max:200'],
            'kosakata_ids.*' => ['integer', Rule::exists('kosakatas', 'id')],
            'pelajaran'      => ['nullable', 'integer', 'min:1', 'max:999'],
        ], self::PESAN);

        $ids       = array_values(array_unique($data['kosakata_ids']));
        $pelajaran = $data['pelajaran'] ?? null;

        [$ditambah, $dilewati] = $this->masukkan($grupKosakata, $ids, $pelajaran);

        return response()->json([
            'pesan' => $this->pesanTambah($ditambah, $dilewati, $pelajaran),
        ]);
    }

    /**
     * Tempel daftar hanzi (satu per baris, atau dipisah spasi / koma) lalu masukkan sekaligus.
     * Hanzi yang cocok dengan lebih dari satu kosakata (pinyin berbeda) tidak dimasukkan,
     * supaya tidak salah pilih; lewat pencarian saja untuk yang itu.
     */
    public function tambahMassal(Request $request, GrupKosakata $grupKosakata): JsonResponse
    {
        $data = $request->validate([
            'daftar'    => ['required', 'string', 'max:20000'],
            'pelajaran' => ['nullable', 'integer', 'min:1', 'max:999'],
        ], self::PESAN);

        $pelajaran = $data['pelajaran'] ?? null;

        $token = collect(preg_split('/[\s,，、;；]+/u', $data['daftar'], -1, PREG_SPLIT_NO_EMPTY))
            ->map(fn ($t) => trim($t))
            ->filter()
            ->unique()
            ->values();

        if ($token->count() > 300) {
            return response()->json(['message' => 'Maksimal 300 kata per tempel.'], 422);
        }

        // hanzi dibandingkan persis (kolom utf8mb4_bin).
        $ditemukan = Kosakata::whereIn('hanzi', $token)->get(['id', 'hanzi', 'pinyin'])->groupBy('hanzi');

        $ids = [];
        $tidakAda = [];
        $ambigu = [];

        foreach ($token as $hanzi) {
            $cocok = $ditemukan->get($hanzi);

            if (! $cocok) {
                $tidakAda[] = $hanzi;
            } elseif ($cocok->count() > 1) {
                $ambigu[] = $hanzi . ' (' . $cocok->pluck('pinyin')->implode(' / ') . ')';
            } else {
                $ids[] = $cocok->first()->id;
            }
        }

        [$ditambah, $dilewati] = $this->masukkan($grupKosakata, $ids, $pelajaran);

        return response()->json([
            'pesan'       => $this->pesanTambah($ditambah, $dilewati, $pelajaran),
            'tidak_ada'   => $tidakAda,
            'ambigu'      => $ambigu,
        ]);
    }

    /** Pindahkan beberapa anggota ke pelajaran lain (kosong = belum ditentukan). */
    public function pindahPelajaran(Request $request, GrupKosakata $grupKosakata): JsonResponse
    {
        $data = $request->validate([
            'kosakata_ids'   => ['required', 'array', 'min:1', 'max:500'],
            'kosakata_ids.*' => ['integer'],
            'pelajaran'      => ['nullable', 'integer', 'min:1', 'max:999'],
        ], self::PESAN);

        $jumlah = DB::table(self::PIVOT)
            ->where('grup_kosakata_id', $grupKosakata->id)
            ->whereIn('kosakata_id', $data['kosakata_ids'])
            ->update(['pelajaran' => $data['pelajaran'] ?? null]);

        $tujuan = isset($data['pelajaran']) ? "pelajaran {$data['pelajaran']}" : 'tanpa pelajaran';

        return response()->json(['pesan' => "{$jumlah} kosakata dipindahkan ke {$tujuan}."]);
    }

    /** Keluarkan anggota dari grup (kosakatanya sendiri tidak dihapus). */
    public function lepasKosakata(Request $request, GrupKosakata $grupKosakata): JsonResponse
    {
        $data = $request->validate([
            'kosakata_ids'   => ['required', 'array', 'min:1', 'max:500'],
            'kosakata_ids.*' => ['integer'],
        ], self::PESAN);

        $jumlah = $grupKosakata->kosakatas()->detach($data['kosakata_ids']);

        return response()->json(['pesan' => "{$jumlah} kosakata dikeluarkan dari grup."]);
    }

    /* ---------------------------------------------------------------- helper */

    private function validasiGrup(Request $request, ?GrupKosakata $grup = null): array
    {
        $request->merge(['nama' => trim((string) $request->input('nama'))]);

        return $request->validate([
            'nama'       => ['required', 'string', 'max:255', Rule::unique('grup_kosakatas', 'nama')->ignore($grup?->id)],
            'keterangan' => ['nullable', 'string', 'max:2000'],
        ], self::PESAN);
    }

    /**
     * Masukkan kosakata yang belum jadi anggota. Yang sudah ada dilewati
     * (tidak dipindah pelajarannya; untuk memindah pakai pindahPelajaran()).
     *
     * @return array{0:int,1:int} [jumlah ditambah, jumlah dilewati]
     */
    private function masukkan(GrupKosakata $grup, array $ids, ?int $pelajaran): array
    {
        if (empty($ids)) {
            return [0, 0];
        }

        $sudah = DB::table(self::PIVOT)
            ->where('grup_kosakata_id', $grup->id)
            ->whereIn('kosakata_id', $ids)
            ->pluck('kosakata_id')
            ->all();

        $baru = array_values(array_diff($ids, $sudah));

        if ($baru) {
            $grup->kosakatas()->attach(
                collect($baru)->mapWithKeys(fn ($id) => [$id => ['pelajaran' => $pelajaran]])->all()
            );
        }

        return [count($baru), count($sudah)];
    }

    private function pesanTambah(int $ditambah, int $dilewati, ?int $pelajaran): string
    {
        $tujuan = $pelajaran !== null ? "pelajaran {$pelajaran}" : 'grup (tanpa pelajaran)';
        $pesan  = "{$ditambah} kosakata ditambahkan ke {$tujuan}.";

        if ($dilewati > 0) {
            $pesan .= " {$dilewati} dilewati karena sudah ada di grup ini.";
        }

        return $pesan;
    }
}
