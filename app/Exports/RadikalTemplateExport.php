<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

/**
 * Template Excel untuk import radikal (sheet "Radikal" + sheet "Petunjuk").
 * Dipakai:  return (new RadikalTemplateExport)->download('template-import-radikal.xlsx');
 */
class RadikalTemplateExport extends RadikalExport
{
    public function spreadsheet(): Spreadsheet
    {
        $buku  = new Spreadsheet();
        $sheet = $buku->getActiveSheet();
        $sheet->setTitle(self::SHEET);

        $contoh = [
            [30, '口', '', 'kǒu', 'mulut', 'Karakter dengan 口 biasanya berhubungan dengan mulut, ucapan, atau bunyi, mis. 吃, 叫, 唱.', 3, 5],
            [64, '手', '扌', 'shǒu', 'tangan', 'Karakter dengan 扌 biasanya berhubungan dengan tangan atau gerakan tangan, mis. 打, 拿, 找.', 4, 4],
            [85, '水', '氵, 氺', 'shuǐ', 'air', 'Karakter dengan 氵 biasanya berhubungan dengan air atau cairan, mis. 河, 海, 洗.', 4, 2],
        ];

        $this->tulisTabel($sheet, self::KOLOM, $contoh, 500);

        // ---- sheet petunjuk
        $info = $buku->createSheet();
        $info->setTitle('Petunjuk');

        $petunjuk = [
            ['kolom', 'wajib', 'keterangan'],
            ['nomor_kangxi', '-', 'Angka 1-214 (nomor radikal menurut kamus Kangxi). Tidak boleh sama dengan radikal lain.'],
            ['hanzi', 'Ya', 'Maks. 8 karakter. Dipakai sebagai kunci: kalau hanzi sudah ada di sistem, barisnya dianggap data yang sama.'],
            ['varian', '-', 'Bentuk lain radikal, dipisah koma, mis. 氵, 氺 (maks. 10, tiap varian maks. 8 karakter).'],
            ['pinyin', 'Ya', 'Maks. 100 karakter, mis. shuǐ.'],
            ['arti_indonesia', 'Ya', 'Maks. 500 karakter.'],
            ['keterangan', '-', 'Penjelasan/cara mengenali radikal (maks. 2000 karakter).'],
            ['jumlah_goresan', '-', 'Angka 1-30.'],
            ['urutan', '-', 'Urutan belajar. Kosong = otomatis di akhir (data baru) / tidak berubah (data lama).'],
            ['', '', ''],
            ['Catatan', '', 'Baris contoh di sheet Radikal hanya contoh format: hapus sebelum mengisi data sendiri.'],
            ['', '', 'Judul kolom (baris 1) jangan diubah. Kolom jumlah_karakter di file export hanya informasi dan diabaikan saat import.'],
            ['', '', 'Sel yang dikosongkan tidak menghapus data lama di sistem.'],
        ];

        foreach ($petunjuk as $i => $baris) {
            foreach ($baris as $c => $nilai) {
                $info->setCellValueExplicit(Coordinate::stringFromColumnIndex($c + 1) . ($i + 1), (string) $nilai, DataType::TYPE_STRING);
            }
        }
        $this->gayaHeader($info, 'A1:C1');
        $info->getColumnDimension('A')->setWidth(18);
        $info->getColumnDimension('B')->setWidth(8);
        $info->getColumnDimension('C')->setWidth(100);
        $info->getStyle('A2:C' . count($petunjuk))->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);

        $buku->setActiveSheetIndex(0);

        return $buku;
    }
}
