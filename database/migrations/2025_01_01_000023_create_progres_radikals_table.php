<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// BARU. Harus berjalan SESUDAH radikals (000019).
return new class extends Migration
{
    public function up(): void
    {
        // PROGRES HAFALAN RADIKAL. Struktur sama dengan progres_hafalans dan progres_karakters.
        // Diperbarui dari riwayat_reviews (jenis_item = 'radikal'), yaitu dari soal yang
        // soals.radikal_id-nya terisi (tipe radikal_ke_arti dst.), atau dari popup flashcard radikal.
        Schema::create('progres_radikals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('radikal_id')->unique()->constrained('radikals')->cascadeOnDelete();

            $table->enum('status', ['ingat_sepenuhnya', 'lupa_dan_ingat', 'berikutnya'])
                  ->default('berikutnya');
            $table->timestamp('pertama_dipelajari_pada')->nullable()->index(); // hitung radikal baru per hari
            $table->timestamp('terakhir_diulang')->nullable();
            $table->unsignedInteger('jumlah_ulang')->default(0);
            $table->unsignedInteger('benar_beruntun')->default(0);
            $table->unsignedSmallInteger('interval_hari')->default(0);  // jarak ke review berikutnya
            $table->decimal('ease_factor', 3, 2)->default(2.50);        // faktor kemudahan ala SM-2
            $table->unsignedInteger('jumlah_salah')->default(0);        // lapses
            $table->boolean('ditandai')->default(false);                // radikal sulit / favorit
            $table->boolean('ditangguhkan')->default(false);            // sudah dikuasai, jangan muncul lagi
            $table->timestamp('review_berikutnya')->nullable()->index();
            $table->timestamps();

            $table->index(['ditangguhkan', 'review_berikutnya']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progres_radikals');
    }
};
