<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // PROGRES HAFALAN KOSAKATA. Satu baris progres per kosakata (karena hanya 1 pengguna).
        // Baris ini diperbarui setiap kali ada baris baru di riwayat_reviews (jenis_item = 'kosakata').
        // Progres karakter dan radikal ada di tabel sendiri: progres_karakters dan progres_radikals.
        Schema::create('progres_hafalans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kosakata_id')->unique()->constrained('kosakatas')->cascadeOnDelete();

            // berikutnya = belum dihafal / kartu baru, lupa_dan_ingat = sempat lupa lalu ingat lagi,
            // ingat_sepenuhnya = sudah hafal.
            $table->enum('status', ['ingat_sepenuhnya', 'lupa_dan_ingat', 'berikutnya'])
                  ->default('berikutnya');
            $table->timestamp('pertama_dipelajari_pada')->nullable()->index(); // untuk hitung kartu baru per hari
            $table->timestamp('terakhir_diulang')->nullable();
            $table->unsignedInteger('jumlah_ulang')->default(0);
            $table->unsignedInteger('benar_beruntun')->default(0);
            $table->unsignedSmallInteger('interval_hari')->default(0); // jarak ke review berikutnya
            $table->decimal('ease_factor', 3, 2)->default(2.50);       // faktor kemudahan ala SM-2 (Anki)
            $table->unsignedInteger('jumlah_salah')->default(0);       // lapses: deteksi kata bandel
            $table->boolean('ditandai')->default(false);               // kata sulit / favorit
            $table->boolean('ditangguhkan')->default(false);           // sudah dikuasai, jangan muncul lagi
            $table->timestamp('review_berikutnya')->nullable()->index();
            $table->timestamps();

            // Antrean review hari ini: yang tidak ditangguhkan dan sudah jatuh tempo
            $table->index(['ditangguhkan', 'review_berikutnya']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progres_hafalans');
    }
};
