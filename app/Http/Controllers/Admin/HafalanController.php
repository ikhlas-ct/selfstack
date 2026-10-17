<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Kosakata;
use App\Models\LevelTocfl;
use App\Models\ProgresHafalan;
use App\Models\RiwayatReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

/**
 * Progres hafalan kosakata milik admin (aplikasi ini satu pengguna,
 * jadi tabel progres_hafalans tidak punya user_id).
 *
 * Tampilan: grid kartu kata + popup detail (sama seperti halaman Belajar Kosakata).
 * Kata yang belum punya baris progres dihitung & ditampilkan sebagai "Berikutnya".
 */
class HafalanController extends Controller
{
    /** Jumlah kartu yang ditampilkan sekali muat (tanpa pagination). */
    private const BATAS_TAMPIL = 50;

    public function index(Request $request)
    {
        $cari     = trim((string) $request->query('search', ''));
        $level    = $request->filled('level') ? (int) $request->query('level') : null;
        $kategori = $request->filled('kategori') ? (int) $request->query('kategori') : null;
        $status   = $request->query('status');
        if (! in_array($status, ProgresHafalan::STATUS, true)) {
            $status = null;
        }

        // ---------- jumlah di pil status (selalu total keseluruhan) ----------
        $totalKosakata = Kosakata::count();
        $perStatus     = ProgresHafalan::selectRaw('status, COUNT(*) as jml')->groupBy('status')->pluck('jml', 'status');

        $ingat      = (int) ($perStatus[ProgresHafalan::STATUS_INGAT_SEPENUHNYA] ?? 0);
        $lupa       = (int) ($perStatus[ProgresHafalan::STATUS_LUPA_DAN_INGAT] ?? 0);
        // Kata yang belum pernah disentuh ikut dihitung "Berikutnya".
        $berikutnya = max($totalKosakata - $ingat - $lupa, 0);

        // ---------- daftar kata ----------
        $query = Kosakata::query()
            ->cari($cari)
            ->when($level, fn ($q) => $q->where('level_tocfl_id', $level))
            ->when($kategori, fn ($q) => $q->where('kategori_id', $kategori))
            ->when($status === ProgresHafalan::STATUS_BERIKUTNYA, function ($q) {
                $q->where(function ($w) {
                    $w->doesntHave('progres')
                      ->orWhereHas('progres', fn ($p) => $p->where('status', ProgresHafalan::STATUS_BERIKUTNYA));
                });
            })
            ->when(
                $status && $status !== ProgresHafalan::STATUS_BERIKUTNYA,
                fn ($q) => $q->whereHas('progres', fn ($p) => $p->where('status', $status))
            );

        $totalCocok = (clone $query)->count();

        $kosakatas = $query
            ->with(['levelTocfl', 'kategori', 'progres'])
            // Saat mencari: yang paling mirip (persis, lalu diawali) tampil duluan.
            ->when($cari !== '', fn ($q) => $q->orderByRaw(
                'CASE WHEN hanzi = ? OR pinyin = ? THEN 0
                      WHEN hanzi LIKE ? OR pinyin LIKE ? OR arti_singkat = ? THEN 1
                      ELSE 2 END',
                [$cari, $cari, $cari . '%', $cari . '%', $cari]
            ))
            ->urut()
            ->limit(self::BATAS_TAMPIL)
            ->get();

        $levels    = LevelTocfl::orderBy('tingkat')->get();
        $kategoris = Kategori::orderBy('nama')->get();

        $view = view('pages.hafalan.index', compact(
            'kosakatas', 'levels', 'kategoris',
            'ingat', 'lupa', 'berikutnya', 'totalKosakata', 'totalCocok',
            'cari', 'level', 'kategori', 'status'
        ));

        // Permintaan AJAX hanya butuh bagian daftar kata.
        return $request->ajax() ? $view->fragment('daftar-kata') : $view;
    }

