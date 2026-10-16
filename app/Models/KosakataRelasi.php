<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Relasi antar kosakata. Tidak otomatis dua arah: "A sinonim B" tidak berarti "B sinonim A".
 * Untuk membaca dua arah pakai Kosakata::idRelasi().
 */
class KosakataRelasi extends Model
{
    protected $table = 'kosakata_relasis';

    protected $guarded = ['id'];

    /** sinonim & mirip_bunyi: DILARANG jadi pengecoh (sinonim) / pengecoh soal dengar (mirip_bunyi). */
    public const JENIS_SINONIM = 'sinonim';
    public const JENIS_ANTONIM = 'antonim';
    public const JENIS_MIRIP = 'mirip';
    public const JENIS_MIRIP_BUNYI = 'mirip_bunyi';

    public const JENIS = [self::JENIS_SINONIM, self::JENIS_ANTONIM, self::JENIS_MIRIP, self::JENIS_MIRIP_BUNYI];

    public function kosakata(): BelongsTo
    {
        return $this->belongsTo(Kosakata::class, 'kosakata_id');
    }

    public function terkait(): BelongsTo
    {
        return $this->belongsTo(Kosakata::class, 'kosakata_terkait_id');
    }
}
