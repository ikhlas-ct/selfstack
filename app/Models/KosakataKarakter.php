<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Satu pecahan kata: karakter ke-N di dalam sebuah kosakata.
 * Contoh 學生: urutan 1 -> 學, urutan 2 -> 生.
 */
class KosakataKarakter extends Model
{
    protected $table = 'kosakata_karakter';

    protected $guarded = ['id'];

    protected $casts = [
        'urutan' => 'integer',
    ];

    public function kosakata(): BelongsTo
    {
        return $this->belongsTo(Kosakata::class, 'kosakata_id');
    }

    public function karakter(): BelongsTo
    {
        return $this->belongsTo(Karakter::class, 'karakter_id');
    }

    /** Bacaan karakter di kata INI; kalau kosong pakai bacaan kamus. */
    public function getPinyinEfektifAttribute(): ?string
    {
        return $this->pinyin_di_kata ?: $this->karakter?->pinyin;
    }

    /** Arti karakter di kata INI; kalau kosong pakai arti kamus. */
    public function getArtiEfektifAttribute(): ?string
    {
        return $this->arti_di_kata ?: $this->karakter?->arti_indonesia;
    }
}
