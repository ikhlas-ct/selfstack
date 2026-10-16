<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

/**
 * Satu kata yang dihafal.
 */
class Kosakata extends Model
{
    protected $table = 'kosakatas';

    protected $guarded = ['id'];

    protected $casts = [
        'urutan' => 'integer',
    ];

    /** Jenis kata (tata bahasa). Disimpan di kosakatas.jenis_kata. */
    public const JENIS_KATA = [
        'kata_benda', 'kata_kerja', 'kata_sifat', 'kata_ganti', 'kata_bilangan',
        'kata_penggolong', 'kata_keterangan', 'kata_depan', 'kata_sambung', 'partikel', 'kata_seru',
    ];

    /** Sub jenis, mis. untuk kata kerja. */
    public const SUB_JENIS = ['transitif', 'intransitif', 'modal', 'penghubung', 'terpisah'];

    // ---------------------------------------------------------------- relasi

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function levelTocfl(): BelongsTo
    {
        return $this->belongsTo(LevelTocfl::class, 'level_tocfl_id');
    }

    /** Progres hafalan kata ini (satu baris, dibuat saat pertama dipelajari). */
    public function progres(): HasOne
    {
        return $this->hasOne(ProgresHafalan::class, 'kosakata_id');
    }

    public function contohKalimats(): HasMany
    {
        return $this->hasMany(ContohKalimat::class, 'kosakata_id');
    }

    /** Pecahan kata per karakter, berurutan kiri ke kanan (tabel kosakata_karakter). */
    public function pecahan(): HasMany
    {
        return $this->hasMany(KosakataKarakter::class, 'kosakata_id')->orderBy('urutan');
    }

    /** Karakter-karakter penyusun kata (lewat kosakata_karakter), berurutan. */
    public function karakters(): BelongsToMany
    {
        return $this->belongsToMany(Karakter::class, 'kosakata_karakter', 'kosakata_id', 'karakter_id')
            ->withPivot(['urutan', 'pinyin_di_kata', 'arti_di_kata', 'penjelasan'])
            ->withTimestamps()
            ->orderByPivot('urutan');
    }

    public function grupKosakatas(): BelongsToMany
    {
        return $this->belongsToMany(GrupKosakata::class, 'grup_kosakata_kosakata', 'kosakata_id', 'grup_kosakata_id');
    }

    /** Relasi yang dicatat dari sisi kata ini. Untuk dua arah, pakai idRelasi(). */
    public function relasis(): HasMany
    {
        return $this->hasMany(KosakataRelasi::class, 'kosakata_id');
    }

    public function soals(): HasMany
    {
        return $this->hasMany(Soal::class, 'kosakata_id');
    }

    public function riwayatReviews(): HasMany
    {
        return $this->hasMany(RiwayatReview::class, 'kosakata_id');
    }

    // ---------------------------------------------------------------- helper

    /**
     * ID kata yang berelasi dengan kata ini, membaca KEDUA arah
     * (relasi tidak otomatis dua arah). Contoh: idRelasi([KosakataRelasi::JENIS_SINONIM]).
     * Dipakai generator untuk MENGECUALIKAN sinonim / kata mirip bunyi dari pengecoh.
     */
    public function idRelasi(array $jenis): Collection
    {
        $searah = KosakataRelasi::where('kosakata_id', $this->id)
            ->whereIn('jenis', $jenis)->pluck('kosakata_terkait_id');

        $kebalikan = KosakataRelasi::where('kosakata_terkait_id', $this->id)
            ->whereIn('jenis', $jenis)->pluck('kosakata_id');

        return $searah->merge($kebalikan)->unique()->values();
    }

    /** Teks arti untuk PILIHAN JAWABAN: arti_singkat kalau ada, kalau tidak arti_indonesia. */
    public function getArtiPilihanAttribute(): ?string
    {
        return $this->arti_singkat ?: $this->arti_indonesia;
    }

    // ---------------------------------------------------------------- scope

    /** Pencarian di hanzi, pinyin, dan arti. */
    public function scopeCari(Builder $query, ?string $kata): Builder
    {
        if ($kata === null || trim($kata) === '') {
            return $query;
        }

        $kata = trim($kata);

        return $query->where(function (Builder $q) use ($kata) {
            $q->where('hanzi', 'like', "%{$kata}%")
              ->orWhere('pinyin', 'like', "%{$kata}%")
              ->orWhere('arti_indonesia', 'like', "%{$kata}%")
              ->orWhere('arti_singkat', 'like', "%{$kata}%");
        });
    }

    /** Urutan tampil: level TOCFL dulu, lalu kolom urutan. */
    public function scopeUrut(Builder $query): Builder
    {
        return $query->orderBy('level_tocfl_id')->orderBy('urutan')->orderBy('id');
    }
}
