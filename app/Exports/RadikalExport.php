<?php

namespace App\Exports;

use App\Models\Radikal;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Export data radikal ke Excel (mengikuti filter di halaman index).
 * Kolomnya sama dengan template import, jadi hasil export bisa diedit lalu di-import kembali.
 *
 * Memakai PhpSpreadsheet langsung (ikut terpasang bersama maatwebsite/excel).
 * Dipakai:  return (new RadikalExport($data))->download('radikal.xlsx');
 */
class RadikalExport
{
    public const SHEET = 'Radikal';

    /** Urutan kolom di template & export (juga judul kolom di baris 1). */
    public const KOLOM = [
        'nomor_kangxi', 'hanzi', 'varian', 'pinyin',
        'arti_indonesia', 'keterangan', 'jumlah_goresan', 'urutan',
    ];

    protected const LEBAR = [
        'nomor_kangxi' => 14, 'hanzi' => 10, 'varian' => 14, 'pinyin' => 16,
        'arti_indonesia' => 34, 'keterangan' => 60, 'jumlah_goresan' => 16, 'urutan' => 10,
        'jumlah_karakter' => 16,
    ];

    /** @param iterable<Radikal> $radikals  (sebaiknya sudah withCount('karakters')) */
    public function __construct(protected iterable $radikals = [])
    {
    }

    public function spreadsheet(): Spreadsheet
    {
        $buku  = new Spreadsheet();
        $sheet = $buku->getActiveSheet();
        $sheet->setTitle(self::SHEET);

        $baris = [];
        foreach ($this->radikals as $r) {
            $baris[] = [
                $r->nomor_kangxi,
                $r->hanzi,
                implode(', ', $r->varian ?? []),
                $r->pinyin,
                $r->arti_indonesia,
                $r->keterangan,
                $r->jumlah_goresan,
                $r->urutan,
                $r->karakters_count ?? null,
            ];
        }

        $this->tulisTabel($sheet, [...self::KOLOM, 'jumlah_karakter'], $baris, max(count($baris) + 1, 2));

        // kolom informasi (diabaikan saat import) dibedakan warnanya
        $sheet->getStyle('I1')->getFill()->getStartColor()->setRGB('64748B');

        $buku->setActiveSheetIndex(0);

        return $buku;
    }

    public function download(string $namaFile): StreamedResponse
    {
        $buku = $this->spreadsheet();

        return response()->streamDownload(function () use ($buku) {
            (new Xlsx($buku))->save('php://output');
        }, $namaFile, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    protected function gayaHeader(Worksheet $sheet, string $rentang): void
    {
        $sheet->getStyle($rentang)->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1A73E8']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
    }

    /**
     * Tulis header + baris data. Teks ditulis sebagai string eksplisit supaya Excel
     * tidak mengubah isinya (mis. "1-2" jadi tanggal).
     */
    protected function tulisTabel(Worksheet $sheet, array $header, array $baris, int $barisFormatSampai): void
    {
        foreach ($header as $c => $nama) {
            $sheet->setCellValueExplicit(Coordinate::stringFromColumnIndex($c + 1) . '1', $nama, DataType::TYPE_STRING);
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($c + 1))->setWidth(self::LEBAR[$nama] ?? 16);
        }

        foreach ($baris as $i => $isi) {
            foreach ($isi as $c => $nilai) {
                if ($nilai === null || $nilai === '') {
                    continue;
                }
                $alamat = Coordinate::stringFromColumnIndex($c + 1) . ($i + 2);

                if (is_int($nilai) || is_float($nilai)) {
                    $sheet->setCellValue($alamat, $nilai);
                } else {
                    $sheet->setCellValueExplicit($alamat, (string) $nilai, DataType::TYPE_STRING);
                }
            }
        }

        $kolomTerakhir = Coordinate::stringFromColumnIndex(count($header));
        $this->gayaHeader($sheet, "A1:{$kolomTerakhir}1");
        $sheet->getRowDimension(1)->setRowHeight(22);
        $sheet->freezePane('A2');
        $sheet->setAutoFilter("A1:{$kolomTerakhir}1");

        // kolom teks diformat sebagai Text sampai baris yang akan diisi
        foreach (['hanzi', 'varian', 'pinyin', 'arti_indonesia', 'keterangan'] as $nama) {
            $idx = array_search($nama, $header, true);
            if ($idx === false) {
                continue;
            }
            $huruf = Coordinate::stringFromColumnIndex($idx + 1);
            $sheet->getStyle("{$huruf}2:{$huruf}{$barisFormatSampai}")
                ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
        }

        // hanzi lebih besar & rata tengah
        $idx = array_search('hanzi', $header, true);
        if ($idx !== false) {
            $huruf = Coordinate::stringFromColumnIndex($idx + 1);
            $sheet->getStyle("{$huruf}2:{$huruf}{$barisFormatSampai}")->applyFromArray([
                'font'      => ['size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);
        }
    }
}
