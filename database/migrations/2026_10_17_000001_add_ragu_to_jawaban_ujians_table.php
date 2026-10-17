<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jawaban_ujians', function (Blueprint $table) {
            // Tanda "ragu-ragu" yang dipasang sendiri oleh pengguna saat mengerjakan.
            $table->boolean('ragu')->default(false)->after('jawaban_dipilih');
        });
    }

    public function down(): void
    {
        Schema::table('jawaban_ujians', function (Blueprint $table) {
            $table->dropColumn('ragu');
        });
    }
};
