<?php

namespace App\Support;

use App\Models\GrupSoal;
use App\Models\Soal;

/**
 * Label tampilan (bahasa Indonesia) untuk konstanta Soal & GrupSoal.
 * Dipakai di view: \App\Support\LabelSoal::STATUS[$soal->status_validasi]
 */
final class LabelSoal
{
    public const JENIS = [
        GrupSoal::JENIS_LATIHAN => 'Latihan',
        GrupSoal::JENIS_UJIAN   => 'Ujian',
    ];

    public const SUMBER = [
        Soal::SUMBER_MANUAL    => 'Manual',
        Soal::SUMBER_GENERATOR => 'Generator',
        Soal::SUMBER_AI        => 'AI',
    ];

    public const BAGIAN = [
        Soal::BAGIAN_LISTENING   => 'Listening',
        Soal::BAGIAN_READING     => 'Reading',
        Soal::BAGIAN_TATA_BAHASA => 'Tata Bahasa',
        Soal::BAGIAN_KOSAKATA    => 'Kosakata',
        Soal::BAGIAN_KARAKTER    => 'Karakter',
    ];

    public const STATUS = [
        Soal::STATUS_OTOMATIS_LOLOS => 'Otomatis lolos',
        Soal::STATUS_PERLU_DICEK    => 'Perlu dicek',
        Soal::STATUS_DISETUJUI      => 'Disetujui',
        Soal::STATUS_DITOLAK        => 'Ditolak',
    ];

    public const TIPE = [
        'hanzi_ke_arti'            => 'Hanzi → Arti',
        'arti_ke_hanzi'            => 'Arti → Hanzi',
        'hanzi_ke_pinyin'          => 'Hanzi → Pinyin',
        'dengar_ke_hanzi'          => 'Dengar → Hanzi',
        'grammar_isian'            => 'Tata bahasa (isian)',
        'karakter_ke_arti'         => 'Karakter → Arti',
        'arti_ke_karakter'         => 'Arti → Karakter',
        'karakter_ke_radikal'      => 'Karakter → Radikal',
        'radikal_ke_arti'          => 'Radikal → Arti',
        'arti_karakter_dalam_kata' => 'Arti karakter dalam kata',
    ];

    /** Pengelompokan untuk <optgroup> pada pilihan tipe. */
    public const TIPE_GROUP = [
        'Kosakata'           => Soal::TIPE_KOSAKATA,
        'Tata bahasa'        => Soal::TIPE_GRAMMAR,
        'Karakter & radikal' => Soal::TIPE_KARAKTER,
    ];
}
