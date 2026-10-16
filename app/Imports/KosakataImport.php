<?php

namespace App\Imports;

use App\Models\ContohKalimat;
use App\Models\GrupKosakata;
use App\Models\Karakter;
use App\Models\Kategori;
use App\Models\Kosakata;
use App\Models\KosakataKarakter;
use App\Models\KosakataRelasi;
use App\Models\LevelTocfl;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Import;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/**
 * Import kosakata dari satu file Excel dengan sheet:
 *
 *   Kosakata          -> tabel kosakatas
 *   Contoh Kalimat    -> tabel contoh_kalimats
 *   Pecahan Karakter  -> tabel kosakata_karakter   (karakternya harus sudah ada di tabel karakters)
 *   Relasi            -> tabel kosakata_relasis
 *   Grup Kosakata     -> tabel grup_kosakatas + grup_kosakata_kosakata
 *
 * Semua sheet bersifat opsional. Excel::import() hanya MENGUMPULKAN baris;
 * selesai() yang memproses dengan urutan tetap (kosakata dulu, baru yang lain),
 * jadi urutan sheet di file tidak berpengaruh. Sheet lain (mis. "Petunjuk") diabaikan.
 *
 * Setiap baris diproses sendiri-sendiri: baris yang gagal dicatat, baris lain tetap masuk.
 */
class KosakataImport implements Import, WithMultipleSheets, SkipsUnknownSheets
{
    private const MAKS_GALAT = 100;

    /** nama sheet di Excel => kunci internal */
    private const SHEET = [
        'Kosakata'         => 'kosakata',
        'Contoh Kalimat'   => 'contoh',
        'Pecahan Karakter' => 'pecahan',
        'Relasi'           => 'relasi',
        'Grup Kosakata'    => 'grup',
    ];

    private const PESAN = [
        'required' => 'kolom :attribute wajib diisi',
        'max'      => 'kolom :attribute terlalu panjang (maks. :max karakter)',
        'integer'  => 'kolom :attribute harus berupa angka bulat',
        'min'      => 'kolom :attribute tidak boleh kurang dari :min',
        'in'       => 'kolom :attribute berisi nilai yang tidak dikenal (lihat daftar di halaman import)',
    ];

    /** @var array<string, Collection> kunci sheet => baris mentah */
    private array $baris = [];

    private array $ringkasan = [
        'kosakata_baru'       => 0,
        'kosakata_diperbarui' => 0,
        'kosakata_dilewati'   => 0,
        'kategori_baru'       => 0,
        'contoh_baru'         => 0,
        'contoh_diperbarui'   => 0,
        'contoh_dilewati'     => 0,
        'pecahan_baru'        => 0,
        'pecahan_diperbarui'  => 0,
        'pecahan_dilewati'    => 0,
        'relasi_baru'         => 0,
        'relasi_dilewati'     => 0,
        'grup_baru'           => 0,
        'anggota_baru'        => 0,
        'anggota_dilewati'    => 0,
    ];

    private int $gagal = 0;
    private int $jumlahBaris = 0;
    private array $galat = [];

    /** "hanzi|pinyin" => id kosakata (null kalau tidak ada) */
    private array $peta = [];
    /** "hanzi|pinyin" => id karakter (null kalau tidak ada) */
    private array $petaKarakter = [];
    /** nama kategori (huruf kecil) => id */
    private array $kategori = [];
    /** angka tingkat => id */
    private array $level = [];
    /** nama grup (huruf kecil) => GrupKosakata */
    private array $grup = [];
    private int $urutanBerikut = 1;

