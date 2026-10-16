<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Satu pola tata bahasa (mis. "Kalimat dengan 是").
 */
class PolaTataBahasa extends Model
{
    protected $table = 'pola_tata_bahasas';

    protected $guarded = ['id'];

    protected $casts = [
        'kata_kunci'    => 'array', // mis. ["是"]
        'pengecoh_umum' => 'array', // KANDIDAT pengecoh, bukan jaminan salah
        'urutan'        => 'integer',
    ];

    public function levelTocfl(): BelongsTo
    {
        return $this->belongsTo(LevelTocfl::class, 'level_tocfl_id');
    }

    public function contohKalimats(): HasMany
    {
        return $this->hasMany(ContohKalimat::class, 'pola_tata_bahasa_id');
    }

    public function soals(): HasMany
    {
        return $this->hasMany(Soal::class, 'pola_tata_bahasa_id');
    }
}
