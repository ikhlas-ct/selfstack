<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Laporan masalah pada sebuah soal, mis. "pilihan B ternyata juga benar".
 */
class LaporanSoal extends Model
{
    protected $table = 'laporan_soals';

    protected $guarded = ['id'];

    protected $casts = [
        'ditangani_pada' => 'datetime',
    ];

    public const JENIS_AMBIGU = 'ambigu_banyak_benar';
    public const JENIS_KUNCI_SALAH = 'kunci_salah';
    public const JENIS_TIDAK_ADA_BENAR = 'tidak_ada_benar';
    public const JENIS_TYPO = 'typo';
    public const JENIS_AUDIO = 'audio_bermasalah';
    public const JENIS_LAINNYA = 'lainnya';

    public const JENIS = [
        self::JENIS_AMBIGU, self::JENIS_KUNCI_SALAH, self::JENIS_TIDAK_ADA_BENAR,
        self::JENIS_TYPO, self::JENIS_AUDIO, self::JENIS_LAINNYA,
    ];

    public const STATUS_BARU = 'baru';
    public const STATUS_DITANGANI = 'ditangani';
    public const STATUS_DIABAIKAN = 'diabaikan';

    public function soal(): BelongsTo
    {
        return $this->belongsTo(Soal::class, 'soal_id');
    }

    public function hasilUjian(): BelongsTo
    {
        return $this->belongsTo(HasilUjian::class, 'hasil_ujian_id');
    }

    /** Antrean laporan yang belum ditangani. */
    public function scopeBaru(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_BARU);
    }

    /**
     * Terapkan laporan ke data sumber:
     *  1. soal ditandai 'perlu_dicek' beserta catatannya,
     *  2. untuk jenis ambigu: teks pilihan bermasalah dimasukkan ke
     *     contoh_kalimats.kunci_alternatif kalimat sumbernya (generator dilarang memakainya lagi),
     *  3. laporan ditandai 'ditangani'.
     */
    public function tandaiDitangani(): void
    {
        $soal = $this->soal;

        if ($soal) {
            $soal->update([
                'status_validasi'  => Soal::STATUS_PERLU_DICEK,
                'catatan_validasi' => trim(($this->catatan ?: 'Dilaporkan: ' . $this->jenis)),
            ]);

            $kalimat = $soal->contohKalimat;

            if ($this->jenis === self::JENIS_AMBIGU && $this->teks_pilihan_bermasalah && $kalimat) {
                $alternatif = collect($kalimat->kunci_alternatif ?? [])
                    ->push($this->teks_pilihan_bermasalah)
                    ->unique()
                    ->values()
                    ->all();

                $kalimat->update(['kunci_alternatif' => $alternatif]);
            }
        }

        $this->update([
            'status'         => self::STATUS_DITANGANI,
            'ditangani_pada' => now(),
        ]);
    }

    public function abaikan(): void
    {
        $this->update([
            'status'         => self::STATUS_DIABAIKAN,
            'ditangani_pada' => now(),
        ]);
    }
}
