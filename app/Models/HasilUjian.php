<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Satu kali mengerjakan sebuah paket soal (satu percobaan).
 */
class HasilUjian extends Model
{
    protected $table = 'hasil_ujians';

    protected $guarded = ['id'];

    protected $casts = [
        'jumlah_soal'  => 'integer',
        'jumlah_benar' => 'integer',
        'skor'         => 'decimal:2',
        'lulus'        => 'boolean',
        'mulai_pada'   => 'datetime',
        'selesai_pada' => 'datetime',
    ];

    public function grupSoal(): BelongsTo
    {
        return $this->belongsTo(GrupSoal::class, 'grup_soal_id');
    }

    public function jawabanUjians(): HasMany
    {
        return $this->hasMany(JawabanUjian::class, 'hasil_ujian_id');
    }

    public function riwayatReviews(): HasMany
    {
        return $this->hasMany(RiwayatReview::class, 'hasil_ujian_id');
    }

    public function laporanSoals(): HasMany
    {
        return $this->hasMany(LaporanSoal::class, 'hasil_ujian_id');
    }

    /** Percobaan yang masih berjalan (selesai_pada kosong). */
    public function scopeBerjalan(Builder $query): Builder
    {
        return $query->whereNull('selesai_pada');
    }

    public function scopeSelesai(Builder $query): Builder
    {
        return $query->whereNotNull('selesai_pada');
    }

    /**
     * Menutup percobaan: hitung jumlah benar dan skor (0-100), tentukan lulus
     * (hanya untuk ujian dengan nilai_lulus; latihan = null), lalu isi selesai_pada.
     * Skor dihitung per soal (bukan berbobot poin).
     */
    public function selesaikan(): void
    {
        $grup = $this->grupSoal;

        $jumlahSoal = $this->jumlah_soal ?: $grup->soals()->dapatDikerjakan()->count();
        $jumlahBenar = $this->jawabanUjians()->where('benar', true)->count();
        $skor = $jumlahSoal > 0 ? round($jumlahBenar / $jumlahSoal * 100, 2) : 0;

        $lulus = ($grup->jenis === GrupSoal::JENIS_UJIAN && $grup->nilai_lulus !== null)
            ? $skor >= $grup->nilai_lulus
            : null;

        $this->update([
            'jumlah_soal'  => $jumlahSoal,
            'jumlah_benar' => $jumlahBenar,
            'skor'         => $skor,
            'lulus'        => $lulus,
            'selesai_pada' => now(),
        ]);
    }
}
