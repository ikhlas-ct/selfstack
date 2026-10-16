<?php

// Nomor 100004: berjalan SESUDAH hasil_ujians (100003) dan soals (100002),
// dan SEBELUM riwayat_reviews (100005) yang punya FK ke tabel ini.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Satu baris = jawaban untuk satu soal dalam satu percobaan. Semua soal pilihan ganda A-D,
        // jadi jawaban cukup disimpan sebagai huruf.
        Schema::create('jawaban_ujians', function (Blueprint $table) {
            $table->id();

            // Percobaan dihapus -> jawabannya ikut terhapus.
            $table->foreignId('hasil_ujian_id')->constrained('hasil_ujians')->cascadeOnDelete();

            // Soal dihapus -> jawaban untuk soal itu ikut terhapus (skor di hasil_ujians tetap
            // sebagai snapshot). Mau mempertahankan jawaban lama? Jangan hapus soalnya, set
            // soals.status_validasi = 'ditolak'.
            $table->foreignId('soal_id')->constrained('soals')->cascadeOnDelete();

            // Huruf yang dipilih. null = dilewati.
            $table->enum('jawaban_dipilih', ['A', 'B', 'C', 'D'])->nullable();

            // Urutan pilihan yang TAMPIL kalau pilihan diacak per percobaan, ditulis sebagai 4 huruf
            // pilihan asli menurut posisi tampil, mis. "CADB" = posisi 1 menampilkan pilihan_c, dst.
            // jawaban_dipilih tetap huruf ASLI (pilihan_a..d di soals), bukan posisi layar, sehingga
            // statistik pengecoh tidak salah baca. null = pilihan tampil dengan urutan asli A-B-C-D.
            $table->string('urutan_pilihan', 4)->nullable();

            // Snapshot benar/salah saat dijawab. Tidak dihitung ulang kalau kunci soal diedit kemudian.
            $table->boolean('benar')->default(false);

            // Lama menjawab (ms); null = tidak tercatat.
            $table->unsignedInteger('waktu_jawab_ms')->nullable();

            // Kapan soal ini dijawab (untuk urutan jawaban dan analisis).
            $table->timestamp('dijawab_pada')->nullable();

            // Satu soal hanya boleh dijawab sekali per percobaan.
            $table->unique(['hasil_ujian_id', 'soal_id']);

            // Statistik soal yang sering salah.
            $table->index(['soal_id', 'benar']);

            // Deteksi pengecoh yang sebenarnya juga benar: kalau satu pilihan salah sering dipilih
            // (mis. > 30%), soal itu patut dicurigai berjawaban ganda -> tandai 'perlu_dicek'.
            $table->index(['soal_id', 'jawaban_dipilih']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jawaban_ujians');
    }
};
