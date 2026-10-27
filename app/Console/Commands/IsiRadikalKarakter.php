<?php

namespace App\Console\Commands;

use App\Models\Karakter;
use App\Models\Radikal;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;

/**
 * Mengisi karakters.radikal_id secara otomatis.
 *
 * Sumber: kamus makemeahanzi (dasar data goresan Hanzi Writer) yang mencatat
 * radikal tiap karakter. Radikalnya dicocokkan ke tabel `radikals` (214 radikal
 * Kangxi) lewat kolom hanzi atau varian. Radikal bentuk sederhana (讠, 钅, 纟 ...)
 * dipetakan dulu ke radikal tradisionalnya (言, 金, 糸 ...).
 *
 * Pemakaian:
 *   php artisan karakter:isi-radikal --dry-run   (lihat hasil dulu, tidak menyimpan)
 *   php artisan karakter:isi-radikal             (isi yang radikal_id-nya masih kosong)
 *   php artisan karakter:isi-radikal --semua     (timpa juga yang sudah terisi)
 *   php artisan karakter:isi-radikal --file=C:\data\dictionary.txt   (tanpa unduh)
 */
class IsiRadikalKarakter extends Command
{
    protected $signature = 'karakter:isi-radikal
                            {--semua : Timpa juga karakter yang radikal_id-nya sudah terisi}
                            {--dry-run : Hanya tampilkan hasilnya, tidak menyimpan ke database}
                            {--file= : Pakai file dictionary.txt lokal (tanpa mengunduh)}';

    protected $description = 'Isi radikal_id tabel karakters dari data makemeahanzi, dicocokkan ke tabel radikals';

    private const SUMBER = 'https://raw.githubusercontent.com/skishore/makemeahanzi/master/dictionary.txt';

    /** Radikal bentuk sederhana / varian di sumber data -> radikal Kangxi di tabel radikals. */
    private const KE_TRADISIONAL = [
        '钅' => '金', '讠' => '言', '纟' => '糸', '鱼' => '魚', '鸟' => '鳥', '贝' => '貝',
        '马' => '馬', '车' => '車', '门' => '門', '饣' => '食', '页' => '頁', '见' => '見',
        '齿' => '齒', '风' => '風', '龙' => '龍', '韦' => '韋', '麦' => '麥', '肀' => '聿',
        '长' => '長', '飞' => '飛', '黄' => '黃', '齐' => '齊', '黾' => '黽', '龟' => '龜', '卤' => '鹵',
        '⺊' => '卜', '⺌' => '小', '⺀' => '冫', '母' => '毋', '虎' => '虍',
    ];

    /**
     * Koreksi per karakter (radikal Kangxi-nya) untuk karakter yang di sumber data
     * radikalnya bukan radikal Kangxi (巳, 民, 炏, 丷 ...) atau tidak ada di kamus.
     * Selalu diutamakan di atas data sumber.
     */
    private const KOREKSI_KARAKTER = [
        '巳' => '己', '巴' => '己', '巷' => '己', '巽' => '己',
        '民' => '氏', '氓' => '氏',
        '營' => '火', '煢' => '火',
        '兼' => '八',
        '刁' => '刀', '孑' => '子',
        '汙' => '水', '湮' => '水',
        '艷' => '色', '嬾' => '女', '馌' => '食',
    ];

