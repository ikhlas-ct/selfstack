<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Aplikasi pribadi: hanya ada 1 user (kamu sendiri), jadi tidak perlu role/status.
        //
        // CATATAN kalau nanti dibuka untuk banyak pengguna: tabel berikut harus diberi kolom user_id
        // (dan unique/index-nya disesuaikan):
        //   progres_hafalans, progres_karakters, progres_radikals,
        //   hasil_ujians, riwayat_reviews, laporan_soals.
        // Target belajar harian di website_settings juga harus dipindah ke pengaturan per pengguna.
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 100);
            $table->string('email', 100)->unique();
            $table->string('password', 255);
            $table->string('foto', 255)->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        // Tabel bawaan Laravel untuk fitur "lupa password".
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Tabel bawaan Laravel kalau SESSION_DRIVER=database.
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
