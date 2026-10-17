<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Kosakata;
use App\Models\LevelTocfl;
use App\Models\ProgresBase;
use App\Models\ProgresHafalan;
use App\Models\RiwayatReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Flashcard hafalan kosakata untuk admin (single user: tidak ada user_id di progres_hafalans).
 *
 *  - Mode "review": kata berstatus lupa_dan_ingat / ingat_sepenuhnya yang sudah jatuh tempo.
 *  - Mode "bebas" : latihan bebas dengan filter status, kategori, level, jumlah.
 *
 * Jawaban: Ingat -> ingat_sepenuhnya (review 7 hari lagi), Lupa -> lupa_dan_ingat (review besok).
 */
class FlashcardController extends Controller
{
    /** Maksimal kata dalam satu sesi review harian. */
    private const BATAS_REVIEW = 50;

    /** Pilihan jumlah kartu pada latihan bebas. */
    private const PILIHAN_JUMLAH = [10, 20, 30, 50];

    /** Jumlah contoh kalimat yang ditampilkan di sisi belakang kartu. */
    private const BATAS_CONTOH = 2;

    private const INTERVAL_INGAT = 7;
    private const INTERVAL_LUPA = 1;

    // ------------------------------------------------------------ halaman awal

    public function index()
    {
        $jatuhTempo = $this->queryJatuhTempo()->count();

        $statusList = collect(ProgresBase::LABEL)
            ->map(fn ($label) => ['label' => $label])
            ->all();

        return view('pages.flashcard.index', [
            'jatuhTempo'     => $jatuhTempo,
            'batasReview'    => self::BATAS_REVIEW,
            'statusList'     => $statusList,
            'kategoris'      => Kategori::orderBy('nama')->get(['id', 'nama']),
            'levels'         => LevelTocfl::orderBy('tingkat')->orderBy('band')->get(['id', 'nama']),
            'pilihanJumlah'  => self::PILIHAN_JUMLAH,
        ]);
    }

    // ------------------------------------------------------------ mulai sesi

    public function mulai(Request $request)
    {
        $data = $request->validate([
            'mode'           => ['required', Rule::in(['review', 'bebas'])],
            'status'         => ['nullable', Rule::in(ProgresBase::STATUS)],
            'kategori_id'    => ['nullable', 'integer', 'exists:kategoris,id'],
            'level_tocfl_id' => ['nullable', 'integer', 'exists:level_tocfls,id'],
            'jumlah'         => ['nullable', 'integer', Rule::in(self::PILIHAN_JUMLAH)],
            'acak'           => ['nullable', 'boolean'],
        ]);

        $mode = $data['mode'];

        $kosakatas = $mode === 'review'
            ? $this->ambilReview()
            : $this->ambilBebas($data, $request->boolean('acak', true));

        if ($kosakatas->isEmpty()) {
            return redirect()->route('admin.flashcard.index')
                ->with('error', $mode === 'review'
                    ? 'Tidak ada kata yang jatuh tempo untuk direview.'
                    : 'Tidak ada kata yang cocok dengan filter tersebut.');
        }

        return view('pages.flashcard.sesi', [
            'mode'  => $mode,
            'kartu' => $kosakatas->map(fn (Kosakata $k) => $this->keKartu($k))->values(),
        ]);
    }

    // ------------------------------------------------------------ simpan jawaban

