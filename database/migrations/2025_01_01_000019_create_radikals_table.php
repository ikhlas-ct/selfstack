<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Harus berjalan SEBELUM karakters (karakters punya FK ke tabel ini).
return new class extends Migration
{
    public function up(): void
    {
        // Satu baris = satu radikal / bushou (部首) menurut kamus Kangxi (214 radikal).
        // Progres hafalan radikal ada di progres_radikals (000023).
        Schema::create('radikals', function (Blueprint $table) {
            $table->id();

            // Nomor urut radikal di kamus Kangxi (1 - 214). Boleh kosong untuk radikal buatan sendiri.
            $table->unsignedTinyInteger('nomor_kangxi')->nullable()->unique();

            // Bentuk baku radikal, mis. 水. utf8mb4_bin agar bentuk mirip tidak dianggap sama.
            $table->string('hanzi', 8)->collation('utf8mb4_bin')->unique();

            // Bentuk lain radikal saat jadi bagian karakter (JSON array), mis. 水 -> ["氵","氺"],
            // 手 -> ["扌"], 心 -> ["忄"]. Karakter menyimpan bentuk yang dipakainya di karakters.bentuk_radikal.
            $table->json('varian')->nullable();

            $table->string('pinyin', 100);              // bacaan radikal, mis. "shuǐ"
            $table->text('arti_indonesia');             // mis. "air"

            // Penjelasan makna / jenis kata yang biasa memakai radikal ini,
            // mis. "Karakter dengan 氵 biasanya berhubungan dengan air atau cairan".
            // Ini yang ditampilkan sebagai petunjuk saat belajar.
            $table->text('keterangan')->nullable();

            $table->unsignedTinyInteger('jumlah_goresan')->nullable(); // goresan radikal bentuk baku

            $table->unsignedSmallInteger('urutan')->default(0);        // urutan tampil (bawaan: nomor Kangxi)
            $table->timestamps();

            $table->index('jumlah_goresan'); // daftar radikal dikelompokkan menurut goresan
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('radikals');
    }
};
