<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Jawaban untuk satu soal dalam satu percobaan (huruf A-D, atau null = dilewati).
 */
class JawabanUjian extends Model
{
    protected $table = 'jawaban_ujians';

    // Tabel ini tidak punya created_at / updated_at (waktunya ada di dijawab_pada).
    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = [
        'benar'          => 'boolean',
        'waktu_jawab_ms' => 'integer',
        'dijawab_pada'   => 'datetime',
    ];

    public function hasilUjian(): BelongsTo
    {
        return $this->belongsTo(HasilUjian::class, 'hasil_ujian_id');
    }

    public function soal(): BelongsTo
    {
        return $this->belongsTo(Soal::class, 'soal_id');
    }

    public function riwayatReviews(): HasMany
    {
        return $this->hasMany(RiwayatReview::class, 'jawaban_ujian_id');
    }

    public function getDilewatiAttribute(): bool
    {
        return $this->jawaban_dipilih === null;
    }
}
