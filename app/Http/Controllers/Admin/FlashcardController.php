<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GrupKosakata;
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
 * Latihan hafalan kosakata untuk admin (single user: tidak ada user_id di progres_hafalans).
 *
 *  - Mode "review": kata berstatus lupa_dan_ingat / ingat_sepenuhnya yang sudah jatuh tempo.
 *  - Mode "bebas" : latihan bebas dengan filter grup, status, kategori, level, jumlah, jenis soal.
 *
 * HANYA kata yang sudah masuk grup kosakata (grup_kosakata_kosakata) yang dipakai,
 * baik di review harian maupun latihan bebas.
 *
 * Setiap kartu ditampilkan sebagai SOAL (mis. "Pinyin-nya apa?"). Jawaban dinilai sendiri
 * oleh pengguna (Benar / Salah), lalu progres dihitung otomatis:
 *
 *  - Benar : benar_beruntun + 1. Mencapai TARGET_BERUNTUN (20) -> ingat_sepenuhnya.
 *  - Salah : benar_beruntun kembali 0 dan status kembali ke lupa_dan_ingat.
 */
class FlashcardController extends Controller
{
    /** Maksimal kata dalam satu sesi review harian. */
    private const BATAS_REVIEW = 50;

    /** Pilihan jumlah kartu pada latihan bebas. */
    private const PILIHAN_JUMLAH = [10, 20, 30, 50];

    /** Jumlah contoh kalimat yang ditampilkan di sisi belakang kartu. */
    private const BATAS_CONTOH = 2;

    /** Jumlah jawaban benar BERTURUT-TURUT agar kata menjadi "ingat sepenuhnya". */
    private const TARGET_BERUNTUN = 20;

    /** Jeda review (hari): kata yang sudah ingat sepenuhnya vs. yang masih dihafal. */
    private const INTERVAL_INGAT = 7;
    private const INTERVAL_BELAJAR = 1;