    public function jawab(Request $request): JsonResponse
    {
        $data = $request->validate([
            'kosakata_id' => ['required', 'integer', 'exists:kosakatas,id'],
            'ingat'       => ['required', 'boolean'],
            'durasi_ms'   => ['nullable', 'integer', 'min:0'],
        ]);

        $ingat = (bool) $data['ingat'];

        $progres = DB::transaction(function () use ($data, $ingat) {
            $progres = ProgresHafalan::where('kosakata_id', $data['kosakata_id'])
                ->lockForUpdate()
                ->first() ?? new ProgresHafalan(['kosakata_id' => $data['kosakata_id']]);

            $statusSebelum = $progres->status ?? ProgresBase::STATUS_BERIKUTNYA;
            $sekarang = now();

            if ($ingat) {
                $status = ProgresBase::STATUS_INGAT_SEPENUHNYA;
                $interval = self::INTERVAL_INGAT;
                $progres->benar_beruntun = ($progres->benar_beruntun ?? 0) + 1;
            } else {
                $status = ProgresBase::STATUS_LUPA_DAN_INGAT;
                $interval = self::INTERVAL_LUPA;
                $progres->benar_beruntun = 0;
                $progres->jumlah_salah = ($progres->jumlah_salah ?? 0) + 1;
            }

            $progres->status = $status;
            $progres->interval_hari = $interval;
            $progres->jumlah_ulang = ($progres->jumlah_ulang ?? 0) + 1;
            $progres->terakhir_diulang = $sekarang;
            $progres->pertama_dipelajari_pada ??= $sekarang;
            // Jatuh tempo mulai awal hari target, jadi "besok" benar-benar muncul besok pagi.
            $progres->review_berikutnya = $sekarang->copy()->addDays($interval)->startOfDay();
            $progres->save();

            RiwayatReview::create([
                'jenis_item'     => RiwayatReview::ITEM_KOSAKATA,
                'kosakata_id'    => $data['kosakata_id'],
                'sumber'         => RiwayatReview::SUMBER_POPUP, // = flashcard
                'status_sebelum' => $statusSebelum,
                'status_sesudah' => $status,
                'benar'          => $ingat,
                'durasi_ms'      => $data['durasi_ms'] ?? null,
                'direview_pada'  => $sekarang,
            ]);

            return $progres;
        });

        return response()->json([
            'ok'                => true,
            'status'            => $progres->status,
            'review_berikutnya' => $progres->review_berikutnya->toDateString(),
        ]);
    }

    // ------------------------------------------------------------ query

    /** Progres yang jatuh tempo (hanya yang sudah pernah dijawab: lupa & ingat / ingat sepenuhnya). */
    private function queryJatuhTempo()
    {
        return ProgresHafalan::jatuhTempo()->whereIn('status', [
            ProgresBase::STATUS_LUPA_DAN_INGAT,
            ProgresBase::STATUS_INGAT_SEPENUHNYA,
        ]);
    }

    private function ambilReview()
    {
        return $this->queryJatuhTempo()
            ->with($this->eagerKosakata('kosakata.'))
            ->orderBy('review_berikutnya')
            ->limit(self::BATAS_REVIEW)
            ->get()
            ->pluck('kosakata')
            ->filter()
            ->shuffle()
            ->values();
    }

    private function ambilBebas(array $data, bool $acak)
    {
        $status = $data['status'] ?? null;

        $query = Kosakata::query()
            ->with($this->eagerKosakata())
            // kata yang ditangguhkan tidak ikut
            ->whereDoesntHave('progres', fn ($p) => $p->where('ditangguhkan', true));

        if ($status === ProgresBase::STATUS_BERIKUTNYA) {
            // "belum dipelajari": belum punya progres, atau berstatus berikutnya
            $query->where(function ($q) {
                $q->doesntHave('progres')
                  ->orWhereHas('progres', fn ($p) => $p->where('status', ProgresBase::STATUS_BERIKUTNYA));
            });
        } elseif ($status) {
            $query->whereHas('progres', fn ($p) => $p->where('status', $status));
        }

        if (! empty($data['kategori_id'])) {
            $query->where('kategori_id', $data['kategori_id']);
        }
        if (! empty($data['level_tocfl_id'])) {
            $query->where('level_tocfl_id', $data['level_tocfl_id']);
        }

        $acak ? $query->inRandomOrder() : $query->urut();

        return $query->limit($data['jumlah'] ?? 20)->get();
    }

    /** Relasi yang dipakai kartu. $awalan dipakai kalau di-load lewat ProgresHafalan. */
    private function eagerKosakata(string $awalan = ''): array
    {
        return [
            $awalan . 'kategori:id,nama',
            $awalan . 'levelTocfl:id,nama',
            $awalan . 'contohKalimats' => fn ($q) => $q->select('id', 'kosakata_id', 'hanzi', 'pinyin', 'arti_indonesia')
                ->orderBy('id'),
        ];
    }

    private function keKartu(Kosakata $k): array
    {
        return [
            'id'       => $k->id,
            'hanzi'    => $k->hanzi,
            'pinyin'   => $k->pinyin,
            'baca'     => $k->baca_indonesia,
            'arti'     => $k->arti_indonesia ?: $k->arti_singkat,
            'english'  => $k->english,
            'level'    => $k->levelTocfl?->nama,
            'kategori' => $k->kategori?->nama,
            'contoh'   => $k->contohKalimats
                ->take(self::BATAS_CONTOH)
                ->map(fn ($c) => [
                    'hanzi'  => $c->hanzi,
                    'pinyin' => $c->pinyin,
                    'arti'   => $c->arti_indonesia,
                ])
                ->values(),
        ];
    }
}
