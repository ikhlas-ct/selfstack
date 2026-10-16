<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

/**
 * Satu karakter tunggal (學, 生, 行). Karakter berbunyi ganda dibuat jadi beberapa baris.
 */
class Karakter extends Model
{
    protected $table = 'karakters';

    protected $guarded = ['id'];

    protected $casts = [
        'komponen'       => 'array', // [{"hanzi":"言","peran":"makna"}, ...]
        'jumlah_goresan' => 'integer',
        'urutan'         => 'integer',
    ];

    public function radikal(): BelongsTo
    {
        return $this->belongsTo(Radikal::class, 'radikal_id');
    }

    public function levelTocfl(): BelongsTo
    {
        return $this->belongsTo(LevelTocfl::class, 'level_tocfl_id');
    }

    public function progres(): HasOne
    {
        return $this->hasOne(ProgresKarakter::class, 'karakter_id');
    }

    /** Kata-kata yang memuat karakter ini (lewat kosakata_karakter). */
    public function kosakatas(): BelongsToMany
    {
        return $this->belongsToMany(Kosakata::class, 'kosakata_karakter', 'karakter_id', 'kosakata_id')
            ->withPivot(['urutan', 'pinyin_di_kata', 'arti_di_kata', 'penjelasan'])
            ->withTimestamps();
    }

    public function relasis(): HasMany
    {
        return $this->hasMany(KarakterRelasi::class, 'karakter_id');
    }

    public function soals(): HasMany
    {
        return $this->hasMany(Soal::class, 'karakter_id');
    }

    public function riwayatReviews(): HasMany
    {
        return $this->hasMany(RiwayatReview::class, 'karakter_id');
    }

    /**
     * ID karakter yang berelasi dengan karakter ini, membaca KEDUA arah.
     * Contoh: idRelasi([KarakterRelasi::JENIS_SINONIM]) untuk dikecualikan dari pengecoh soal arti.
     */
    public function idRelasi(array $jenis): Collection
    {
        $searah = KarakterRelasi::where('karakter_id', $this->id)
            ->whereIn('jenis', $jenis)->pluck('karakter_terkait_id');

        $kebalikan = KarakterRelasi::where('karakter_terkait_id', $this->id)
            ->whereIn('jenis', $jenis)->pluck('karakter_id');

        return $searah->merge($kebalikan)->unique()->values();
    }

    /** Hanzi komponen (selain radikal utama) yang TIDAK boleh jadi pengecoh soal "radikal dari karakter ini". */
    public function hanziKomponen(): array
    {
        return collect($this->komponen ?? [])->pluck('hanzi')->filter()->values()->all();
    }
}
