<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Satu radikal / bushou (部首) menurut kamus Kangxi.
 */
class Radikal extends Model
{
    protected $table = 'radikals';

    protected $guarded = ['id'];

    protected $casts = [
        'varian'         => 'array', // mis. ["氵","氺"]
        'nomor_kangxi'   => 'integer',
        'jumlah_goresan' => 'integer',
        'urutan'         => 'integer',
    ];

    public function karakters(): HasMany
    {
        return $this->hasMany(Karakter::class, 'radikal_id');
    }

    public function progres(): HasOne
    {
        return $this->hasOne(ProgresRadikal::class, 'radikal_id');
    }

    public function soals(): HasMany
    {
        return $this->hasMany(Soal::class, 'radikal_id');
    }

    public function riwayatReviews(): HasMany
    {
        return $this->hasMany(RiwayatReview::class, 'radikal_id');
    }
}
