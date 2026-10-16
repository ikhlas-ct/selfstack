<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Harus berjalan SEBELUM contoh_kalimats dan soals (keduanya punya FK ke tabel ini).
return new class extends Migration
{
    public function up(): void
    {
        // Satu baris = satu pola tata bahasa yang dipelajari (mis. "Kalimat dengan 是").
        Schema::create('pola_tata_bahasas', function (Blueprint $table) {
            $table->id();

            // Level TOCFL tempat pola ini diajarkan. Level dihapus -> pola tetap ada, levelnya kosong.
            $table->foreignId('level_tocfl_id')->nullable()->constrained('level_tocfls')->nullOnDelete();

            // Nama pola yang tampil di daftar materi dan judul soal grammar.
            $table->text('judul');                 // mis. "Kalimat dengan 是"

            // Rumus singkat susunan kalimat, ditampilkan sebagai ringkasan pola.
            $table->text('rumus')->nullable();     // mis. "Subjek + 是 + Kata Benda"

            // Penjelasan lengkap aturan pemakaian. Juga dipakai sebagai "penjelasan" setelah soal dijawab.
            $table->longText('penjelasan')->nullable();

            // Pengecualian dan kesalahan yang sering dilakukan pelajar.
            $table->text('catatan')->nullable();

            // Kata inti pola (JSON array), mis. ["是"] atau ["了","过"].
            // Dipakai generator untuk menentukan kata mana yang dikosongkan pada soal isian,
            // dan untuk mencari kalimat contoh yang memuat pola ini.
            $table->json('kata_kunci')->nullable();

            // KANDIDAT pengecoh tingkat pola (JSON array), mis. ["过","着","的"] untuk pola 了.
            // PERHATIAN: ini bukan jaminan salah. Pada kalimat tertentu kata ini bisa saja benar,
            // jadi generator WAJIB menyaringnya dengan contoh_kalimats.kunci_alternatif.
            // Hanya dipakai sebagai cadangan kalau contoh_kalimats.pengecoh kosong / belum dicek.
            $table->json('pengecoh_umum')->nullable();

            // Urutan tampil dalam satu level.
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();

            $table->index(['level_tocfl_id', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pola_tata_bahasas');
    }
};
