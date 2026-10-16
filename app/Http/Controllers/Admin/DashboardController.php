<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContohKalimat;
use App\Models\GrupSoal;
use App\Models\HasilUjian;
use App\Models\Karakter;
use App\Models\Kategori;
use App\Models\Kosakata;
use App\Models\LaporanSoal;
use App\Models\LevelTocfl;
use App\Models\Paragraf;
use App\Models\PolaTataBahasa;
use App\Models\ProgresBase;
use App\Models\ProgresHafalan;
use App\Models\ProgresKarakter;
use App\Models\ProgresRadikal;
use App\Models\Radikal;
use App\Models\RiwayatReview;
use App\Models\Soal;
use App\Models\WebsiteSetting;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    /** Tiga tabel progres yang strukturnya sama (lihat ProgresBase). */
    private const PROGRES = [
        'kosakata' => ProgresHafalan::class,
        'karakter' => ProgresKarakter::class,
        'radikal'  => ProgresRadikal::class,
    ];

    public function index(): View
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $setting = WebsiteSetting::ambil();
        $progres = $this->ringkasProgres();
        $review  = $this->ringkasReviewHariIni();

        $stats = $this->ringkasKonten() + $review + [
            'streak'        => $this->hitungStreak(),
            'dipelajari'    => collect($progres)->sum('dipelajari'),
            'jatuh_tempo'   => collect($progres)->sum('jatuh_tempo'),
            'bandel'        => collect($progres)->sum('bandel'),
            'laporan_baru'  => LaporanSoal::baru()->count(),
            'perlu_dicek'   => Soal::perluDicek()->count(),
        ];

        return view('pages.admin.dashboard', [
            'setting'            => $setting,
            'stats'              => $stats,
            'progres'            => $progres,
            'target'             => $this->targetHariIni($setting, $stats['review_hari_ini'], $progres),
            'grafik'             => $this->reviewHarian(30),
            'distribusi_level'   => $this->distribusiLevel(),
            'distribusi_status'  => $this->distribusiStatus(),
            'bank_soal'          => $this->bankSoal(),
            'review_terbaru'     => RiwayatReview::with(['kosakata', 'karakter', 'radikal'])
                                        ->latest('direview_pada')->limit(8)->get(),
            'ujian_terbaru'      => HasilUjian::with('grupSoal')->selesai()
                                        ->latest('selesai_pada')->limit(6)->get(),
            'item_bandel'        => $this->itemBandel(8),
            'laporan_terbaru'    => LaporanSoal::with('soal')->baru()->latest('id')->limit(6)->get(),
        ]);
    }

    // ------------------------------------------------------------ ringkasan

    /** Jumlah data master. */
    private function ringkasKonten(): array
    {
        return [
            'kosakata'    => Kosakata::count(),
            'karakter'    => Karakter::count(),
            'radikal'     => Radikal::count(),
            'kalimat'     => ContohKalimat::count(),
            'pola'        => PolaTataBahasa::count(),
            'paragraf'    => Paragraf::count(),
            'kategori'    => Kategori::count(),
            'paket_soal'  => GrupSoal::count(),
            'paket_aktif' => GrupSoal::aktif()->count(),
            'soal'        => Soal::count(),
        ];
    }

    /** Review dan akurasi hari ini (hari kalender). */
    private function ringkasReviewHariIni(): array
    {
        $hariIni = RiwayatReview::where('direview_pada', '>=', today());

        $total = (clone $hariIni)->count();
        $benar = (clone $hariIni)->where('benar', true)->count();

        return [
            'review_hari_ini'  => $total,
            'akurasi_hari_ini' => $total > 0 ? (int) round($benar / $total * 100) : 0,
        ];
    }

    /** Angka progres per jenis item: kosakata, karakter, radikal. */
    private function ringkasProgres(): array
    {
        $hasil = [];

        foreach (self::PROGRES as $jenis => $kelas) {
            $hasil[$jenis] = [
                'dipelajari'    => $kelas::sudahDipelajari()->count(),
                'jatuh_tempo'   => $kelas::jatuhTempo()->count(),
                'bandel'        => $kelas::aktif()->bandel()->count(),
                'ditandai'      => $kelas::ditandai()->count(),
                'ditangguhkan'  => $kelas::where('ditangguhkan', true)->count(),
                'baru_hari_ini' => $kelas::where('pertama_dipelajari_pada', '>=', today())->count(),
            ];
        }

        return $hasil;
    }

    /** Target belajar harian dari WebsiteSetting dibandingkan dengan capaian hari ini. */
    private function targetHariIni(WebsiteSetting $setting, int $reviewHariIni, array $progres): array
    {
        return [
            ['label' => 'Review',         'ikon' => 'fa-rotate',     'warna' => '#16a34a', 'nilai' => $reviewHariIni,                     'target' => $setting->batas_review_per_hari],
            ['label' => 'Kosakata baru',  'ikon' => 'fa-language',   'warna' => '#7c3aed', 'nilai' => $progres['kosakata']['baru_hari_ini'], 'target' => $setting->kartu_baru_per_hari],
            ['label' => 'Karakter baru',  'ikon' => 'fa-pen-nib',    'warna' => '#1a73e8', 'nilai' => $progres['karakter']['baru_hari_ini'], 'target' => $setting->karakter_baru_per_hari],
            ['label' => 'Radikal baru',   'ikon' => 'fa-puzzle-piece', 'warna' => '#0d9488', 'nilai' => $progres['radikal']['baru_hari_ini'],  'target' => $setting->radikal_baru_per_hari],
        ];
    }

    // ---------------------------------------------------------------- grafik

    /** Review per hari untuk $hari hari terakhir (hari tanpa review diisi 0). */
    private function reviewHarian(int $hari): array
    {
        $mulai = today()->subDays($hari - 1);

        $baris = RiwayatReview::where('direview_pada', '>=', $mulai)
            ->selectRaw('DATE(direview_pada) as tgl, COUNT(*) as total, SUM(CASE WHEN benar THEN 1 ELSE 0 END) as jumlah_benar')
            ->groupBy('tgl')
            ->get()
            ->keyBy('tgl');

        $label = $total = $benar = [];

        for ($i = $hari - 1; $i >= 0; $i--) {
            $tanggal = today()->subDays($i);
            $data    = $baris->get($tanggal->toDateString());

            $label[] = $tanggal->translatedFormat('d M');
            $total[] = (int) ($data->total ?? 0);
            $benar[] = (int) ($data->jumlah_benar ?? 0);
        }

        return ['label' => $label, 'total' => $total, 'benar' => $benar];
    }

    /** Hari berturut-turut dengan minimal satu review. Kalau hari ini belum belajar, hitung mundur dari kemarin. */
    private function hitungStreak(): int
    {
        $tanggal = RiwayatReview::where('direview_pada', '>=', today()->subDays(365))
            ->selectRaw('DATE(direview_pada) as tgl')
            ->groupBy('tgl')
            ->pluck('tgl')
            ->flip();

        $kursor = today();

        if (! $tanggal->has($kursor->toDateString())) {
            $kursor = $kursor->copy()->subDay();
        }

        $streak = 0;

        while ($tanggal->has($kursor->toDateString())) {
            $streak++;
            $kursor = $kursor->copy()->subDay();
        }

        return $streak;
    }

    /** Jumlah kosakata per level TOCFL. */
    private function distribusiLevel()
    {
        $level = LevelTocfl::withCount('kosakatas')
            ->orderBy('tingkat')
            ->get()
            ->map(fn ($l) => ['label' => 'Level ' . $l->tingkat, 'total' => $l->kosakatas_count]);

        $tanpaLevel = Kosakata::whereNull('level_tocfl_id')->count();

        if ($tanpaLevel > 0) {
            $level->push(['label' => 'Tanpa level', 'total' => $tanpaLevel]);
        }

        return $level->values();
    }

    /** Gabungan status SM-2 dari kosakata, karakter, dan radikal. */
    private function distribusiStatus(): array
    {
        $hasil = array_fill_keys(ProgresBase::STATUS, 0);

        foreach (self::PROGRES as $kelas) {
            $kelas::query()
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status')
                ->each(function ($total, $status) use (&$hasil) {
                    if (array_key_exists($status, $hasil)) {
                        $hasil[$status] += (int) $total;
                    }
                });
        }

        return $hasil;
    }

    // ------------------------------------------------------------- bank soal

    private function bankSoal(): array
    {
        return [
            'bagian' => Soal::query()->selectRaw('bagian, COUNT(*) as total')
                ->groupBy('bagian')->pluck('total', 'bagian'),
            'status' => Soal::query()->selectRaw('status_validasi, COUNT(*) as total')
                ->groupBy('status_validasi')->pluck('total', 'status_validasi'),
        ];
    }

    // ------------------------------------------------------------ item bandel

    /** Item yang paling sering salah (di antara yang tidak ditangguhkan), gabungan tiga jenis. */
    private function itemBandel(int $batas)
    {
        $sumber = [
            ['kelas' => ProgresHafalan::class,  'relasi' => 'kosakata', 'jenis' => 'Kosakata'],
            ['kelas' => ProgresKarakter::class, 'relasi' => 'karakter', 'jenis' => 'Karakter'],
            ['kelas' => ProgresRadikal::class,  'relasi' => 'radikal',  'jenis' => 'Radikal'],
        ];

        return collect($sumber)
            ->flatMap(fn ($s) => $s['kelas']::aktif()->bandel()
                ->with($s['relasi'])
                ->orderByDesc('jumlah_salah')
                ->limit($batas)
                ->get()
                ->map(function ($p) use ($s) {
                    $item = $p->{$s['relasi']};

                    return [
                        'jenis'        => $s['jenis'],
                        'hanzi'        => $item?->hanzi,
                        'pinyin'       => $item?->pinyin,
                        'arti'         => $item?->arti_pilihan ?? $item?->arti_indonesia,
                        'jumlah_salah' => $p->jumlah_salah,
                    ];
                }))
            ->sortByDesc('jumlah_salah')
            ->take($batas)
            ->values();
    }
}
