<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Harus berjalan SESUDAH kosakatas (000010) dan karakters (000020).
return new class extends Migration
{
    public function up(): void
    {
        // Pecahan sebuah kosakata menjadi karakter-karakternya, berurutan dari kiri ke kanan.
        // Contoh 學生 (xuésheng):
        //   urutan 1 -> karakter 學, arti_di_kata "belajar"
        //   urutan 2 -> karakter 生, arti_di_kata "murid / orang yang belajar"
        // Kata satu karakter (如 "我") punya satu baris saja.
        Schema::create('kosakata_karakter', function (Blueprint $table) {
            $table->id();

            // Kosakata dihapus -> pecahannya ikut terhapus.
            $table->foreignId('kosakata_id')->constrained('kosakatas')->cascadeOnDelete();

            // Karakter yang masih dipakai sebuah kata TIDAK boleh dihapus (restrict),
            // supaya pecahan kata tidak hilang diam-diam.
            $table->foreignId('karakter_id')->constrained('karakters')->restrictOnDelete();

            // Posisi karakter di dalam kata (1, 2, 3, ...). Karakter yang sama boleh muncul dua kali
            // dalam satu kata (爸爸), karena itu keunikan dijaga lewat urutan, bukan karakter_id.
            $table->unsignedTinyInteger('urutan');

            // Bacaan karakter di kata INI bila berbeda dari karakters.pinyin,
            // mis. nada ringan (學生 -> sheng) atau perubahan nada (不 + 是 -> bú shì).
            // Kosong = pakai bacaan kamus.
            $table->string('pinyin_di_kata', 100)->collation('utf8mb4_bin')->nullable();

            // Makna karakter DALAM kata ini. Sering lebih sempit atau berbeda dari arti kamus
            // (生 = lahir / hidup / mentah, tapi di 學生 = "murid"). Kosong = pakai karakters.arti_indonesia.
            $table->text('arti_di_kata')->nullable();

            // Penjelasan bagaimana karakter ini membentuk arti kata, mis. 學 (belajar) + 生 (orang) = pelajar.
            // Ditampilkan sebagai pembahasan di bawah kata dan pada soal "arti karakter dalam kata".
            $table->text('penjelasan')->nullable();

            $table->timestamps();

            // Satu posisi hanya boleh diisi satu karakter dalam satu kata.
            $table->unique(['kosakata_id', 'urutan']);

            // Cari semua kata yang memuat karakter tertentu (indeks FK karakter_id sudah dibuat otomatis).
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kosakata_karakter');
    }
};