    /** Jenis soal: kunci => label. Cara tampil tiap jenis ada di sesi.blade.php. */
    private const JENIS_SOAL = [
        'hanzi_pinyin' => 'Hanzi → Pinyin',
        'pinyin_hanzi' => 'Pinyin → Hanzi',
        'arti_hanzi'   => 'Arti → Hanzi',
        'arti_pinyin'  => 'Arti → Pinyin',
        'hanzi_arti'   => 'Hanzi → Arti',
    ];

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
            'grups'          => GrupKosakata::withCount('kosakatas')->orderBy('nama')->get(),
            'kategoris'      => Kategori::orderBy('nama')->get(['id', 'nama']),
            'levels'         => LevelTocfl::orderBy('tingkat')->orderBy('band')->get(['id', 'nama']),
            'pilihanJumlah'  => self::PILIHAN_JUMLAH,
            'jenisSoal'      => self::JENIS_SOAL,
            'targetBeruntun' => self::TARGET_BERUNTUN,
        ]);
    }

    // ------------------------------------------------------------ mulai sesi

    public function mulai(Request $request)
    {
        $data = $request->validate([
            'mode'           => ['required', Rule::in(['review', 'bebas'])],
            'status'         => ['nullable', Rule::in(ProgresBase::STATUS)],
            'grup_kosakata_id' => ['nullable', 'integer', 'exists:grup_kosakatas,id'],
            'kategori_id'    => ['nullable', 'integer', 'exists:kategoris,id'],
            'level_tocfl_id' => ['nullable', 'integer', 'exists:level_tocfls,id'],
            'jumlah'         => ['nullable', 'integer', Rule::in(self::PILIHAN_JUMLAH)],
            'acak'           => ['nullable', 'boolean'],
            'jenis'          => ['nullable', 'array'],
            'jenis.*'        => ['string', Rule::in(array_keys(self::JENIS_SOAL))],
        ]);

        $mode = $data['mode'];

        // Review harian selalu memakai semua jenis soal; latihan bebas boleh memilih.
        $jenis = ($mode === 'bebas' && ! empty($data['jenis']))
            ? array_values(array_unique($data['jenis']))
            : array_keys(self::JENIS_SOAL);

        $kosakatas = $mode === 'review'
            ? $this->ambilReview()
            : $this->ambilBebas($data, $request->boolean('acak', true));

        if ($kosakatas->isEmpty()) {
            return redirect()->route('admin.flashcard.index')
                ->with('error', $mode === 'review'
                    ? 'Tidak ada kata di grup kosakatamu yang jatuh tempo untuk direview.'
                    : 'Tidak ada kata di grup kosakatamu yang cocok dengan filter tersebut.');
        }

        return view('pages.flashcard.sesi', [
            'mode'           => $mode,
            'jenis'          => $jenis,
            'targetBeruntun' => self::TARGET_BERUNTUN,
            'kartu'          => $kosakatas->map(fn (Kosakata $k) => $this->keKartu($k))->values(),
        ]);
    }

    // ------------------------------------------------------------ simpan jawaban

    public function jawab(Request $request): JsonResponse
    {
        $data = $request->validate([
            'kosakata_id' => ['required', 'integer', 'exists:kosakatas,id'],
            'benar'       => ['required', 'boolean'],
            'durasi_ms'   => ['nullable', 'integer', 'min:0'],
        ]);

        $benar = (bool) $data['benar'];

        $hasil = DB::transaction(function () use ($data, $benar) {
            $progres = ProgresHafalan::where('kosakata_id', $data['kosakata_id'])
                ->lockForUpdate()
                ->first() ?? new ProgresHafalan(['kosakata_id' => $data['kosakata_id']]);

            $statusSebelum = $progres->status ?? ProgresBase::STATUS_BERIKUTNYA;
            $sekarang = now();

            if ($benar) {
                $progres->benar_beruntun = ($progres->benar_beruntun ?? 0) + 1;

                // Sudah ingat sepenuhnya tetap di sana selama jawabannya benar.
                // Kata lain naik kalau sudah benar TARGET_BERUNTUN kali berturut-turut.
                $status = ($statusSebelum === ProgresBase::STATUS_INGAT_SEPENUHNYA
                    || $progres->benar_beruntun >= self::TARGET_BERUNTUN)
                    ? ProgresBase::STATUS_INGAT_SEPENUHNYA
                    : ProgresBase::STATUS_LUPA_DAN_INGAT;
            } else {
                // Salah satu kali: hitungan mulai dari nol, status kembali ke lupa & ingat.
                $progres->benar_beruntun = 0;
                $progres->jumlah_salah = ($progres->jumlah_salah ?? 0) + 1;
                $status = ProgresBase::STATUS_LUPA_DAN_INGAT;
            }

            $interval = $status === ProgresBase::STATUS_INGAT_SEPENUHNYA
                ? self::INTERVAL_INGAT
                : self::INTERVAL_BELAJAR;

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
                'benar'          => $benar,
                'durasi_ms'      => $data['durasi_ms'] ?? null,
                'direview_pada'  => $sekarang,
            ]);

            return [
                'progres' => $progres,
                'naik'    => $statusSebelum !== ProgresBase::STATUS_INGAT_SEPENUHNYA
                             && $status === ProgresBase::STATUS_INGAT_SEPENUHNYA,
                'turun'   => $statusSebelum === ProgresBase::STATUS_INGAT_SEPENUHNYA
                             && $status !== ProgresBase::STATUS_INGAT_SEPENUHNYA,
            ];
        });

        $progres = $hasil['progres'];

        return response()->json([
            'ok'                => true,
            'status'            => $progres->status,
            'benar_beruntun'    => (int) $progres->benar_beruntun,
            'target'            => self::TARGET_BERUNTUN,
            'naik'              => $hasil['naik'],
            'turun'             => $hasil['turun'],
            'review_berikutnya' => $progres->review_berikutnya->toDateString(),
        ]);
    }

    // ------------------------------------------------------------ query

    /**
     * Progres yang jatuh tempo (hanya yang sudah pernah dijawab: lupa & ingat / ingat sepenuhnya),
     * dan hanya untuk kata yang ada di salah satu grup kosakata.
     */
    private function queryJatuhTempo()
    {
        return ProgresHafalan::jatuhTempo()
            ->whereIn('status', [
                ProgresBase::STATUS_LUPA_DAN_INGAT,
                ProgresBase::STATUS_INGAT_SEPENUHNYA,
            ])
            ->whereHas('kosakata.grupKosakatas');
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
            // hanya kata yang ada di grup kosakata (grup tertentu kalau dipilih, kalau tidak: grup mana pun)
            ->whereHas('grupKosakatas', function ($g) use ($data) {
                if (! empty($data['grup_kosakata_id'])) {
                    $g->where('grup_kosakatas.id', $data['grup_kosakata_id']);
                }
            })
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
            $awalan . 'progres',
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
            'beruntun' => (int) ($k->progres?->benar_beruntun ?? 0),
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
