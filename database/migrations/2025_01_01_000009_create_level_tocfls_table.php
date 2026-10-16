<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Master level TOCFL (6 baris saja: level 1 sampai 6).
        // Dipakai oleh kosakatas, karakters, paragrafs, pola_tata_bahasas, dan grup_soals.
        Schema::create('level_tocfls', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('tingkat')->unique(); // 1 - 6
            $table->char('band', 1);                          // A (level 1-2), B (level 3-4), C (level 5-6)
            $table->string('nama', 30);                       // TOCFL Level 1, TOCFL Level 2, ...
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Filter per band (A / B / C)
            $table->index('band');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('level_tocfls');
    }
};
