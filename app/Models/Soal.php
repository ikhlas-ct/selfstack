<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Satu soal PILIHAN GANDA (4 pilihan A-D, satu jawaban benar).
 * Tanpa softDeletes. Untuk menyembunyikan soal bermasalah: status_validasi = 'ditolak'.
 */
class Soal extends Model
{
    protected $table = 'soals';

    protected $guarded = ['id'];

    protected $casts = [
        'meta'       => 'array',
        'dicek_pada' => 'datetime',
        'tingkat'    => 'integer',
        'urutan'     => 'integer',
        'poin'       => 'integer',
    ];

    // ---- bagian (string di database, daftar dijaga di sini)
    public const BAGIAN_LISTENING = 'listening';
    public const BAGIAN_READING = 'reading';
    public const BAGIAN_TATA_BAHASA = 'tata_bahasa';
    public const BAGIAN_KOSAKATA = 'kosakata';
    public const BAGIAN_KARAKTER = 'karakter';

    public const BAGIAN = [
        self::BAGIAN_LISTENING, self::BAGIAN_READING, self::BAGIAN_TATA_BAHASA,
        self::BAGIAN_KOSAKATA, self::BAGIAN_KARAKTER,
    ];

    // ---- tipe soal (pakai penamaan yang sama di generator)
    public const TIPE_KOSAKATA = ['hanzi_ke_arti', 'arti_ke_hanzi', 'hanzi_ke_pinyin', 'dengar_ke_hanzi'];
    public const TIPE_GRAMMAR = ['grammar_isian'];
    public const TIPE_KARAKTER = [
        'karakter_ke_arti', 'arti_ke_karakter', 'karakter_ke_radikal',
        'radikal_ke_arti', 'arti_karakter_dalam_kata',
    ];

    // ---- status validasi
    public const STATUS_OTOMATIS_LOLOS = 'otomatis_lolos';
    public const STATUS_PERLU_DICEK = 'perlu_dicek';
    public const STATUS_DISETUJUI = 'disetujui';
    public const STATUS_DITOLAK = 'ditolak';

    // ---- asal soal
    public const SUMBER_MANUAL = 'manual';
    public const SUMBER_GENERATOR = 'generator';
    public const SUMBER_AI = 'ai';

    // ---------------------------------------------------------------- relasi

    public function grupSoal(): BelongsTo
    {
        return $this->belongsTo(GrupSoal::class, 'grup_soal_id');
    }

    public function kosakata(): BelongsTo
    {
        return $this->belongsTo(Kosakata::class, 'kosakata_id');
    }

    public function polaTataBahasa(): BelongsTo
    {
        return $this->belongsTo(PolaTataBahasa::class, 'pola_tata_bahasa_id');
    }

    public function contohKalimat(): BelongsTo
    {
        return $this->belongsTo(ContohKalimat::class, 'contoh_kalimat_id');
    }

    public function paragrafBacaan(): BelongsTo
    {
        // Dinamai paragrafBacaan karena kolom soals.paragraf sudah dipakai untuk teks soal.
        return $this->belongsTo(Paragraf::class, 'paragraf_id');
    }

    public function karakter(): BelongsTo
    {
        return $this->belongsTo(Karakter::class, 'karakter_id');
    }

    public function radikal(): BelongsTo
    {
        return $this->belongsTo(Radikal::class, 'radikal_id');
    }

    public function jawabanUjians(): HasMany
    {
        return $this->hasMany(JawabanUjian::class, 'soal_id');
    }

    public function laporanSoals(): HasMany
    {
        return $this->hasMany(LaporanSoal::class, 'soal_id');
    }

    // ---------------------------------------------------------------- scope

    /** Soal yang boleh dikerjakan: semua kecuali yang ditolak. */
    public function scopeDapatDikerjakan(Builder $query): Builder
    {
        return $query->where('status_validasi', '!=', self::STATUS_DITOLAK);
    }

    /** Antrean pemeriksaan admin. */
    public function scopePerluDicek(Builder $query): Builder
    {
        return $query->where('status_validasi', self::STATUS_PERLU_DICEK);
    }

    public function scopeBagian(Builder $query, string $bagian): Builder
    {
        return $query->where('bagian', $bagian);
    }

    // ---------------------------------------------------------------- helper

    /** ['A' => ..., 'B' => ..., 'C' => ..., 'D' => ...] */
    public function getPilihanAttribute(): array
    {
        return [
            'A' => $this->pilihan_a,
            'B' => $this->pilihan_b,
            'C' => $this->pilihan_c,
            'D' => $this->pilihan_d,
        ];
    }

    public function teksPilihan(?string $huruf): ?string
    {
        return $huruf === null ? null : ($this->pilihan[strtoupper($huruf)] ?? null);
    }

    public function getTeksJawabanBenarAttribute(): ?string
    {
        return $this->teksPilihan($this->jawaban_benar);
    }

    /** Benar kalau huruf yang dipilih sama dengan jawaban_benar. Null (dilewati) = salah. */
    public function periksaJawaban(?string $huruf): bool
    {
        return $huruf !== null && strtoupper($huruf) === $this->jawaban_benar;
    }

    /**
     * Item yang hafalannya diperbarui oleh jawaban soal ini:
     * karakter_id terisi -> 'karakter', radikal_id terisi -> 'radikal', selain itu 'kosakata'.
     */
    public function jenisItemReview(): string
    {
        if ($this->karakter_id !== null) {
            return RiwayatReview::ITEM_KARAKTER;
        }

        if ($this->radikal_id !== null) {
            return RiwayatReview::ITEM_RADIKAL;
        }

        return RiwayatReview::ITEM_KOSAKATA;
    }
}
