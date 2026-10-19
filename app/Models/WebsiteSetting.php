<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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

    /** URL logo; kalau belum diunggah pakai logo bawaan. Dipakai di sidebar: $settings->logo_url */
    public function getLogoUrlAttribute(): string
    {
        return self::urlStorage($this->logo) ?? asset('default-image/default-logo.png');
    }

    /** URL gambar pengantar; null kalau belum diunggah. */
    public function getGambarPengantarUrlAttribute(): ?string
    {
        return self::urlStorage($this->gambar_pengantar);
    }

    /**
     * Ubah path di database jadi URL. Menerima "website/x.png", "storage/website/x.png",
     * atau URL penuh, jadi aman walaupun format path-nya beda-beda.
     */
    protected static function urlStorage(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        $path = ltrim($path, '/');

        return asset('storage/' . (Str::startsWith($path, 'storage/') ? Str::after($path, 'storage/') : $path));
    }
}
