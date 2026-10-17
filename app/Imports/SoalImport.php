<?php

namespace App\Imports;

use App\Models\ContohKalimat;
use App\Models\GrupKosakata;
use App\Models\GrupSoal;
use App\Models\Karakter;
use App\Models\Kosakata;
use App\Models\LevelTocfl;
use App\Models\Paragraf;
use App\Models\PolaTataBahasa;
use App\Models\Radikal;
use App\Models\Soal;
use App\Support\LabelSoal;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Import;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Import soal pilihan ganda dari satu file Excel dengan sheet:
 *
 *   Soal    -> tabel soals            (satu baris satu soal)
 *   Paket   -> tabel grup_soals       (opsional; membuat paket baru kalau judulnya belum ada)
 *   Bacaan  -> tabel paragrafs        (opsional; satu bacaan boleh dipakai banyak soal lewat kode_bacaan)
 *
 * Gaya kerjanya sama dengan KosakataImport: Excel::import() hanya MENGUMPULKAN baris,
 * selesai() yang memproses (Paket -> Bacaan -> Soal), tiap baris berdiri sendiri:
 * baris yang gagal dicatat, baris lain tetap masuk. Sheet lain (mis. "Petunjuk") diabaikan.
 *
 * Soal yang persis sama (pertanyaan + keempat pilihan) di paket yang sama dilewati,
 * jadi mengimpor ulang file yang sama tidak membuat soal dobel.
 */
class SoalImport implements Import, WithMultipleSheets, SkipsUnknownSheets
{
    private const MAKS_GALAT = 100;

    public const KOLOM_PAKET = [
        'judul', 'deskripsi', 'jenis', 'level_tocfl', 'grup_kosakata',
        'durasi_menit', 'nilai_lulus', 'maks_percobaan',
    ];

    public const KOLOM_BACAAN = [
        'kode', 'judul', 'hanzi', 'pinyin', 'arti_indonesia', 'penjelasan_tata_bahasa', 'level_tocfl',
    ];

    public const KOLOM_SOAL = [
        'paket', 'bagian', 'tipe', 'tingkat', 'poin', 'urutan',
        'kode_bacaan', 'paragraf', 'audio',
        'pertanyaan', 'pilihan_a', 'pilihan_b', 'pilihan_c', 'pilihan_d', 'jawaban_benar', 'penjelasan',
        'kosakata_hanzi', 'kosakata_pinyin', 'karakter_hanzi', 'karakter_pinyin', 'radikal_hanzi',
        'pola_tata_bahasa', 'contoh_kalimat_hanzi',
    ];

    /** nama sheet di Excel => kunci internal */
    private const SHEET = [
        'Paket'  => 'paket',
        'Bacaan' => 'bacaan',
        'Soal'   => 'soal',
    ];

    /** Tipe soal karakter yang wajib terhubung ke karakter (sama dengan SoalController). */
    private const TIPE_BUTUH_KARAKTER = [
        'karakter_ke_arti', 'arti_ke_karakter', 'karakter_ke_radikal', 'arti_karakter_dalam_kata',
    ];

    private const PESAN = [
        'required' => 'kolom :attribute wajib diisi',
        'max'      => 'kolom :attribute terlalu panjang (maks. :max karakter)',
        'integer'  => 'kolom :attribute harus berupa angka bulat',
        'between'  => 'kolom :attribute harus berada di antara :min dan :max',
        'in'       => 'kolom :attribute berisi nilai yang tidak dikenal (lihat daftar di halaman import)',
    ];

    /** @var array<string, Collection> kunci sheet => baris mentah */
    private array $baris = [];

    private array $ringkasan = [
        'paket_baru'    => 0,
        'paket_dipakai' => 0,
        'bacaan_baru'   => 0,
        'bacaan_dipakai' => 0,
        'soal_baru'     => 0,
        'soal_dilewati' => 0,
    ];

