<?php

namespace App\Imports;

use App\Exports\RadikalExport;
use App\Http\Requests\RadikalRequest;
use App\Models\Radikal;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Import radikal dari Excel (sheet "Radikal", kalau tidak ada dipakai sheet pertama).
 *
 * Dipakai:
 *   $hasil = (new RadikalImport('perbarui'))->import($request->file('file')->getRealPath());
 *
 * Mode: 'perbarui' (data lama ditimpa dengan sel yang terisi) | 'lewati' (hanya tambah yang baru).
 * Memakai PhpSpreadsheet langsung (ikut terpasang bersama maatwebsite/excel).
 */
class RadikalImport
{
    public const WAJIB = ['hanzi', 'pinyin', 'arti_indonesia'];

    private const MAKS_BARIS = 5000;
    private const MAKS_GALAT = 200;

    public function __construct(private string $mode = 'perbarui')
    {
    }

    // ------------------------------------------------------------------ IMPORT

    /**
     *      * @return array{radikal_baru:int, radikal_diperbarui:int, radikal_dilewati:int, gagal:int, galat:array<int,string>}
     *
     * @throws \RuntimeException kalau struktur file salah (pesannya aman ditampilkan).
     */
    public function import(string $path): array
    {
        $hasil = [
            'radikal_baru'       => 0,
            'radikal_diperbarui' => 0,
            'radikal_dilewati'   => 0,
            'gagal'              => 0,
            'galat'              => [],
        ];

        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);
        $buku  = $reader->load($path);
        $sheet = $buku->getSheetByName(RadikalExport::SHEET) ?? $buku->getSheet(0);

        $semua = $sheet->toArray(null, true, true, false);
        if (count($semua) < 2) {
            throw new \RuntimeException('File kosong: tidak ada baris data di bawah judul kolom.');
        }
        if (count($semua) > self::MAKS_BARIS + 1) {
            throw new \RuntimeException('Terlalu banyak baris (maks. ' . self::MAKS_BARIS . ' per file).');
        }

        $header = array_map(
            fn ($h) => Str::of((string) ($h ?? ''))->trim()->lower()->replaceMatches('/\s+/', '_')->toString(),
            array_shift($semua)
        );

        $kurang = array_diff(self::WAJIB, $header);
        if ($kurang) {
            throw new \RuntimeException(
                'Judul kolom wajib tidak ditemukan di baris 1: ' . implode(', ', $kurang)
                . '. Pakai template dari tombol "Unduh Template".'
            );
        }

        $terlihat = [];

        foreach ($semua as $i => $baris) {
            $noBaris = $i + 2; // baris 1 = header
            $data    = [];

            foreach ($header as $c => $nama) {
                if ($nama === '' || ! in_array($nama, RadikalExport::KOLOM, true)) {
                    continue;
                }
                $nilai = $baris[$c] ?? null;
                $nilai = $nilai === null ? null : trim((string) $nilai);
                $data[$nama] = $nilai === '' ? null : $nilai;
            }

            // baris kosong total: lewati tanpa menghitung
            if (! array_filter($data, fn ($v) => $v !== null)) {
                continue;
            }

            $hanzi = $data['hanzi'] ?? null;
            if ($hanzi === null) {
                $this->galat($hasil, "Baris {$noBaris}: kolom hanzi kosong.");
                continue;
            }
            if (isset($terlihat[$hanzi])) {
                $this->galat($hasil, "Baris {$noBaris} ({$hanzi}): hanzi sama dengan baris {$terlihat[$hanzi]} di file ini.");
                continue;
            }
            $terlihat[$hanzi] = $noBaris;

            $ada = Radikal::where('hanzi', $hanzi)->first();

            if ($ada && $this->mode === 'lewati') {
                $hasil['radikal_dilewati']++;
                continue;
            }

            // Nilai dari Excel (hanya kolom yang terisi).
            $isi = ['hanzi' => $hanzi];
            foreach (['nomor_kangxi', 'jumlah_goresan', 'urutan'] as $k) {
                if (($data[$k] ?? null) !== null) {
                    $isi[$k] = $this->angka($data[$k]);
                }
            }
            foreach (['pinyin', 'arti_indonesia', 'keterangan'] as $k) {
                if (($data[$k] ?? null) !== null) {
                    $isi[$k] = $data[$k];
                }
            }
            $varian = RadikalRequest::pecahVarian($data['varian'] ?? null);
            if ($varian) {
                $isi['varian'] = $varian;
            }

            // Keadaan akhir = data lama (kalau ada) ditimpa isi Excel. Yang divalidasi keadaan akhir ini.
            $akhir = $ada
                ? array_merge($ada->only(RadikalExport::KOLOM), $isi)
                : array_merge(array_fill_keys(RadikalExport::KOLOM, null), $isi);

            $cek = Validator::make($akhir, RadikalRequest::aturan($ada?->id), RadikalRequest::pesan(), RadikalRequest::atribut());
            if ($cek->fails()) {
                $this->galat($hasil, "Baris {$noBaris} ({$hanzi}): " . implode(' ', $cek->errors()->all()));
                continue;
            }

            $akhir['varian'] = $akhir['varian'] ?: null;

            try {
                if ($ada) {
                    $ada->fill($akhir);
                    if ($ada->isDirty()) {
                        $ada->save();
                        $hasil['radikal_diperbarui']++;
                    } else {
                        $hasil['radikal_dilewati']++;
                    }
                } else {
                    $akhir['urutan'] ??= ((int) Radikal::max('urutan')) + 1;
                    Radikal::create($akhir);
                    $hasil['radikal_baru']++;
                }
            } catch (\Throwable $e) {
                report($e);
                $this->galat($hasil, "Baris {$noBaris} ({$hanzi}): gagal disimpan ke database.");
            }
        }

        return $hasil;
    }

    // ------------------------------------------------------------------ HELPER

    private function galat(array &$hasil, string $pesan): void
    {
        $hasil['gagal']++;
        if (count($hasil['galat']) < self::MAKS_GALAT) {
            $hasil['galat'][] = $pesan;
        } elseif (count($hasil['galat']) === self::MAKS_GALAT) {
            $hasil['galat'][] = '... dan galat lainnya (hanya ' . self::MAKS_GALAT . ' pertama yang ditampilkan).';
        }
    }

    /** "85" / "85.0" / 85.0 -> 85 ; selain angka dibiarkan (nanti gagal validasi). */
    private function angka(mixed $nilai): mixed
    {
        if (is_numeric($nilai) && (float) $nilai == (int) $nilai) {
            return (int) $nilai;
        }

        return $nilai;
    }
}
