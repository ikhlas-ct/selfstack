<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProgresBase;
use App\Models\ProgresRadikal;
use App\Models\Radikal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Halaman "Hafalan Radikal": daftar radikal + status hafalan (progres_radikals),
 * popup detail (JSON), dan pindah status satu radikal.
 *
 * Radikal yang belum punya baris progres dianggap berstatus "berikutnya".
 */
class HafalanRadikalController extends Controller
{
    public function index(Request $request)
    {
        $status = in_array($request->query('status'), ProgresBase::STATUS, true)
            ? $request->query('status')
            : null;
        $cari    = trim((string) $request->query('search', ''));
        $goresan = $request->filled('goresan') ? (int) $request->query('goresan') : null;

        // Jumlah pil status: total keseluruhan, tidak ikut filter.
        $totalRadikal = Radikal::count();
        $ingat        = ProgresRadikal::status(ProgresBase::STATUS_INGAT_SEPENUHNYA)->count();
        $lupa         = ProgresRadikal::status(ProgresBase::STATUS_LUPA_DAN_INGAT)->count();
        $berikutnya   = max($totalRadikal - $ingat - $lupa, 0);

        $query = Radikal::with('progres')
            ->orderBy('nomor_kangxi')
            ->orderBy('urutan')
            ->orderBy('id');

        if ($cari !== '') {
            $tanpaSpasi = str_replace([' ', "'", '’'], '', $cari);

            $query->where(function ($q) use ($cari, $tanpaSpasi) {
                $q->where('hanzi', 'like', "%{$cari}%")
                  ->orWhere('arti_indonesia', 'like', "%{$cari}%")
                  ->orWhere('pinyin', 'like', "%{$cari}%")
                  // pinyin tanpa spasi & nada, sama seperti Kosakata::scopeCari
                  ->orWhereRaw(
                      "REPLACE(REPLACE(REPLACE(pinyin, ' ', ''), '''', ''), '’', '') COLLATE utf8mb4_unicode_ci LIKE ?",
                      ["%{$tanpaSpasi}%"]
                  );
            });
        }

        if ($goresan !== null) {
            $query->where('jumlah_goresan', $goresan);
        }

        if ($status === ProgresBase::STATUS_BERIKUTNYA) {
            $query->where(function ($q) {
                $q->whereDoesntHave('progres')
                  ->orWhereHas('progres', fn ($p) => $p->where('status', ProgresBase::STATUS_BERIKUTNYA));
            });
        } elseif ($status !== null) {
            $query->whereHas('progres', fn ($p) => $p->where('status', $status));
        }

        $radikals   = $query->get(); // maksimal 214 baris (Kangxi), aman diambil semua
        $daftarGoresan = Radikal::query()
            ->whereNotNull('jumlah_goresan')
            ->distinct()
            ->orderBy('jumlah_goresan')
            ->pluck('jumlah_goresan');

        $view = view('pages.hafalan-radikal.index', [
            'radikals'      => $radikals,
            'totalRadikal'  => $totalRadikal,
            'totalCocok'    => $radikals->count(),
            'ingat'         => $ingat,
            'lupa'          => $lupa,
            'berikutnya'    => $berikutnya,
            'status'        => $status,
            'cari'          => $cari,
            'goresan'       => $goresan,
            'daftarGoresan' => $daftarGoresan,
        ]);

        // Pencarian/filter via AJAX hanya butuh bagian daftar.
        return $request->ajax() ? $view->fragment('daftar-radikal') : $view;
    }

    /** Data popup detail satu radikal. */
    public function riwayat(Radikal $radikal): JsonResponse
    {
        $radikal->load('progres');
        $progres = $radikal->progres;
        $kunci   = $progres->status ?? ProgresBase::STATUS_BERIKUTNYA;

        $total = $radikal->riwayatReviews()->count();
        $benar = $radikal->riwayatReviews()->where('benar', true)->count();

        $riwayat = $radikal->riwayatReviews()
            ->orderByDesc('id')
            ->limit(10)
            ->get()
            ->map(fn ($r) => [
                'waktu'              => $r->created_at?->translatedFormat('d M Y, H:i'),
                'relatif'            => $r->created_at?->diffForHumans(),
                'benar'              => (bool) $r->benar,
                'sumber'             => $r->jawaban_ujian_id ? 'soal' : 'flashcard',
                'status_saat_review' => ProgresBase::LABEL[$r->status_saat_review] ?? (string) $r->status_saat_review,
            ])
            ->values();

        $rb = $progres?->review_berikutnya;

        return response()->json([
            'id'             => $radikal->id,
            'hanzi'          => $radikal->hanzi,
            'pinyin'         => $radikal->pinyin,
            'arti'           => $radikal->arti_indonesia,
            'nomor_kangxi'   => $radikal->nomor_kangxi,
            'jumlah_goresan' => $radikal->jumlah_goresan,
            'varian'         => $radikal->varian ?? [],
            'jumlah_karakter' => $radikal->karakters()->count(),
            'karakter'       => $radikal->karakters()
                ->orderBy('urutan')
                ->limit(12)
                ->get(['id', 'hanzi', 'pinyin'])
                ->map(fn ($k) => ['hanzi' => $k->hanzi, 'pinyin' => $k->pinyin])
                ->values(),
            'status'         => ['kunci' => $kunci, 'label' => ProgresBase::LABEL[$kunci]],
            'url_status'     => route('admin.hafalan-radikal.status', $radikal),
            'url_detail'     => route('admin.radikal.show', $radikal),
            'review_berikutnya' => $rb ? [
                'tanggal' => $rb->translatedFormat('d M Y, H:i'),
                'lewat'   => $rb->isPast(),
            ] : null,
            'total'          => $total,
            'akurasi'        => $total > 0 ? (int) round($benar / $total * 100) : null,
            'beruntun'       => (int) ($progres->benar_beruntun ?? 0),
            'riwayat'        => $riwayat,
        ]);
    }

    /** Pindahkan status hafalan satu radikal (membuat baris progres kalau belum ada). */
    public function updateStatus(Request $request, Radikal $radikal): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(ProgresBase::STATUS)],
        ]);

        $progres = ProgresRadikal::firstOrNew(['radikal_id' => $radikal->id]);

        if (! $progres->exists) {
            $progres->pertama_dipelajari_pada = now();
        }

        $progres->status = $data['status'];
        $progres->save();

        return response()->json([
            'kunci' => $progres->status,
            'label' => $progres->status_label,
        ]);
    }
}
