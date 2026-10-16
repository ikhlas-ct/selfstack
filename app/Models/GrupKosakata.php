<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Grup kosakata buatan sendiri; bahan generator soal (grup kosakata -> paket soal).
 */
class GrupKosakata extends Model
{
    protected $table = 'grup_kosakatas';

    protected $guarded = ['id'];

    /** Anggota grup lewat tabel pivot grup_kosakata_kosakata (tanpa timestamps). */
    public function kosakatas(): BelongsToMany
    {
        return $this->belongsToMany(Kosakata::class, 'grup_kosakata_kosakata', 'grup_kosakata_id', 'kosakata_id');
    }

    public function grupSoals(): HasMany
    {
        return $this->hasMany(GrupSoal::class, 'grup_kosakata_id');
    }
}
