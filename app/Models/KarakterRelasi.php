<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Relasi antar karakter. Tidak otomatis dua arah (lihat Karakter::idRelasi()).
 */
class KarakterRelasi extends Model
{
    protected $table = 'karakter_relasis';

    protected $guarded = ['id'];

    /** sinonim: dilarang jadi pengecoh soal arti. mirip_bunyi: dilarang jadi pengecoh soal dengar. */
    public const JENIS_SINONIM = 'sinonim';
    public const JENIS_MIRIP_BENTUK = 'mirip_bentuk';
    public const JENIS_MIRIP_BUNYI = 'mirip_bunyi';

    public const JENIS = [self::JENIS_SINONIM, self::JENIS_MIRIP_BENTUK, self::JENIS_MIRIP_BUNYI];

    public function karakter(): BelongsTo
    {
        return $this->belongsTo(Karakter::class, 'karakter_id');
    }

    public function terkait(): BelongsTo
    {
        return $this->belongsTo(Karakter::class, 'karakter_terkait_id');
    }
}