    private int $gagal = 0;
    private int $jumlahBaris = 0;
    private array $galat = [];

    /** id paket => ['id', 'judul', 'jumlah'] untuk tautan "periksa soalnya" di halaman hasil */
    private array $tambahan = [];

    /** judul paket (huruf kecil) => GrupSoal */
    private array $paket = [];
    /** angka tingkat => id level */
    private array $level = [];
    /** nama grup kosakata (huruf kecil) => id */
    private array $grupKosakata = [];
    /** kode bacaan (huruf kecil) => id paragraf */
    private array $bacaan = [];
    /** id paket => urutan terakhir yang dipakai */
    private array $urutan = [];

    /** cache pencarian: kunci => daftar id (maks. 2) */
    private array $petaKosakata = [];
    private array $petaKarakter = [];

    /**
     * @param GrupSoal|null $paketTujuan  Kalau diisi, semua soal masuk ke paket ini (kolom "paket" dan sheet Paket diabaikan).
     * @param string        $sumber       Soal::SUMBER_AI atau Soal::SUMBER_MANUAL
     * @param string        $statusAwal   Soal::STATUS_PERLU_DICEK atau Soal::STATUS_DISETUJUI
     */
    public function __construct(
        private ?GrupSoal $paketTujuan = null,
        private string $sumber = Soal::SUMBER_AI,
        private string $statusAwal = Soal::STATUS_PERLU_DICEK,
    ) {
        foreach (self::SHEET as $kunci) {
            $this->baris[$kunci] = collect();
        }
    }

    /* ---------- Bagian Laravel Excel ---------- */

    public function sheets(): array
    {
        $hasil = [];
        foreach (self::SHEET as $nama => $kunci) {
            $hasil[$nama] = $this->pengumpul($kunci);
        }

        return $hasil;
    }

    public function onUnknownSheet(string|int $sheetName): void
    {
        // Sheet lain (mis. "Petunjuk") diabaikan, begitu juga sheet opsional yang tidak ada di file.
    }

    public function terima(string $kunci, Collection $rows): void
    {
        $this->baris[$kunci] = $this->baris[$kunci]->concat($rows->values());
    }

    private function pengumpul(string $kunci): object
    {
        return new class($this, $kunci) implements ToCollection, WithHeadingRow {
            public function __construct(private SoalImport $induk, private string $kunci) {}

            public function collection(Collection $rows): void
            {
                $this->induk->terima($this->kunci, $rows);
            }
        };
    }

    /* ---------- Dipanggil controller ---------- */

    public function selesai(): void
    {
        $this->level = LevelTocfl::pluck('id', 'tingkat')->all();

        $this->paket = GrupSoal::all()
            ->keyBy(fn ($g) => mb_strtolower(trim($g->judul)))
            ->all();

        $this->grupKosakata = GrupKosakata::pluck('id', 'nama')
            ->mapWithKeys(fn ($id, $nama) => [mb_strtolower(trim($nama)) => $id])
            ->all();

        $this->prosesPaket();
        $this->prosesBacaan();
        $this->prosesSoal();
    }

    public function adaIsi(): bool
    {
        return $this->jumlahBaris > 0;
    }

    public function ringkasan(): array
    {
        return $this->ringkasan + [
            'gagal'             => $this->gagal,
            'galat'             => $this->galat,
            'paket_terpengaruh' => array_values($this->tambahan),
        ];
    }

    /* ---------- Kerangka satu sheet ---------- */

