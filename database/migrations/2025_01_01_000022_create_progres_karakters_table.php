<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// BARU. Harus berjalan SESUDAH karakters (000020).
return new class extends Migration
{
    public function up(): void
    {
        // PROGRES HAFALAN KARAKTER. Strukturnya sama dengan progres_hafalans (kosakata), supaya
        // satu service penjadwalan (SM-2) bisa dipakai untuk kosakata, karakter, dan radikal.
        // Satu baris progres per karakter. Diperbarui dari riwayat_reviews (jenis_item = 'karakter'),
        // yaitu dari soal yang soals.karakter_id-nya terisi, atau dari popup flashcard karakter.
        Schema::create('progres_karakters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karakter_id')->unique()->constrained('karakters')->cascadeOnDelete();

            $table->enum('status', ['ingat_sepenuhnya', 'lupa_dan_ingat', 'berikutnya'])
                  ->default('berikutnya');
            $table->timestamp('pertama_dipelajari_pada')->nullable()->index(); // hitung karakter baru per hari
            $table->timestamp('terakhir_diulang')->nullable();
            $table->unsignedInteger('jumlah_ulang')->default(0);
            $table->unsignedInteger('benar_beruntun')->default(0);
            $table->unsignedSmallInteger('interval_hari')->default(0);  // jarak ke review berikutnya
            $table->decimal('ease_factor', 3, 2)->default(2.50);        // faktor kemudahan ala SM-2
            $table->unsignedInteger('jumlah_salah')->default(0);        // lapses: deteksi karakter bandel
            $table->boolean('ditandai')->default(false);                // karakter sulit / favorit
            $table->boolean('ditangguhkan')->default(false);            // sudah dikuasai, jangan muncul lagi
            $table->timestamp('review_berikutnya')->nullable()->index();
            $table->timestamps();

            $table->index(['ditangguhkan', 'review_berikutnya']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progres_karakters');
    }
};
