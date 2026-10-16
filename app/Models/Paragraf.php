<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Satu bacaan (paragraf). Dipakai halaman bacaan dan soal bagian "reading".
 */
class Paragraf extends Model
{
    protected $table = 'paragrafs';

    protected $guarded = ['id'];

    public function levelTocfl(): BelongsTo
    {
        return $this->belongsTo(LevelTocfl::class, 'level_tocfl_id');
    }

    public function soals(): HasMany
    {
        return $this->hasMany(Soal::class, 'paragraf_id');
    }
}