    /**
     * Baca tiap baris sheet: lewati baris kosong, validasi, lalu panggil $simpan.
     * $simpan mengembalikan null kalau berhasil, atau teks galat kalau baris itu harus gagal.
     */
    private function olah(
        string $kunci,
        string $namaSheet,
        array $kolom,
        array $aturan,
        callable $simpan,
        ?callable $siapkan = null,
    ): void {
        foreach ($this->baris[$kunci] as $i => $row) {
            $lokasi = "Sheet {$namaSheet} baris " . ($i + 2); // baris 1 = judul kolom
            $d = $this->ambil($row, $kolom);

            if ($siapkan) {
                $d = $siapkan($d);
            }

            if ($this->kosong($d)) {
                continue;
            }
            $this->jumlahBaris++;

            $v = Validator::make($d, $aturan, self::PESAN);

            if ($v->fails()) {
                $this->catatGagal($lokasi, $v->errors()->all());
                continue;
            }

            try {
                $galat = $simpan($d);
            } catch (\Throwable $e) {
                report($e);
                $this->catatGagal($lokasi, [$e->getMessage()]);
                continue;
            }

            if (is_string($galat)) {
                $this->catatGagal($lokasi, [$galat]);
            }
        }
    }

    /* ---------- Sheet Paket ---------- */

    private function prosesPaket(): void
    {
        // Paket tujuan sudah dipilih di halaman import: sheet Paket tidak dipakai.
        if ($this->paketTujuan) {
            return;
        }

        $this->olah(
            'paket',
            'Paket',
            self::KOLOM_PAKET,
            [
                'judul'          => ['required', 'string', 'max:500'],
                'deskripsi'      => ['nullable', 'string', 'max:5000'],
                'jenis'          => ['nullable', 'string', Rule::in(array_keys(LabelSoal::JENIS))],
                'level_tocfl'    => ['nullable', 'integer'],
                'grup_kosakata'  => ['nullable', 'string', 'max:255'],
                'durasi_menit'   => ['nullable', 'integer', 'between:1,1440'],
                'nilai_lulus'    => ['nullable', 'integer', 'between:0,100'],
                'maks_percobaan' => ['nullable', 'integer', 'between:1,255'],
            ],
            function (array $d): ?string {
                $kunci = mb_strtolower($d['judul']);

                // Paket dengan judul yang sama dipakai apa adanya (tidak diubah).
                if (isset($this->paket[$kunci])) {
                    $this->ringkasan['paket_dipakai']++;
                    return null;
                }

                if ($d['level_tocfl'] !== null && ! isset($this->level[(int) $d['level_tocfl']])) {
                    return "level_tocfl {$d['level_tocfl']} tidak ada di sistem";
                }

                $grupId = null;
                if ($d['grup_kosakata'] !== null) {
                    $grupId = $this->grupKosakata[mb_strtolower($d['grup_kosakata'])] ?? null;
                    if (! $grupId) {
                        return "grup kosakata \"{$d['grup_kosakata']}\" tidak ditemukan; buat grupnya dulu atau kosongkan kolom ini";
                    }
                }

                $jenis = $d['jenis'] ?? GrupSoal::JENIS_LATIHAN;

                $this->paket[$kunci] = GrupSoal::create([
                    'judul'            => $d['judul'],
                    'deskripsi'        => $d['deskripsi'],
                    'jenis'            => $jenis,
                    'level_tocfl_id'   => $d['level_tocfl'] !== null ? $this->level[(int) $d['level_tocfl']] : null,
                    'grup_kosakata_id' => $grupId,
                    'durasi_menit'     => $d['durasi_menit'] !== null ? (int) $d['durasi_menit'] : null,
                    // Nilai lulus hanya dipakai untuk ujian (lihat HasilUjian::selesaikan()).
                    'nilai_lulus'      => ($jenis === GrupSoal::JENIS_UJIAN && $d['nilai_lulus'] !== null) ? (int) $d['nilai_lulus'] : null,
                    'maks_percobaan'   => $d['maks_percobaan'] !== null ? (int) $d['maks_percobaan'] : null,
                    'sumber'           => $this->sumber,
                    'aktif'            => true,
                ]);
                $this->ringkasan['paket_baru']++;

                return null;
            },
            fn (array $d) => $this->normalisasiKolom($d, ['jenis']),
        );
    }