    public function handle(): int
    {
        $kolomHanzi = Schema::hasColumn('karakters', 'hanzi') ? 'hanzi' : 'karakter';
        $dryRun     = (bool) $this->option('dry-run');
        $semua      = (bool) $this->option('semua');

        // 1) Peta hanzi & varian radikal -> id radikal (dari tabel radikals).
        $radikals = Radikal::all(['id', 'hanzi', 'pinyin', 'arti_indonesia', 'varian'])->keyBy('id');
        $peta     = [];
        foreach ($radikals as $r) {
            $peta[$r->hanzi] = $r->id;
        }
        foreach ($radikals as $r) {
            foreach ($r->varian ?? [] as $v) {
                $peta[$v] ??= $r->id;
            }
        }

        // 2) Data sumber -> peta karakter -> id radikal.
        $berkas = $this->siapkanBerkas();
        if ($berkas === null) {
            return self::FAILURE;
        }

        $petaKarakter = [];
        $radikalTakCocok = [];
        $fh = fopen($berkas, 'r');
        while (($baris = fgets($fh)) !== false) {
            $d = json_decode($baris, true);
            if (! is_array($d) || empty($d['character']) || empty($d['radical'])) {
                continue;
            }

            $id = $this->cariIdRadikal($d['radical'], $peta);
            if ($id !== null) {
                $petaKarakter[$d['character']] = $id;
            } else {
                $radikalTakCocok[$d['radical']] = true;
            }
        }
        fclose($fh);

        // 3) Isi karakters.radikal_id.
        $karakters = Karakter::query()
            ->when(! $semua, fn ($q) => $q->whereNull('radikal_id'))
            ->get(['id', $kolomHanzi, 'radikal_id']);

        $terisi = 0;
        $sudahBenar = 0;
        $tanpaData = [];
        $contoh = [];

        foreach ($karakters as $k) {
            $hanzi = $k->getAttribute($kolomHanzi);
            $koreksi = self::KOREKSI_KARAKTER[$hanzi] ?? null;
            $id    = ($koreksi !== null && isset($peta[$koreksi]))
                ? (int) $peta[$koreksi]
                : ($petaKarakter[$hanzi] ?? null);

            if ($id === null) {
                $tanpaData[] = $hanzi;
                continue;
            }

            if ((int) $k->radikal_id === $id) {
                $sudahBenar++;
                continue;
            }

            if (count($contoh) < 15) {
                $r = $radikals[$id];
                $contoh[] = [$hanzi, $r->hanzi . ' (' . $r->pinyin . ')', $r->arti_indonesia];
            }

            if (! $dryRun) {
                Karakter::whereKey($k->id)->update(['radikal_id' => $id]);
            }
            $terisi++;
        }

        // 4) Ringkasan.
        if ($contoh) {
            $this->table(['Karakter', 'Radikal', 'Arti radikal'], $contoh);
            if ($terisi > count($contoh)) {
                $this->line('... dan ' . ($terisi - count($contoh)) . ' karakter lainnya.');
            }
        }

        $this->info(($dryRun ? '[DRY-RUN, tidak disimpan] ' : '') . "Karakter diperiksa: {$karakters->count()}");
        $this->info(($dryRun ? 'Akan diisi: ' : 'Berhasil diisi: ') . $terisi);
        if ($sudahBenar) {
            $this->line("Sudah benar (dilewati): {$sudahBenar}");
        }
        if ($tanpaData) {
            $this->warn('Tidak ada data radikal di sumber untuk ' . count($tanpaData) . ' karakter: '
                . implode(' ', array_slice($tanpaData, 0, 30)) . (count($tanpaData) > 30 ? ' ...' : ''));
            $this->line('Isi manual lewat kolom radikal_id di tabel karakters (atau tambahkan ke KOREKSI_KARAKTER). Baris non-hanzi seperti "OK" boleh dibiarkan kosong.');
        }
        if ($radikalTakCocok) {
            $this->line('Radikal sumber yang tidak ada padanannya di tabel radikals: '
                . implode(' ', array_keys($radikalTakCocok)));
        }

        return self::SUCCESS;
    }

    /** Cocokkan radikal dari sumber ke id di tabel radikals (hanzi/varian, lalu bentuk tradisional). */
    private function cariIdRadikal(string $radikal, array $peta): ?int
    {
        foreach ([$radikal, self::KE_TRADISIONAL[$radikal] ?? null] as $kunci) {
            if ($kunci !== null && isset($peta[$kunci])) {
                return (int) $peta[$kunci];
            }
        }

        return null;
    }

    /** Path file dictionary.txt: dari --file, dari cache storage, atau diunduh dulu. */
    private function siapkanBerkas(): ?string
    {
        if ($lokal = $this->option('file')) {
            if (! is_file($lokal)) {
                $this->error("File tidak ditemukan: {$lokal}");

                return null;
            }

            return $lokal;
        }

        $tujuan = storage_path('app/makemeahanzi-dictionary.txt');
        if (is_file($tujuan)) {
            return $tujuan;
        }

        $this->info('Mengunduh data radikal makemeahanzi (±2,5 MB) ...');
        try {
            $res = Http::timeout(120)->get(self::SUMBER);
        } catch (\Throwable $e) {
            $this->error('Gagal mengunduh: ' . $e->getMessage());
            $this->line('Unduh manual dari ' . self::SUMBER . ' lalu jalankan dengan --file=<lokasi file>.');

            return null;
        }

        if (! $res->successful()) {
            $this->error('Gagal mengunduh (HTTP ' . $res->status() . '). Unduh manual dari ' . self::SUMBER
                . ' lalu jalankan dengan --file=<lokasi file>.');

            return null;
        }

        file_put_contents($tujuan, $res->body());

        return $tujuan;
    }
}
