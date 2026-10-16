<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Berjalan SESUDAH soals (100002) dan hasil_ujians (100003).
return new class extends Migration
{
    public function up(): void
    {
        // Laporan masalah pada sebuah soal, mis. "pilihan B ternyata juga benar".
        // Alurnya: lapor -> soal diubah jadi 'perlu_dicek' -> kata bermasalah dimasukkan ke
        // contoh_kalimats.kunci_alternatif -> laporan ditandai 'ditangani'.
        Schema::create('laporan_soals', function (Blueprint $table) {
            $table->id();

            // Soal yang dilaporkan. Soal dihapus permanen -> laporannya ikut terhapus.
            $table->foreignId('soal_id')->constrained('soals')->cascadeOnDelete();

            // Percobaan tempat kamu menemukan masalahnya (opsional). Percobaan dihapus -> laporan tetap ada.
            $table->foreignId('hasil_ujian_id')->nullable()->constrained('hasil_ujians')->nullOnDelete();

            // ambigu_banyak_benar : lebih dari satu pilihan benar
            // kunci_salah         : jawaban_benar yang tersimpan keliru
            // tidak_ada_benar     : semua pilihan salah
            // typo                : salah ketik di soal / pilihan / penjelasan
            // audio_bermasalah    : suara tidak terdengar atau tidak cocok
            // lainnya
            $table->string('jenis', 30);

            // Pilihan yang menurutmu juga benar / bermasalah (untuk jenis ambigu_banyak_benar).
            $table->enum('pilihan_bermasalah', ['A', 'B', 'C', 'D'])->nullable();

            // SNAPSHOT teks pilihan itu saat dilaporkan. Huruf saja tidak cukup: kalau soal diedit atau
            // pilihannya diganti, huruf tidak lagi menunjuk teks yang sama. Teks ini yang dimasukkan ke
            // kunci_alternatif kalimat sumbernya.
            $table->text('teks_pilihan_bermasalah')->nullable();

            // Catatan bebas: alasan kenapa pilihan itu juga benar.
            $table->text('catatan')->nullable();

            // baru       : belum ditindaklanjuti
            // ditangani  : soal / data sumber sudah diperbaiki
            // diabaikan  : ternyata soalnya benar, laporan tidak valid
            $table->enum('status', ['baru', 'ditangani', 'diabaikan'])->default('baru');
            $table->timestamp('ditangani_pada')->nullable();

            $table->timestamps();

            // Antrean laporan yang belum ditangani
            $table->index(['status', 'created_at']);
            // Riwayat laporan per soal
            $table->index(['soal_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_soals');
    }
};
