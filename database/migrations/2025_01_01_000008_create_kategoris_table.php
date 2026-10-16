<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kategori = TEMA kosakata (kata ganti orang, keluarga, makanan, ...).
        // Dipakai kosakatas.kategori_id. Ini BUKAN jenis kata (kata kerja, kata benda, dst);
        // jenis kata ada di kosakatas.jenis_kata.
        Schema::create('kategoris', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50)->unique();   // nama tema, tidak boleh kembar
            $table->text('keterangan')->nullable(); // deskripsi tema (opsional)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kategoris');
    }
};
