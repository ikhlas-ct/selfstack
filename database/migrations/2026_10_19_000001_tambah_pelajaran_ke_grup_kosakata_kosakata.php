<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Grup kosakata = satu buku. Nomor pelajaran (lesson) disimpan di tabel pivot,
 * karena kata yang sama bisa muncul di pelajaran berbeda pada buku yang berbeda.
 * NULL = belum ditentukan pelajarannya (data lama / hasil import Excel).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('grup_kosakata_kosakata', function (Blueprint $table) {
            $table->unsignedSmallInteger('pelajaran')->nullable()->after('kosakata_id');
            $table->index(['grup_kosakata_id', 'pelajaran'], 'gkk_grup_pelajaran_index');
        });
    }

    public function down(): void
    {
        Schema::table('grup_kosakata_kosakata', function (Blueprint $table) {
            $table->dropIndex('gkk_grup_pelajaran_index');
            $table->dropColumn('pelajaran');
        });
    }
};