    /* ---------- Sheet Bacaan ---------- */

    private function prosesBacaan(): void
    {
        $this->olah(
            'bacaan',
            'Bacaan',
            self::KOLOM_BACAAN,
            [
                'kode'                   => ['required', 'string', 'max:100'],
                'judul'                  => ['required', 'string', 'max:500'],
                'hanzi'                  => ['required', 'string'],
                'pinyin'                 => ['required', 'string'],
                'arti_indonesia'         => ['required', 'string'],
                'penjelasan_tata_bahasa' => ['nullable', 'string'],
                'level_tocfl'            => ['nullable', 'integer'],
            ],
            function (array $d): ?string {
                $kode = mb_strtolower($d['kode']);

                if (isset($this->bacaan[$kode])) {
                    return "kode bacaan {$d['kode']} dipakai dua kali di sheet Bacaan; pakai kode yang berbeda";
                }

                if ($d['level_tocfl'] !== null && ! isset($this->level[(int) $d['level_tocfl']])) {
                    return "level_tocfl {$d['level_tocfl']} tidak ada di sistem";
                }

                // Bacaan yang sama persis (judul + teks) dipakai lagi, tidak dibuat ulang.
                $id = Paragraf::where('judul', $d['judul'])->where('hanzi', $d['hanzi'])->value('id');

                if ($id) {
                    $this->bacaan[$kode] = $id;
                    $this->ringkasan['bacaan_dipakai']++;
                    return null;
                }

                $baru = Paragraf::create([
                    'level_tocfl_id'         => $d['level_tocfl'] !== null ? $this->level[(int) $d['level_tocfl']] : null,
                    'judul'                  => $d['judul'],
                    'hanzi'                  => $d['hanzi'],
                    'pinyin'                 => $d['pinyin'],
                    'arti_indonesia'         => $d['arti_indonesia'],
                    'penjelasan_tata_bahasa' => $d['penjelasan_tata_bahasa'],
                ]);

                $this->bacaan[$kode] = $baru->id;
                $this->ringkasan['bacaan_baru']++;

                return null;
            },
        );
    }

    /* ---------- Sheet Soal ---------- */

    private function prosesSoal(): void
    {
        $this->olah(
            'soal',
            'Soal',
            self::KOLOM_SOAL,
            [
                'paket'                => ['nullable', 'string', 'max:500'],
                'bagian'               => ['required', 'string', Rule::in(Soal::BAGIAN)],
                'tipe'                 => ['nullable', 'string', Rule::in(array_keys(LabelSoal::TIPE))],
                'tingkat'              => ['nullable', 'integer', 'between:1,6'],
                'poin'                 => ['nullable', 'integer', 'between:1,255'],
                'urutan'               => ['nullable', 'integer', 'between:0,65535'],
                'kode_bacaan'          => ['nullable', 'string', 'max:100'],
                'paragraf'             => ['nullable', 'string'],
                'audio'                => ['nullable', 'string', 'max:1000'],
                'pertanyaan'           => ['required', 'string'],
                'pilihan_a'            => ['required', 'string', 'max:1000'],
                'pilihan_b'            => ['required', 'string', 'max:1000'],
                'pilihan_c'            => ['required', 'string', 'max:1000'],
                'pilihan_d'            => ['required', 'string', 'max:1000'],
                'jawaban_benar'        => ['required', 'string', Rule::in(['A', 'B', 'C', 'D'])],
                'penjelasan'           => ['nullable', 'string'],
                'kosakata_hanzi'       => ['nullable', 'string'],
                'kosakata_pinyin'      => ['nullable', 'string'],
                'karakter_hanzi'       => ['nullable', 'string'],
                'karakter_pinyin'      => ['nullable', 'string'],
                'radikal_hanzi'        => ['nullable', 'string'],
                'pola_tata_bahasa'     => ['nullable', 'string'],
                'contoh_kalimat_hanzi' => ['nullable', 'string'],
            ],
            fn (array $d) => $this->simpanSoal($d),
            function (array $d): array {
                $d = $this->normalisasiKolom($d, ['bagian', 'tipe']);

                // "a", "A.", "B)" -> "A", "B"
                if ($d['jawaban_benar'] !== null && preg_match('/^([A-D])[\.\):]?$/i', $d['jawaban_benar'], $m)) {
                    $d['jawaban_benar'] = strtoupper($m[1]);
                }

                return $d;
            },
        );
    }

