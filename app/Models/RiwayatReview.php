<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Satu kali sebuah item (kosakata / karakter / radikal) direview.
 * Dari sini progres_* dan statistik harian & streak dibuat.
 */
class RiwayatReview extends Model
{
    protected $table = 'riwayat_reviews';

    // Tabel ini tidak punya created_at / updated_at (waktunya ada di direview_pada).
    public $timestamps = false;

    protected $guarded = ['id'];

    protected $casts = [
        'benar'         => 'boolean',
        'durasi_ms'     => 'integer',
        'direview_pada' => 'datetime',
    ];

    public const ITEM_KOSAKATA = 'kosakata';
    public const ITEM_KARAKTER = 'karakter';
    public const ITEM_RADIKAL = 'radikal';

    public const SUMBER_POPUP = 'popup';
    public const SUMBER_SOAL = 'soal';

    public function kosakata(): BelongsTo
    {
        return $this->belongsTo(Kosakata::class, 'kosakata_id');
    }

    public function karakter(): BelongsTo
    {
        return $this->belongsTo(Karakter::class, 'karakter_id');
    }

    public function radikal(): BelongsTo
    {
        return $this->belongsTo(Radikal::class, 'radikal_id');
    }

    public function hasilUjian(): BelongsTo
    {
        return $this->belongsTo(HasilUjian::class, 'hasil_ujian_id');
    }

    public function jawabanUjian(): BelongsTo
    {
        return $this->belongsTo(JawabanUjian::class, 'jawaban_ujian_id');
    }

    /** Item yang direview (null kalau item-nya sudah dihapus). */
    public function item(): ?Model
    {
        return match ($this->jenis_item) {
            self::ITEM_KARAKTER => $this->karakter,
            self::ITEM_RADIKAL  => $this->radikal,
            default             => $this->kosakata,
        };
    }
}
