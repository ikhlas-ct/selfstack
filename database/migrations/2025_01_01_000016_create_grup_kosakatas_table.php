<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Harus berjalan SEBELUM grup_soals, karena grup_soals punya FK ke grup_kosakatas.
return new class extends Migration
{
    public function up(): void
    {
        // Grup kosakata buatan sendiri (mis. "Kata sulit minggu ini"), terpisah dari kategori/tema.
        // Dipakai sebagai bahan generator: grup kosakata -> paket soal (grup_soals).
        Schema::create('grup_kosakatas', function (Blueprint $table) {
            $table->id();
            $table->text('nama');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // Tabel pivot: kata mana saja yang ada di sebuah grup.
        // Grup dihapus -> keanggotaannya ikut terhapus. Kata dihapus -> keluar dari semua grup.
        Schema::create('grup_kosakata_kosakata', function (Blueprint $table) {
            $table->id();
            $table->foreignId('grup_kosakata_id')->constrained('grup_kosakatas')->cascadeOnDelete();
            $table->foreignId('kosakata_id')->constrained('kosakatas')->cascadeOnDelete();
            $table->unique(['grup_kosakata_id', 'kosakata_id']); // satu kata hanya sekali dalam satu grup
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grup_kosakata_kosakata');
        Schema::dropIfExists('grup_kosakatas');
    }
};
