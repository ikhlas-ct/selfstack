<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Dasar untuk tiga tabel progres yang strukturnya sama:
 * ProgresHafalan (kosakata), ProgresKarakter, ProgresRadikal.
 * Satu service penjadwalan (SM-2) bisa memakai ketiganya.
 */
abstract class ProgresBase extends Model
{
    public const STATUS_BERIKUTNYA = 'berikutnya';
    public const STATUS_LUPA_DAN_INGAT = 'lupa_dan_ingat';
    public const STATUS_INGAT_SEPENUHNYA = 'ingat_sepenuhnya';

    public const STATUS = [
        self::STATUS_BERIKUTNYA,
        self::STATUS_LUPA_DAN_INGAT,
        self::STATUS_INGAT_SEPENUHNYA,
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'pertama_dipelajari_pada' => 'datetime',
        'terakhir_diulang'        => 'datetime',
        'review_berikutnya'       => 'datetime',
        'jumlah_ulang'            => 'integer',
        'benar_beruntun'          => 'integer',
        'interval_hari'           => 'integer',
        'jumlah_salah'            => 'integer',
        'ease_factor'             => 'decimal:2',
        'ditandai'                => 'boolean',
        'ditangguhkan'            => 'boolean',
    ];

    /** Yang tidak ditangguhkan. */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('ditangguhkan', false);
    }

    /** Antrean review: tidak ditangguhkan dan sudah jatuh tempo. */
    public function scopeJatuhTempo(Builder $query): Builder
    {
        return $query->where('ditangguhkan', false)
            ->whereNotNull('review_berikutnya')
            ->where('review_berikutnya', '<=', now());
    }

    /** Item yang sudah dipelajari (punya tanggal pertama dipelajari). */
    public function scopeSudahDipelajari(Builder $query): Builder
    {
        return $query->whereNotNull('pertama_dipelajari_pada');
    }

    /** Item "bandel": sudah salah minimal $batas kali. */
    public function scopeBandel(Builder $query, int $batas = 4): Builder
    {
        return $query->where('jumlah_salah', '>=', $batas);
    }

    public function scopeDitandai(Builder $query): Builder
    {
        return $query->where('ditandai', true);
    }

    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }
}
