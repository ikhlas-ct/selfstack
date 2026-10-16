<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Master level TOCFL (6 baris: level 1 sampai 6, band A/B/C).
 */
class LevelTocfl extends Model
{
    protected $table = 'level_tocfls';

    protected $guarded = ['id'];

    protected $casts = [
        'tingkat' => 'integer',
    ];

    public function kosakatas(): HasMany
    {
        return $this->hasMany(Kosakata::class, 'level_tocfl_id');
    }

    public function karakters(): HasMany
    {
        return $this->hasMany(Karakter::class, 'level_tocfl_id');
    }

    public function paragrafs(): HasMany
    {
        return $this->hasMany(Paragraf::class, 'level_tocfl_id');
    }

    public function polaTataBahasas(): HasMany
    {
        return $this->hasMany(PolaTataBahasa::class, 'level_tocfl_id');
    }

    public function grupSoals(): HasMany
    {
        return $this->hasMany(GrupSoal::class, 'level_tocfl_id');
    }
}
