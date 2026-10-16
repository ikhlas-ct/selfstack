<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Kategori = TEMA kosakata (keluarga, makanan, ...). Bukan jenis kata.
 */
class Kategori extends Model
{
    protected $table = 'kategoris';

    protected $guarded = ['id'];

    public function kosakatas(): HasMany
    {
        return $this->hasMany(Kosakata::class, 'kategori_id');
    }
}
