<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambah kolom `nama` (nama tampil) dan `moto` ke tabel users.
 * Ada pengecekan hasColumn supaya aman kalau kolomnya sudah ditambah manual lewat SQL.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'nama')) {
                $table->string('nama', 100)->nullable()->after('username');
            }
            if (! Schema::hasColumn('users', 'moto')) {
                $table->string('moto', 150)->nullable()->after('email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['nama', 'moto'] as $kolom) {
                if (Schema::hasColumn('users', $kolom)) {
                    $table->dropColumn($kolom);
                }
            }
        });
    }
};