    private function simpanSoal(array $d): ?string
    {
        // ---- paket tujuan
        $paket = $this->paketTujuan;

        if (! $paket) {
            if ($d['paket'] === null) {
                return 'kolom paket wajib diisi (atau pilih paket tujuan di halaman import)';
            }

            $paket = $this->paket[mb_strtolower($d['paket'])] ?? null;

            if (! $paket) {
                return "paket \"{$d['paket']}\" tidak ditemukan; tulis di sheet Paket atau samakan judulnya dengan paket yang sudah ada";
            }
        }

        // ---- bacaan
        $paragrafId = null;
        if ($d['kode_bacaan'] !== null) {
            $paragrafId = $this->bacaan[mb_strtolower($d['kode_bacaan'])] ?? null;

            if (! $paragrafId) {
                return "kode_bacaan {$d['kode_bacaan']} tidak ada di sheet Bacaan";
            }
        }

        if ($d['bagian'] === Soal::BAGIAN_READING && ! $paragrafId && $d['paragraf'] === null) {
            return 'soal Reading butuh bacaan: isi kode_bacaan (sheet Bacaan) atau kolom paragraf';
        }

        // ---- tautan ke item hafalan
        $kosakataId = $this->tautKosakata($d['kosakata_hanzi'], $d['kosakata_pinyin']);
        if (is_string($kosakataId)) {
            return $kosakataId;
        }

        $karakterId = $this->tautKarakter($d['karakter_hanzi'], $d['karakter_pinyin']);
        if (is_string($karakterId)) {
            return $karakterId;
        }

        $radikalId = $this->tautRadikal($d['radikal_hanzi']);
        if (is_string($radikalId)) {
            return $radikalId;
        }

        $polaId = $this->tautPola($d['pola_tata_bahasa']);
        if (is_string($polaId)) {
            return $polaId;
        }

        $contohId = $this->tautContoh($d['contoh_kalimat_hanzi'], $kosakataId);
        if (is_string($contohId)) {
            return $contohId;
        }

        // Tautan ke kosakata/karakter/radikal tidak lagi wajib: soal tanpa tautan
        // tetap diimpor, hanya saja tidak ikut memperbarui hafalan.
        $tipe = $d['tipe'];

        // ---- empat pilihan harus berbeda
        $pilihan = collect(['pilihan_a', 'pilihan_b', 'pilihan_c', 'pilihan_d'])
            ->map(fn ($k) => mb_strtolower($d[$k]));
        if ($pilihan->unique()->count() < 4) {
            return 'keempat pilihan harus berbeda satu sama lain';
        }

        // ---- soal yang sama persis di paket ini dilewati
        $sudah = Soal::where('grup_soal_id', $paket->id)
            ->where('pertanyaan', $d['pertanyaan'])
            ->where('pilihan_a', $d['pilihan_a'])
            ->where('pilihan_b', $d['pilihan_b'])
            ->where('pilihan_c', $d['pilihan_c'])
            ->where('pilihan_d', $d['pilihan_d'])
            ->exists();

        if ($sudah) {
            $this->ringkasan['soal_dilewati']++;
            return null;
        }

        Soal::create([
            'grup_soal_id'        => $paket->id,
            'kosakata_id'         => $kosakataId,
            'pola_tata_bahasa_id' => $polaId,
            'contoh_kalimat_id'   => $contohId,
            'paragraf_id'         => $paragrafId,
            'karakter_id'         => $karakterId,
            'radikal_id'          => $radikalId,
            'bagian'              => $d['bagian'],
            'tipe'                => $tipe,
            'tingkat'             => $d['tingkat'] !== null ? (int) $d['tingkat'] : $this->tingkatBawaan($paket),
            'sumber'              => $this->sumber,
            'status_validasi'     => $this->statusAwal,
            'dicek_pada'          => $this->statusAwal === Soal::STATUS_DISETUJUI ? now() : null,
            'urutan'              => $this->urutanBaru($paket, $d['urutan']),
            'poin'                => $d['poin'] !== null ? (int) $d['poin'] : 1,
            'paragraf'            => $d['paragraf'],
            'audio'               => $d['audio'],
            'pertanyaan'          => $d['pertanyaan'],
            'pilihan_a'           => $d['pilihan_a'],
            'pilihan_b'           => $d['pilihan_b'],
            'pilihan_c'           => $d['pilihan_c'],
            'pilihan_d'           => $d['pilihan_d'],
            'jawaban_benar'       => $d['jawaban_benar'],
            'penjelasan'          => $d['penjelasan'],
        ]);

        $this->ringkasan['soal_baru']++;
        $this->catatTambahan($paket);

        return null;
    }

