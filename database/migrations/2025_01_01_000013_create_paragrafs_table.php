<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Satu baris = satu bacaan (paragraf). Dipakai halaman bacaan dan soal bagian "reading"
        // (soals.paragraf_id menunjuk ke sini).
        Schema::create('paragrafs', function (Blueprint $table) {
            $table->id();
            // Level dihapus -> bacaan tetap ada, levelnya kosong.
            $table->foreignId('level_tocfl_id')->nullable()->constrained('level_tocfls')->nullOnDelete();
            $table->text('judul');
            $table->text('hanzi');
            $table->text('pinyin');
            $table->text('arti_indonesia');
            // Penjelasan tata bahasa yang muncul di bacaan (boleh panjang, boleh HTML dari editor).
            $table->longText('penjelasan_tata_bahasa')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paragrafs');
    }
};
