<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Satu kalimat contoh. Sumber utama soal isian dan soal tata bahasa (pilihan ganda A-D).
 */
class ContohKalimat extends Model
{
    protected $table = 'contoh_kalimats';

    protected $guarded = ['id'];

    protected $casts = [
        'susunan'             => 'array',    // kalimat dipecah per kata + peran + penanda kunci
        'pengecoh'            => 'array',    // pengecoh yang sudah dipastikan salah
        'kunci_alternatif'    => 'array',    // kata lain yang juga benar
        'pengecoh_dicek_pada' => 'datetime',
    ];

    public function kosakata(): BelongsTo
    {
        return $this->belongsTo(Kosakata::class, 'kosakata_id');
    }

    public function polaTataBahasa(): BelongsTo
    {
        return $this->belongsTo(PolaTataBahasa::class, 'pola_tata_bahasa_id');
    }

    public function soals(): HasMany
    {
        return $this->hasMany(Soal::class, 'contoh_kalimat_id');
    }

    // ------------------------------------------------ helper untuk generator

    /** Kata kunci (yang dikosongkan pada soal isian) dari susunan. Null kalau belum ada. */
    public function kataKunci(): ?string
    {
        foreach ($this->susunan ?? [] as $kata) {
            if (! empty($kata['kunci'])) {
                return $kata['hanzi'] ?? null;
            }
        }

        return null;
    }

    /** Kalimat hanzi dengan kata kunci diganti titik kosong. */
    public function kalimatBertitik(string $titik = '____'): string
    {
        if (empty($this->susunan)) {
            return $this->hanzi;
        }

        return collect($this->susunan)
            ->map(fn ($kata) => ! empty($kata['kunci']) ? $titik : ($kata['hanzi'] ?? ''))
            ->implode('');
    }

    /**
     * Pengecoh yang BOLEH dipakai generator: hanya kalau sudah diverifikasi
     * (pengecoh_dicek_pada terisi), dan tanpa kata yang ada di kunci_alternatif.
     */
    public function pengecohTerverifikasi(): array
    {
        if ($this->pengecoh_dicek_pada === null) {
            return [];
        }

        return array_values(array_diff($this->pengecoh ?? [], $this->kunci_alternatif ?? []));
    }

    /** Apakah $kata boleh jadi pengecoh (bukan kunci, bukan kunci alternatif). */
    public function bolehJadiPengecoh(string $kata): bool
    {
        return $kata !== $this->kataKunci()
            && ! in_array($kata, $this->kunci_alternatif ?? [], true);
    }
}
