<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pengaturan website + target belajar harian. Hanya 1 baris yang dipakai.
        Schema::create('website_settings', function (Blueprint $table) {
            $table->id();

            // Identitas website
            $table->text('nama')->nullable();
            $table->text('slogan')->nullable();
            $table->text('alamat')->nullable();
            $table->text('email')->nullable();
            $table->text('nomor_telepon')->nullable();
            $table->text('logo')->nullable();

            // Media sosial
            $table->text('social_facebook')->nullable();
            $table->text('social_instagram')->nullable();
            $table->text('social_twitter')->nullable();
            $table->text('social_youtube')->nullable();

            // Halaman pengantar / tentang
            $table->text('title_pengantar')->nullable();
            $table->longText('paragraf_pengantar')->nullable();
            $table->text('gambar_pengantar')->nullable();
            $table->longText('about_us')->nullable();

            // Target belajar harian
            $table->unsignedSmallInteger('kartu_baru_per_hari')->default(10);   // kosakata baru per hari
            $table->unsignedSmallInteger('karakter_baru_per_hari')->default(5); // karakter baru per hari (progres_karakters)
            $table->unsignedSmallInteger('radikal_baru_per_hari')->default(2);  // radikal baru per hari (progres_radikals)
            $table->unsignedSmallInteger('batas_review_per_hari')->default(100); // batas review total per hari
            // Jam pergantian hari (0-23): review sebelum jam ini masih dihitung hari kemarin.
            // Statistik harian & streak memakai jam ini dan zona waktu aplikasi (config app.timezone),
            // jadi pastikan app.timezone sesuai tempat kamu belajar.
            $table->unsignedTinyInteger('jam_ganti_hari')->default(4);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_settings');
    }
};
