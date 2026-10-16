<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Pengaturan website + target belajar harian. Hanya 1 baris yang dipakai.
 */
class WebsiteSetting extends Model
{
    protected $table = 'website_settings';

    protected $guarded = ['id'];

    protected $casts = [
        'kartu_baru_per_hari'    => 'integer',
        'karakter_baru_per_hari' => 'integer',
        'radikal_baru_per_hari'  => 'integer',
        'batas_review_per_hari'  => 'integer',
        'jam_ganti_hari'         => 'integer',
    ];

    /** Ambil satu-satunya baris pengaturan (dibuat dengan nilai bawaan kalau belum ada). */
    public static function ambil(): self
    {
        return static::query()->first() ?? static::create([]);
    }
}
