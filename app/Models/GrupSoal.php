<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Satu paket soal (latihan atau ujian) berisi banyak soal pilihan ganda.
 * Menghapus paket ikut menghapus soal, riwayat percobaan, dan jawabannya.
 * Untuk menyembunyikan tanpa menghapus: set aktif = false.
 */
class GrupSoal extends Model
{
    protected $table = 'grup_soals';

    protected $guarded = ['id'];

    protected $casts = [
        'pengaturan_generator' => 'array',
        'aktif'                => 'boolean',
        'durasi_menit'         => 'integer',
        'nilai_lulus'          => 'integer',
        'maks_percobaan'       => 'integer',
    ];

    public const JENIS_LATIHAN = 'latihan';
    public const JENIS_UJIAN = 'ujian';

    public const SUMBER_MANUAL = 'manual';
    public const SUMBER_GENERATOR = 'generator';
    public const SUMBER_AI = 'ai';

    public function levelTocfl(): BelongsTo
    {
        return $this->belongsTo(LevelTocfl::class, 'level_tocfl_id');
    }

    public function grupKosakata(): BelongsTo
    {
        return $this->belongsTo(GrupKosakata::class, 'grup_kosakata_id');
    }

    public function soals(): HasMany
    {
        return $this->hasMany(Soal::class, 'grup_soal_id')->orderBy('urutan')->orderBy('id');
    }

    public function hasilUjians(): HasMany
    {
        return $this->hasMany(HasilUjian::class, 'grup_soal_id');
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('aktif', true);
    }

    public function scopeLatihan(Builder $query): Builder
    {
        return $query->where('jenis', self::JENIS_LATIHAN);
    }

    public function scopeUjian(Builder $query): Builder
    {
        return $query->where('jenis', self::JENIS_UJIAN);
    }

    /** Masih boleh dikerjakan lagi? (maks_percobaan kosong = tanpa batas) */
    public function masihBolehMencoba(): bool
    {
        if ($this->maks_percobaan === null) {
            return true;
        }

        return $this->hasilUjians()->whereNotNull('selesai_pada')->count() < $this->maks_percobaan;
    }
}
