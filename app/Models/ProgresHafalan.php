<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Progres hafalan KOSAKATA (satu baris per kosakata).
 * Diperbarui dari riwayat_reviews (jenis_item = 'kosakata').
 */
class ProgresHafalan extends ProgresBase
{
    protected $table = 'progres_hafalans';

    public function kosakata(): BelongsTo
    {
        return $this->belongsTo(Kosakata::class, 'kosakata_id');
    }
}
