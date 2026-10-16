<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Progres hafalan KARAKTER (satu baris per karakter).
 * Diperbarui dari riwayat_reviews (jenis_item = 'karakter').
 */
class ProgresKarakter extends ProgresBase
{
    protected $table = 'progres_karakters';

    public function karakter(): BelongsTo
    {
        return $this->belongsTo(Karakter::class, 'karakter_id');
    }
}