    public function __construct(private bool $perbarui = true)
    {
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
            public function __construct(private KosakataImport $induk, private string $kunci) {}

            public function collection(Collection $rows): void
            {
                $this->induk->terima($this->kunci, $rows);
            }
        };
    }

    /* ---------- Dipanggil controller ---------- */

    public function selesai(): void
    {
        $this->kategori = Kategori::pluck('id', 'nama')
            ->mapWithKeys(fn ($id, $nama) => [mb_strtolower($nama) => $id])
            ->all();
        $this->level = LevelTocfl::pluck('id', 'tingkat')->all();
        $this->grup  = GrupKosakata::all()
            ->keyBy(fn ($g) => mb_strtolower($g->nama))
            ->all();
        $this->urutanBerikut = (int) Kosakata::max('urutan') + 1;

        $this->prosesKosakata();
        $this->prosesContoh();
        $this->prosesPecahan();
        $this->prosesRelasi();
        $this->prosesGrup();
    }

    public function adaIsi(): bool
    {
        return $this->jumlahBaris > 0;
    }

    public function ringkasan(): array
    {
        return $this->ringkasan + ['gagal' => $this->gagal, 'galat' => $this->galat];
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

    /* ---------- Sheet Kosakata ---------- */

    private function prosesKosakata(): void
    {
        $this->olah(
            'kosakata',
            'Kosakata',
            [
                'hanzi', 'pinyin', 'zhuyin', 'arti_indonesia', 'arti_singkat', 'baca_indonesia',
                'english', 'kegunaan', 'kata_pengait', 'jenis_kata', 'sub_jenis',
                'kategori', 'level_tocfl', 'urutan',
            ],
            [
                'hanzi'          => ['required', 'string', 'max:20'],
                'pinyin'         => ['required', 'string', 'max:60'],
                'zhuyin'         => ['nullable', 'string', 'max:100'],
                'arti_indonesia' => ['nullable', 'string', 'max:150'],
                'arti_singkat'   => ['nullable', 'string', 'max:100'],
                'baca_indonesia' => ['nullable', 'string', 'max:60'],
                'english'        => ['nullable', 'string', 'max:150'],
                'kegunaan'       => ['nullable', 'string', 'max:2000'],
                'kata_pengait'   => ['nullable', 'string', 'max:1000'],
                'jenis_kata'     => ['nullable', 'string', Rule::in(Kosakata::JENIS_KATA)],
                'sub_jenis'      => ['nullable', 'string', Rule::in(Kosakata::SUB_JENIS)],
                'kategori'       => ['nullable', 'string', 'max:50'],
                'level_tocfl'    => ['nullable', 'integer'],
                'urutan'         => ['nullable', 'integer', 'min:0'],
            ],
            function (array $d): ?string {
                if ($d['level_tocfl'] !== null && ! isset($this->level[(int) $d['level_tocfl']])) {
                    return "level_tocfl {$d['level_tocfl']} tidak ada di sistem";
                }

                $this->simpanKosakata($d);

                return null;
            },
            fn (array $d) => $this->normalisasiKolom($d, ['jenis_kata', 'sub_jenis']),
        );
    }

    private function simpanKosakata(array $d): void
    {
        $kunci = $d['hanzi'] . '|' . $d['pinyin'];

        $ada = Kosakata::where('hanzi', $d['hanzi'])->where('pinyin', $d['pinyin'])->first();

        if ($ada) {
            $this->peta[$kunci] = $ada->id;

            if (! $this->perbarui) {
                $this->ringkasan['kosakata_dilewati']++;
                return;
            }

            // Sel kosong di Excel tidak menghapus data lama.
            $ada->update($this->isiKosakata($d));
            $this->ringkasan['kosakata_diperbarui']++;
            return;
        }

        $isi = $this->isiKosakata($d);
        $isi['urutan'] ??= $this->urutanBerikut++;

        $baru = Kosakata::create($isi + ['hanzi' => $d['hanzi'], 'pinyin' => $d['pinyin']]);
        $this->peta[$kunci] = $baru->id;
        $this->ringkasan['kosakata_baru']++;
    }

    /** Kolom yang boleh diubah, hanya yang terisi. */
    private function isiKosakata(array $d): array
    {
        $isi = [
            'zhuyin'         => $d['zhuyin'],
            'arti_indonesia' => $d['arti_indonesia'],
            'arti_singkat'   => $d['arti_singkat'],
            'baca_indonesia' => $d['baca_indonesia'],
            'english'        => $d['english'],
            'kegunaan'       => $d['kegunaan'],
            'kata_pengait'   => $d['kata_pengait'],
            'jenis_kata'     => $d['jenis_kata'],
            'sub_jenis'      => $d['sub_jenis'],
            'kategori_id'    => $d['kategori'] !== null ? $this->idKategori($d['kategori']) : null,
            'level_tocfl_id' => $d['level_tocfl'] !== null ? $this->level[(int) $d['level_tocfl']] : null,
            'urutan'         => $d['urutan'] !== null ? (int) $d['urutan'] : null,
        ];

        return array_filter($isi, fn ($nilai) => $nilai !== null);
    }

    private function idKategori(string $nama): int
    {
        $kunci = mb_strtolower($nama);

        if (! isset($this->kategori[$kunci])) {
            $this->kategori[$kunci] = Kategori::create(['nama' => $nama])->id;
            $this->ringkasan['kategori_baru']++;
        }

        return $this->kategori[$kunci];
    }

    /* ---------- Sheet Contoh Kalimat ---------- */

    private function prosesContoh(): void
    {
        $this->olah(
            'contoh',
            'Contoh Kalimat',
            ['kosakata_hanzi', 'kosakata_pinyin', 'kalimat_hanzi', 'kalimat_pinyin', 'kalimat_arti', 'catatan_tata_bahasa'],
            [
                'kosakata_hanzi'      => ['required', 'string'],
                'kosakata_pinyin'     => ['required', 'string'],
                'kalimat_hanzi'       => ['required', 'string'],
                'kalimat_pinyin'      => ['required', 'string'],
                'kalimat_arti'        => ['required', 'string'],
                'catatan_tata_bahasa' => ['nullable', 'string'],
            ],
            function (array $d): ?string {
                $kosakataId = $this->cariKosakata($d['kosakata_hanzi'], $d['kosakata_pinyin']);

                if (! $kosakataId) {
                    return $this->pesanKosakataHilang($d['kosakata_hanzi'], $d['kosakata_pinyin']);
                }

                $this->simpanContoh($kosakataId, $d);

                return null;
            },
        );
    }

    private function simpanContoh(int $kosakataId, array $d): void
    {
        $ada = ContohKalimat::where('kosakata_id', $kosakataId)
            ->where('hanzi', $d['kalimat_hanzi'])
            ->first();

        if ($ada) {
            if (! $this->perbarui) {
                $this->ringkasan['contoh_dilewati']++;
                return;
            }

            // hanzi kalimat sama, jadi susunan & pengecoh yang sudah ada tetap cocok.
            $ada->update(array_filter([
                'pinyin'              => $d['kalimat_pinyin'],
                'arti_indonesia'      => $d['kalimat_arti'],
                'catatan_tata_bahasa' => $d['catatan_tata_bahasa'],
            ], fn ($nilai) => $nilai !== null));
            $this->ringkasan['contoh_diperbarui']++;
            return;
        }

        ContohKalimat::create([
            'kosakata_id'         => $kosakataId,
            'hanzi'               => $d['kalimat_hanzi'],
            'pinyin'              => $d['kalimat_pinyin'],
            'arti_indonesia'      => $d['kalimat_arti'],
            'catatan_tata_bahasa' => $d['catatan_tata_bahasa'],
        ]);
        $this->ringkasan['contoh_baru']++;
    }

    /* ---------- Sheet Pecahan Karakter ---------- */

    private function prosesPecahan(): void
    {
        $this->olah(
            'pecahan',
            'Pecahan Karakter',
            [
                'kosakata_hanzi', 'kosakata_pinyin', 'urutan',
                'karakter_hanzi', 'karakter_pinyin',
                'pinyin_di_kata', 'arti_di_kata', 'penjelasan',
            ],
            [
                'kosakata_hanzi'  => ['required', 'string'],
                'kosakata_pinyin' => ['required', 'string'],
                'urutan'          => ['required', 'integer', 'min:1', 'max:255'],
                'karakter_hanzi'  => ['required', 'string', 'max:8'],
                'karakter_pinyin' => ['required', 'string', 'max:255'],
                'pinyin_di_kata'  => ['nullable', 'string', 'max:100'],
                'arti_di_kata'    => ['nullable', 'string'],
                'penjelasan'      => ['nullable', 'string'],
            ],
            function (array $d): ?string {
                $kosakataId = $this->cariKosakata($d['kosakata_hanzi'], $d['kosakata_pinyin']);

                if (! $kosakataId) {
                    return $this->pesanKosakataHilang($d['kosakata_hanzi'], $d['kosakata_pinyin']);
                }

                $karakterId = $this->cariKarakter($d['karakter_hanzi'], $d['karakter_pinyin']);

                if (! $karakterId) {
                    return "karakter {$d['karakter_hanzi']} ({$d['karakter_pinyin']}) belum ada di data karakter; tambahkan karakternya dulu (hanzi dan pinyin harus sama persis)";
                }

                $this->simpanPecahan($kosakataId, $karakterId, $d);

                return null;
            },
        );
    }

    private function simpanPecahan(int $kosakataId, int $karakterId, array $d): void
    {
        $urutan = (int) $d['urutan'];

        $isi = array_filter([
            'pinyin_di_kata' => $d['pinyin_di_kata'],
            'arti_di_kata'   => $d['arti_di_kata'],
            'penjelasan'     => $d['penjelasan'],
        ], fn ($nilai) => $nilai !== null);

        // Yang unik di database: satu kosakata + satu urutan.
        $ada = KosakataKarakter::where('kosakata_id', $kosakataId)->where('urutan', $urutan)->first();

        if ($ada) {
            if (! $this->perbarui) {
                $this->ringkasan['pecahan_dilewati']++;
                return;
            }

            $ada->update(['karakter_id' => $karakterId] + $isi);
            $this->ringkasan['pecahan_diperbarui']++;
            return;
        }

        KosakataKarakter::create(
            ['kosakata_id' => $kosakataId, 'karakter_id' => $karakterId, 'urutan' => $urutan] + $isi
        );
        $this->ringkasan['pecahan_baru']++;
    }

    /* ---------- Sheet Relasi ---------- */

    private function prosesRelasi(): void
    {
        $this->olah(
            'relasi',
            'Relasi',
            ['kosakata_hanzi', 'kosakata_pinyin', 'terkait_hanzi', 'terkait_pinyin', 'jenis'],
            [
                'kosakata_hanzi'  => ['required', 'string'],
                'kosakata_pinyin' => ['required', 'string'],
                'terkait_hanzi'   => ['required', 'string'],
                'terkait_pinyin'  => ['required', 'string'],
                'jenis'           => ['required', 'string', Rule::in(KosakataRelasi::JENIS)],
            ],
            function (array $d): ?string {
                $a = $this->cariKosakata($d['kosakata_hanzi'], $d['kosakata_pinyin']);
                if (! $a) {
                    return $this->pesanKosakataHilang($d['kosakata_hanzi'], $d['kosakata_pinyin']);
                }

                $b = $this->cariKosakata($d['terkait_hanzi'], $d['terkait_pinyin']);
                if (! $b) {
                    return $this->pesanKosakataHilang($d['terkait_hanzi'], $d['terkait_pinyin']);
                }

                if ($a === $b) {
                    return 'kosakata tidak boleh berelasi dengan dirinya sendiri';
                }

                // Relasi dibaca dua arah (Kosakata::idRelasi), jadi kalau sudah tercatat
                // dari sisi sebaliknya, tidak perlu dicatat lagi.
                $sudah = KosakataRelasi::where('jenis', $d['jenis'])
                    ->where(function ($q) use ($a, $b) {
                        $q->where(fn ($x) => $x->where('kosakata_id', $a)->where('kosakata_terkait_id', $b))
                          ->orWhere(fn ($x) => $x->where('kosakata_id', $b)->where('kosakata_terkait_id', $a));
                    })
                    ->exists();

                if ($sudah) {
                    $this->ringkasan['relasi_dilewati']++;
                    return null;
                }

                KosakataRelasi::create([
                    'kosakata_id'         => $a,
                    'kosakata_terkait_id' => $b,
                    'jenis'               => $d['jenis'],
                ]);
                $this->ringkasan['relasi_baru']++;

                return null;
            },
            fn (array $d) => $this->normalisasiKolom($d, ['jenis']),
        );
    }

    /* ---------- Sheet Grup Kosakata ---------- */

    private function prosesGrup(): void
    {
        $this->olah(
            'grup',
            'Grup Kosakata',
            ['grup_nama', 'grup_keterangan', 'kosakata_hanzi', 'kosakata_pinyin'],
            [
                'grup_nama'       => ['required', 'string', 'max:255'],
                'grup_keterangan' => ['nullable', 'string'],
                'kosakata_hanzi'  => ['required', 'string'],
                'kosakata_pinyin' => ['required', 'string'],
            ],
            function (array $d): ?string {
                $kosakataId = $this->cariKosakata($d['kosakata_hanzi'], $d['kosakata_pinyin']);

                // Dicek dulu supaya grup kosong tidak terbuat kalau kosakatanya tidak ada.
                if (! $kosakataId) {
                    return $this->pesanKosakataHilang($d['kosakata_hanzi'], $d['kosakata_pinyin']);
                }

                $kunci = mb_strtolower($d['grup_nama']);

                if (! isset($this->grup[$kunci])) {
                    $this->grup[$kunci] = GrupKosakata::create([
                        'nama'       => $d['grup_nama'],
                        'keterangan' => $d['grup_keterangan'],
                    ]);
                    $this->ringkasan['grup_baru']++;
                }

                $hasil = $this->grup[$kunci]->kosakatas()->syncWithoutDetaching([$kosakataId]);

                if (! empty($hasil['attached'])) {
                    $this->ringkasan['anggota_baru']++;
                } else {
                    $this->ringkasan['anggota_dilewati']++;
                }

                return null;
            },
        );
    }

    /* ---------- Pencarian ---------- */

    private function cariKosakata(string $hanzi, string $pinyin): ?int
    {
        $kunci = $hanzi . '|' . $pinyin;

        if (! array_key_exists($kunci, $this->peta)) {
            $this->peta[$kunci] = Kosakata::where('hanzi', $hanzi)->where('pinyin', $pinyin)->value('id');
        }

        return $this->peta[$kunci];
    }

    private function cariKarakter(string $hanzi, string $pinyin): ?int
    {
        $kunci = $hanzi . '|' . $pinyin;

        if (! array_key_exists($kunci, $this->petaKarakter)) {
            $this->petaKarakter[$kunci] = Karakter::where('hanzi', $hanzi)->where('pinyin', $pinyin)->value('id');
        }

        return $this->petaKarakter[$kunci];
    }

    private function pesanKosakataHilang(string $hanzi, string $pinyin): string
    {
        return "kosakata {$hanzi} ({$pinyin}) tidak ditemukan; hanzi dan pinyin harus sama persis";
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

    /** "Kata Benda", "kata-benda", " KATA_BENDA " -> "kata_benda". */
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