    /** Tingkat soal kalau kolom tingkat kosong: ikut level paket, atau 1. */
    private function tingkatBawaan(GrupSoal $paket): int
    {
        if ($paket->level_tocfl_id) {
            $tingkat = array_search($paket->level_tocfl_id, $this->level, true);

            if ($tingkat !== false) {
                return (int) $tingkat;
            }
        }

        return 1;
    }

    /** Urutan dari Excel kalau diisi, kalau tidak: di akhir paket. */
    private function urutanBaru(GrupSoal $paket, ?string $dariExcel): int
    {
        $this->urutan[$paket->id] ??= (int) Soal::where('grup_soal_id', $paket->id)->max('urutan');

        if ($dariExcel !== null) {
            $this->urutan[$paket->id] = max($this->urutan[$paket->id], (int) $dariExcel);

            return (int) $dariExcel;
        }

        return ++$this->urutan[$paket->id];
    }

    private function catatTambahan(GrupSoal $paket): void
    {
        $this->tambahan[$paket->id] ??= ['id' => $paket->id, 'judul' => $paket->judul, 'jumlah' => 0];
        $this->tambahan[$paket->id]['jumlah']++;
    }

    /* ---------- Pencarian item (id, null kalau kolomnya kosong, atau string galat) ---------- */

    private function tautKosakata(?string $hanzi, ?string $pinyin): int|string|null
    {
        if ($hanzi === null) {
            return $pinyin !== null ? 'kosakata_pinyin diisi tanpa kosakata_hanzi' : null;
        }

        $kunci = $hanzi . '|' . $pinyin;

        if (! array_key_exists($kunci, $this->petaKosakata)) {
            $q = Kosakata::where('hanzi', $hanzi);
            if ($pinyin !== null) {
                $q->where('pinyin', $pinyin);
            }
            $this->petaKosakata[$kunci] = $q->limit(2)->pluck('id')->all();
        }

        $ids = $this->petaKosakata[$kunci];

        if (count($ids) === 1) {
            return $ids[0];
        }

        // Tidak ditemukan di database: soal tetap diimpor tanpa tautan kosakata.
        if ($ids === []) {
            return null;
        }

        return "kosakata {$hanzi} punya lebih dari satu bacaan; isi kolom kosakata_pinyin";
    }

