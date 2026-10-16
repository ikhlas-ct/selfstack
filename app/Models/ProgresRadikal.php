<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Progres hafalan RADIKAL (satu baris per radikal).
 * Diperbarui dari riwayat_reviews (jenis_item = 'radikal').
 */
class ProgresRadikal extends ProgresBase
{
    protected $table = 'progres_radikals';

    public function radikal(): BelongsTo
    {
        return $this->belongsTo(Radikal::class, 'radikal_id');
    }
}
