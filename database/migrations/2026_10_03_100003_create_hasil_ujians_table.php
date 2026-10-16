<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Satu baris = satu kali mengerjakan sebuah paket soal (satu percobaan).
        Schema::create('hasil_ujians', function (Blueprint $table) {
            $table->id();

            // Paket dihapus -> riwayat percobaannya (dan jawabannya) ikut terhapus.
            $table->foreignId('grup_soal_id')->constrained('grup_soals')->cascadeOnDelete();

            $table->unsignedSmallInteger('jumlah_soal')->default(0);   // snapshot jumlah soal saat dikerjakan
            $table->unsignedSmallInteger('jumlah_benar')->default(0);
            $table->decimal('skor', 5, 2)->default(0); // 0 - 100
            $table->boolean('lulus')->nullable();      // snapshot: tidak berubah walau nilai_lulus diedit; null = latihan / belum selesai

            $table->timestamp('mulai_pada')->nullable();
            $table->timestamp('selesai_pada')->nullable(); // null = masih berjalan

            $table->timestamps();

            // Cek maks_percobaan & "lanjutkan ujian" (selesai_pada null = masih berjalan)
            $table->index(['grup_soal_id', 'selesai_pada']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_ujians');
    }
};
