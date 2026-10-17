<?php

namespace App\Exports;

use App\Imports\SoalImport;
use App\Models\Soal;
use App\Support\LabelSoal;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Template Excel untuk SoalImport.
 * Nama sheet & judul kolom harus persis (lihat sheet "Petunjuk" di file hasil unduhan).
 * Sheet Paket dan Bacaan opsional; sheet Petunjuk diabaikan saat import.
 */
class SoalTemplateExport implements Export, WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            $this->lembar('Soal', SoalImport::KOLOM_SOAL, $this->contohSoal()),
            $this->lembar('Paket', SoalImport::KOLOM_PAKET, [
                ['Latihan Reading TOCFL 1 - Keluarga', 'Latihan membaca teks pendek tentang keluarga.', 'latihan', 1, '', '', '', ''],
            ]),
            $this->lembar('Bacaan', SoalImport::KOLOM_BACAAN, [
                [
                    'B1', '我的家',
                    '我家有四個人：爸爸、媽媽、哥哥和我。爸爸是老師，媽媽是醫生，哥哥是學生。',
                    'Wǒ jiā yǒu sì ge rén: bàba, māma, gēge hé wǒ. Bàba shì lǎoshī, māma shì yīshēng, gēge shì xuéshēng.',
                    'Keluarga saya ada empat orang: ayah, ibu, kakak laki-laki, dan saya. Ayah adalah guru, ibu adalah dokter, kakak adalah pelajar.',
                    '', 1,
                ],
            ]),
            $this->lembar('Petunjuk', ['sheet', 'kolom', 'wajib', 'keterangan'], $this->petunjuk()),
        ];
    }

    private function contohSoal(): array
    {
        $kosong = array_fill_keys(SoalImport::KOLOM_SOAL, '');

        $baris = [
            [
                'paket' => 'Latihan Reading TOCFL 1 - Keluarga', 'bagian' => 'reading', 'tingkat' => 1,
                'kode_bacaan' => 'B1', 'pertanyaan' => '誰是老師？',
                'pilihan_a' => '爸爸', 'pilihan_b' => '媽媽', 'pilihan_c' => '哥哥', 'pilihan_d' => '我',
                'jawaban_benar' => 'A', 'penjelasan' => '爸爸是老師。',
            ],
            [
                'paket' => 'Latihan Reading TOCFL 1 - Keluarga', 'bagian' => 'reading', 'tingkat' => 1,
                'kode_bacaan' => 'B1', 'pertanyaan' => '這個家有幾個人？',
                'pilihan_a' => '三個', 'pilihan_b' => '四個', 'pilihan_c' => '五個', 'pilihan_d' => '六個',
                'jawaban_benar' => 'B', 'penjelasan' => '爸爸、媽媽、哥哥和我，一共四個人。',
            ],
            [
                'paket' => 'Latihan Kosakata TOCFL 1', 'bagian' => 'kosakata', 'tipe' => 'hanzi_ke_arti', 'tingkat' => 1,
                'pertanyaan' => 'Apa arti kata 「老師」?',
                'pilihan_a' => 'Dokter', 'pilihan_b' => 'Guru', 'pilihan_c' => 'Pelajar', 'pilihan_d' => 'Teman',
                'jawaban_benar' => 'B',
                'kosakata_hanzi' => '老師', 'kosakata_pinyin' => 'lǎoshī',
            ],
        ];

        return array_map(fn (array $b) => array_values(array_merge($kosong, $b)), $baris);
    }

    private function petunjuk(): array
    {
        $bagian = implode(', ', array_keys(LabelSoal::BAGIAN));
        $tipe   = implode(', ', array_keys(LabelSoal::TIPE));
        $jenis  = implode(', ', array_keys(LabelSoal::JENIS));

        return [
            ['(umum)', '', '', 'Hapus baris contoh di setiap sheet sebelum mengisi data sendiri. Sheet Paket dan Bacaan boleh dihapus kalau tidak dipakai.'],
            ['(umum)', '', '', 'Urutan sheet tidak berpengaruh: Paket diproses dulu, lalu Bacaan, lalu Soal. Baris yang gagal dilaporkan, baris lain tetap masuk.'],
            ['(umum)', '', '', 'Soal yang persis sama (pertanyaan + keempat pilihan) di paket yang sama dilewati, jadi aman diimpor ulang.'],
            ['(umum)', '', '', 'Soal hasil import berstatus "Perlu dicek" (atau sesuai pilihan di halaman import). Periksa lalu setujui di halaman detail paket.'],

            ['Soal', 'paket', 'Ya*', 'Judul paket (sama dengan sheet Paket atau paket yang sudah ada). *Boleh kosong kalau paket tujuan dipilih di halaman import'],
            ['Soal', 'bagian', 'Ya', $bagian],
            ['Soal', 'tipe', '-', $tipe],
            ['Soal', 'tingkat', '-', 'Angka 1 sampai 6. Kosong = ikut level paket, atau 1'],
            ['Soal', 'poin', '-', 'Kosong = 1'],
            ['Soal', 'urutan', '-', 'Kosong = otomatis di akhir paket'],
            ['Soal', 'kode_bacaan', '-', 'Kode dari sheet Bacaan. Beberapa soal boleh memakai kode yang sama'],
            ['Soal', 'paragraf', '-', 'Teks pendek khusus untuk soal ini (alternatif kode_bacaan)'],
            ['Soal', 'audio', '-', 'Path atau URL file audio (soal Listening)'],
            ['Soal', 'pertanyaan', 'Ya', ''],
            ['Soal', 'pilihan_a … pilihan_d', 'Ya', 'Keempat pilihan harus berbeda'],
            ['Soal', 'jawaban_benar', 'Ya', 'A, B, C, atau D'],
            ['Soal', 'penjelasan', '-', 'Ditampilkan setelah soal dijawab'],
            ['Soal', 'kosakata_hanzi, kosakata_pinyin', '-', 'Wajib untuk tipe ' . implode(', ', Soal::TIPE_KOSAKATA) . '. Pinyin boleh kosong kalau hanzi-nya hanya punya satu bacaan'],
            ['Soal', 'karakter_hanzi, karakter_pinyin', '-', 'Wajib untuk tipe karakter_ke_arti, arti_ke_karakter, karakter_ke_radikal, arti_karakter_dalam_kata'],
            ['Soal', 'radikal_hanzi', '-', 'Wajib untuk tipe radikal_ke_arti'],
            ['Soal', 'pola_tata_bahasa', '-', 'Judul pola tata bahasa, harus sama persis dengan yang ada di sistem'],
            ['Soal', 'contoh_kalimat_hanzi', '-', 'Hanzi kalimat contoh yang sudah ada (untuk soal tata bahasa)'],

            ['Paket', 'judul', 'Ya', 'Paket dibuat kalau judulnya belum ada; kalau sudah ada, dipakai apa adanya'],
            ['Paket', 'deskripsi', '-', ''],
            ['Paket', 'jenis', '-', $jenis . '. Kosong = latihan'],
            ['Paket', 'level_tocfl', '-', 'Angka 1 sampai 6'],
            ['Paket', 'grup_kosakata', '-', 'Nama grup kosakata yang sudah ada'],
            ['Paket', 'durasi_menit, maks_percobaan', '-', 'Kosong = tanpa batas'],
            ['Paket', 'nilai_lulus', '-', '0-100, hanya berlaku untuk jenis ujian'],

            ['Bacaan', 'kode', 'Ya', 'Kode bebas (mis. B1), dipakai kolom kode_bacaan di sheet Soal. Tidak boleh kembar'],
            ['Bacaan', 'judul, hanzi, pinyin, arti_indonesia', 'Ya', 'Isi teks bacaan'],
            ['Bacaan', 'penjelasan_tata_bahasa, level_tocfl', '-', ''],
        ];
    }

    private function lembar(string $judul, array $kepala, array $baris): object
    {
        return new class($judul, $kepala, $baris) implements FromArray, WithHeadings, WithTitle, WithStyles, ShouldAutoSize {
            public function __construct(
                private string $judul,
                private array $kepala,
                private array $baris,
            ) {}

            public function array(): array
            {
                return $this->baris;
            }

            public function headings(): array
            {
                return $this->kepala;
            }

            public function title(): string
            {
                return $this->judul;
            }

            public function styles(Worksheet $sheet): array
            {
                return [
                    1 => [
                        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1A73E8']],
                    ],
                ];
            }
        };
    }
}