    /** JSON detail satu kata + contoh kalimat + riwayat review (untuk popup). */
    public function riwayat(Kosakata $kosakata): JsonResponse
    {
        $kosakata->load(['levelTocfl', 'kategori', 'progres', 'contohKalimats']);
        $p = $kosakata->progres;

        $kunciStatus = $p->status ?? ProgresHafalan::STATUS_BERIKUTNYA;

        $reviews = RiwayatReview::where('jenis_item', RiwayatReview::ITEM_KOSAKATA)
            ->where('kosakata_id', $kosakata->id)
            ->orderByDesc('direview_pada')
            ->limit(20)
            ->get();

        $total = RiwayatReview::where('jenis_item', RiwayatReview::ITEM_KOSAKATA)
            ->where('kosakata_id', $kosakata->id)->count();
        $benar = RiwayatReview::where('jenis_item', RiwayatReview::ITEM_KOSAKATA)
            ->where('kosakata_id', $kosakata->id)->where('benar', true)->count();

        return response()->json([
            'id'             => $kosakata->id,
            'hanzi'          => $kosakata->hanzi,
            'pinyin'         => $kosakata->pinyin,
            'arti'           => $kosakata->arti_indonesia ?: $kosakata->arti_singkat,
            'baca_indonesia' => $kosakata->baca_indonesia,
            'english'        => $kosakata->english,
            'kegunaan'       => $kosakata->kegunaan,
            'kategori'       => $kosakata->kategori?->nama,
            'level'          => $kosakata->levelTocfl ? 'TOCFL ' . $kosakata->levelTocfl->tingkat : null,
            'status'         => [
                'kunci' => $kunciStatus,
                'label' => ProgresHafalan::LABEL[$kunciStatus] ?? $kunciStatus,
            ],

            'contoh' => $kosakata->contohKalimats->sortBy('id')->map(fn ($c) => [
                'hanzi'   => $c->hanzi,
                'pinyin'  => $c->pinyin,
                'arti'    => $c->arti_indonesia,
                'catatan' => $c->catatan_tata_bahasa,
            ])->values(),

            'total'    => $total,
            'akurasi'  => $total > 0 ? round($benar / $total * 100) : null,
            'beruntun' => $p->benar_beruntun ?? 0,
            'review_berikutnya' => $p?->review_berikutnya ? [
                'tanggal' => $p->review_berikutnya->translatedFormat('d M Y'),
                'lewat'   => $p->review_berikutnya->isPast(),
            ] : null,
            'riwayat' => $reviews->map(fn ($r) => [
                'waktu'   => $r->direview_pada->translatedFormat('d M Y, H:i'),
                'relatif' => $r->direview_pada->diffForHumans(),
                'sumber'  => $r->sumber,
                'benar'   => (bool) $r->benar,
                'status_saat_review' => ProgresHafalan::LABEL[$r->status_sebelum] ?? $r->status_sebelum,
            ])->values(),

            'url_status' => route('admin.hafalan.status', $kosakata->id),
            // Tombol "Buka halaman detail" hanya muncul kalau route-nya ada.
            'url_detail' => Route::has('admin.kosakata.show') ? route('admin.kosakata.show', $kosakata->id) : null,
        ]);
    }

    /**
     * Tombol "Pindahkan ke" di popup: ubah status hafalan satu kata secara manual.
     * Hanya mengubah kolom status (jadwal SM-2 dan riwayat_reviews tidak disentuh).
     */
    public function pindahStatus(Request $request, Kosakata $kosakata): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(ProgresHafalan::STATUS)],
        ]);

        $progres = ProgresHafalan::firstOrNew(['kosakata_id' => $kosakata->id]);
        $progres->status = $data['status'];

        // Dianggap "sudah dipelajari" begitu dipindah ke status selain Berikutnya.
        if ($data['status'] !== ProgresHafalan::STATUS_BERIKUTNYA && ! $progres->pertama_dipelajari_pada) {
            $progres->pertama_dipelajari_pada = now();
        }

        $progres->save();

        return response()->json([
            'kunci' => $progres->status,
            'label' => $progres->status_label,
        ]);
    }
}
