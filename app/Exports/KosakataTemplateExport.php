<?php

namespace App\Exports;

use App\Models\Kosakata;
use App\Models\KosakataRelasi;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Template Excel untuk KosakataImport.
 * Nama sheet & judul kolom harus persis (lihat sheet "Petunjuk" di file hasil unduhan).
 * Semua sheet selain "Kosakata" opsional: yang tidak dipakai boleh dihapus dari file.
 */
class KosakataTemplateExport implements Export, WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            $this->lembar(
                'Kosakata',
                ['hanzi', 'pinyin', 'zhuyin', 'arti_indonesia', 'arti_singkat', 'baca_indonesia', 'english', 'kegunaan', 'kata_pengait', 'jenis_kata', 'sub_jenis', 'kategori', 'level_tocfl', 'urutan'],
                [
                    ['老師', 'lǎoshī', 'ㄌㄠˇ ㄕ', 'Guru', 'Guru', '', 'Teacher', 'Menyebut atau memanggil pengajar di sekolah.', 'Guru = 老 (tua / berpengalaman) + 師 (pengajar / ahli).', 'kata_benda', '', 'Pendidikan', 1, ''],
                ]
            ),
            $this->lembar(
                'Contoh Kalimat',
                ['kosakata_hanzi', 'kosakata_pinyin', 'kalimat_hanzi', 'kalimat_pinyin', 'kalimat_arti', 'catatan_tata_bahasa'],
                [
                    ['老師', 'lǎoshī', '我的老師很好。', 'Wǒ de lǎoshī hěn hǎo.', 'Guru saya baik.', '很 (hěn) = sangat; dipakai di depan kata sifat'],
                    ['老師', 'lǎoshī', '她是中文老師。', 'Tā shì Zhōngwén lǎoshī.', 'Dia adalah guru bahasa Mandarin.', '是 (shì) = adalah'],
                ]
            ),
            // Sengaja kosong: pecahan butuh karakter yang sudah ada di data karakter,
            // dan relasi butuh dua kosakata. Format kolomnya ada di sheet Petunjuk.
            $this->lembar(
                'Pecahan Karakter',
                ['kosakata_hanzi', 'kosakata_pinyin', 'urutan', 'karakter_hanzi', 'karakter_pinyin', 'pinyin_di_kata', 'arti_di_kata', 'penjelasan'],
                []
            ),
            $this->lembar(
                'Relasi',
                ['kosakata_hanzi', 'kosakata_pinyin', 'terkait_hanzi', 'terkait_pinyin', 'jenis'],
                []
            ),
            $this->lembar(
                'Grup Kosakata',
                ['grup_nama', 'grup_keterangan', 'kosakata_hanzi', 'kosakata_pinyin'],
                [
                    ['Pendidikan dasar', 'Kata-kata dasar seputar sekolah', '老師', 'lǎoshī'],
                ]
            ),
            $this->lembar(
                'Petunjuk',
                ['sheet', 'kolom', 'wajib', 'keterangan'],
                $this->petunjuk()
            ),
        ];
    }

    private function petunjuk(): array
    {
        $jenisKata   = implode(', ', Kosakata::JENIS_KATA);
        $subJenis    = implode(', ', Kosakata::SUB_JENIS);
        $jenisRelasi = implode(', ', KosakataRelasi::JENIS);

        return [
            ['(umum)', '', '', 'Hapus baris contoh di setiap sheet sebelum mengisi data sendiri. Sheet selain Kosakata boleh dihapus kalau tidak dipakai.'],
            ['(umum)', '', '', 'Urutan sheet tidak berpengaruh. Kosakata diproses lebih dulu, lalu sheet lainnya. Satu kosakata dikenali dari hanzi + pinyin yang sama persis.'],
            ['(umum)', '', '', 'Sel kosong tidak menghapus data lama. Huruf besar/kecil dan tanda nada pada pinyin dibedakan.'],
            ['(umum)', '', '', 'Sheet Pecahan Karakter dan Relasi sengaja kosong di template ini. Isi sesuai kolom di bawah (pecahan butuh karakter yang sudah ada; relasi butuh dua kosakata).'],

            ['Kosakata', 'hanzi', 'Ya', 'Maks. 20 karakter'],
            ['Kosakata', 'pinyin', 'Ya', 'Maks. 60 karakter. Hanzi + pinyin harus unik'],
            ['Kosakata', 'zhuyin', '-', 'Bopomofo, mis. ㄋㄧˇ ㄏㄠˇ (maks. 100)'],
            ['Kosakata', 'arti_indonesia', '-', 'Boleh dikosongkan dulu dan diisi belakangan (maks. 150)'],
            ['Kosakata', 'arti_singkat', '-', 'Teks pilihan jawaban di soal (maks. 100)'],
            ['Kosakata', 'baca_indonesia', '-', 'Maks. 60'],
            ['Kosakata', 'english', '-', 'Maks. 150'],
            ['Kosakata', 'kegunaan', '-', 'Kapan kata ini dipakai (maks. 2000)'],
            ['Kosakata', 'kata_pengait', '-', 'Pengait ingatan untuk membantu menghafal (maks. 1000)'],
            ['Kosakata', 'jenis_kata', '-', $jenisKata],
            ['Kosakata', 'sub_jenis', '-', $subJenis],
            ['Kosakata', 'kategori', '-', 'Nama kategori (tema); dibuat otomatis kalau belum ada'],
            ['Kosakata', 'level_tocfl', '-', 'Angka tingkat 1 sampai 6'],
            ['Kosakata', 'urutan', '-', 'Kosong = otomatis di akhir'],

            ['Contoh Kalimat', 'kosakata_hanzi, kosakata_pinyin', 'Ya', 'Harus sama persis dengan kosakatanya'],
            ['Contoh Kalimat', 'kalimat_hanzi, kalimat_pinyin, kalimat_arti', 'Ya', 'Satu kosakata boleh punya banyak baris'],
            ['Contoh Kalimat', 'catatan_tata_bahasa', '-', ''],

            ['Pecahan Karakter', 'kosakata_hanzi, kosakata_pinyin', 'Ya', 'Kosakata yang dipecah'],
            ['Pecahan Karakter', 'urutan', 'Ya', 'Posisi karakter di dalam kata, mulai dari 1 (kiri ke kanan)'],
            ['Pecahan Karakter', 'karakter_hanzi, karakter_pinyin', 'Ya', 'Karakter HARUS sudah ada di data karakter (hanzi + pinyin sama persis), kalau belum baris ini gagal'],
            ['Pecahan Karakter', 'pinyin_di_kata, arti_di_kata, penjelasan', '-', 'Isi kalau bacaan/arti karakter di kata ini berbeda dari kamus'],

            ['Relasi', 'kosakata_hanzi, kosakata_pinyin', 'Ya', 'Kata pertama'],
            ['Relasi', 'terkait_hanzi, terkait_pinyin', 'Ya', 'Kata kedua; kedua kata harus sudah ada'],
            ['Relasi', 'jenis', 'Ya', $jenisRelasi . '. Dibaca dua arah, jadi cukup dicatat sekali'],

            ['Grup Kosakata', 'grup_nama', 'Ya', 'Grup dibuat otomatis kalau namanya belum ada'],
            ['Grup Kosakata', 'grup_keterangan', '-', 'Dipakai hanya saat grup baru dibuat'],
            ['Grup Kosakata', 'kosakata_hanzi, kosakata_pinyin', 'Ya', 'Satu baris satu anggota grup'],
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
