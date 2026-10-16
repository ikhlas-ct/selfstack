<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Satu baris = satu paket soal (latihan atau ujian) yang berisi banyak soal PILIHAN GANDA (A-D).
        Schema::create('grup_soals', function (Blueprint $table) {
            $table->id();

            // Level TOCFL paket ini. Level dihapus -> paket tetap ada.
            $table->foreignId('level_tocfl_id')->nullable()->constrained('level_tocfls')->nullOnDelete();

            // Latihan hasil generate dari grup kosakata. Grup dihapus TIDAK menghapus
            // latihan (nullOnDelete), karena latihan bisa saja sudah pernah dikerjakan.
            $table->foreignId('grup_kosakata_id')->nullable()->constrained('grup_kosakatas')->nullOnDelete();

            // latihan = bebas diulang, tanpa lulus/gagal. ujian = punya nilai lulus & batas percobaan.
            $table->enum('jenis', ['latihan', 'ujian'])->default('latihan');

            // Asal paket: 'manual' (dibuat tangan), 'generator' (dibuat otomatis oleh kode),
            // atau 'ai' (dibuatkan Claude / AI). Membantu memisahkan soal otomatis yang perlu
            // diperiksa dari soal buatan sendiri.
            $table->string('sumber', 20)->default('manual');

            // Pengaturan yang dipakai generator (JSON), mis.
            // {"tipe":["hanzi_ke_arti","grammar_isian"],"jumlah":20,"level_tocfl_id":2}
            // Disimpan supaya paket bisa dibuat ulang dengan pengaturan yang sama.
            $table->json('pengaturan_generator')->nullable();

            $table->text('judul');
            $table->text('deskripsi')->nullable();
            $table->unsignedSmallInteger('durasi_menit')->nullable();   // kosong = tanpa batas waktu
            $table->unsignedTinyInteger('nilai_lulus')->nullable();    // khusus ujian (0 - 100)
            $table->unsignedTinyInteger('maks_percobaan')->nullable(); // kosong = tanpa batas

            // false = disembunyikan. Paket hasil generator / AI dibuat nonaktif sampai kamu selesai memeriksanya.
            $table->boolean('aktif')->default(true);

            $table->timestamps();

            // Paket dihapus permanen: soal, riwayat percobaan (hasil_ujians), dan jawabannya ikut terhapus.
            // Kalau cuma mau menyembunyikan paket tanpa kehilangan riwayat, set aktif = false.

            $table->index(['jenis', 'aktif']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grup_soals');
    }
};