    private function tautKarakter(?string $hanzi, ?string $pinyin): int|string|null
    {
        if ($hanzi === null) {
            return $pinyin !== null ? 'karakter_pinyin diisi tanpa karakter_hanzi' : null;
        }

        $kunci = $hanzi . '|' . $pinyin;

        if (! array_key_exists($kunci, $this->petaKarakter)) {
            $q = Karakter::where('hanzi', $hanzi);
            if ($pinyin !== null) {
                $q->where('pinyin', $pinyin);
            }
            $this->petaKarakter[$kunci] = $q->limit(2)->pluck('id')->all();
        }

        $ids = $this->petaKarakter[$kunci];

        if (count($ids) === 1) {
            return $ids[0];
        }

        // Tidak ditemukan di database: soal tetap diimpor tanpa tautan karakter.
        if ($ids === []) {
            return null;
        }

        return "karakter {$hanzi} punya lebih dari satu bacaan; isi kolom karakter_pinyin";
    }

    private function tautRadikal(?string $hanzi): int|string|null
    {
        if ($hanzi === null) {
            return null;
        }

        $ids = Radikal::where('hanzi', $hanzi)->limit(2)->pluck('id')->all();

        return match (count($ids)) {
            1       => $ids[0],
            0       => "radikal {$hanzi} tidak ditemukan",
            default => "radikal {$hanzi} ditemukan lebih dari satu kali di data radikal",
        };
    }

    private function tautPola(?string $judul): int|string|null
    {
        if ($judul === null) {
            return null;
        }

        $ids = PolaTataBahasa::where('judul', $judul)->limit(2)->pluck('id')->all();

        return match (count($ids)) {
            1       => $ids[0],
            0       => "pola tata bahasa \"{$judul}\" tidak ditemukan; judulnya harus sama persis",
            default => "pola tata bahasa \"{$judul}\" ditemukan lebih dari satu kali",
        };
    }

    private function tautContoh(?string $hanzi, int|string|null $kosakataId): int|string|null
    {
        if ($hanzi === null) {
            return null;
        }

        $q = ContohKalimat::where('hanzi', $hanzi);
        if (is_int($kosakataId)) {
            $q->where('kosakata_id', $kosakataId);
        }
        $ids = $q->limit(2)->pluck('id')->all();

        return match (count($ids)) {
            1       => $ids[0],
            0       => "contoh kalimat {$hanzi} tidak ditemukan; hanzi kalimat harus sama persis",
            default => "contoh kalimat {$hanzi} ditemukan lebih dari satu kali; isi kosakata_hanzi untuk mempersempit",
        };
    }

    /* ---------- Helper ---------- */

    private function ambil($row, array $kolom): array
    {
        $hasil = [];
        foreach ($kolom as $k) {
            $hasil[$k] = $this->bersih($row[$k] ?? null);
        }

        return $hasil;
    }

    /** Rapikan nilai sel: trim, kosong jadi null, angka desimal bulat (1.0) jadi "1". */
    private function bersih(mixed $nilai): ?string
    {
        if ($nilai === null) {
            return null;
        }
        if (is_float($nilai) && floor($nilai) == $nilai) {
            $nilai = (int) $nilai;
        }

        $nilai = trim((string) $nilai);

        return $nilai === '' ? null : $nilai;
    }

    /** "Tata Bahasa", "tata-bahasa", " TATA_BAHASA " -> "tata_bahasa". */
    private function normalisasiKolom(array $d, array $kolom): array
    {
        foreach ($kolom as $k) {
            if ($d[$k] !== null) {
                $d[$k] = preg_replace('/[\s\-]+/u', '_', mb_strtolower($d[$k]));
            }
        }

        return $d;
    }

    private function kosong(array $d): bool
    {
        return collect($d)->filter(fn ($v) => $v !== null)->isEmpty();
    }

    private function catatGagal(string $lokasi, array $pesan): void
    {
        $this->gagal++;

        if (count($this->galat) < self::MAKS_GALAT) {
            $this->galat[] = $lokasi . ': ' . implode('; ', $pesan) . '.';
        }
    }
}
