<?php

// Nomor 100005 (bukan 000014) karena punya FK ke hasil_ujians (100003) dan jawaban_ujians (100004).

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Satu baris = satu kali sebuah ITEM (kosakata / karakter / radikal) direview.
        // Dari sini progres_hafalans, progres_karakters, dan progres_radikals dihitung,
        // dan dari sini juga statistik harian & streak dibuat.
        Schema::create('riwayat_reviews', function (Blueprint $table) {
            $table->id();

            // Jenis item yang direview. Saat review dibuat, TEPAT SATU dari tiga kolom FK di bawah
            // terisi, sesuai jenis_item ini (dijaga di kode / service, bukan oleh database).
            // Kalau item-nya kemudian dihapus, FK-nya menjadi kosong tetapi barisnya tetap ada.
            $table->enum('jenis_item', ['kosakata', 'karakter', 'radikal'])->default('kosakata');

            // Item dihapus -> FK menjadi kosong (nullOnDelete) dan baris review TETAP ada, supaya
            // statistik harian dan streak tidak berubah walau katanya sudah dihapus.
            $table->foreignId('kosakata_id')->nullable()->constrained('kosakatas')->nullOnDelete();
            $table->foreignId('karakter_id')->nullable()->constrained('karakters')->nullOnDelete();
            $table->foreignId('radikal_id')->nullable()->constrained('radikals')->nullOnDelete();

            $table->enum('sumber', ['popup', 'soal']);

            // Arah uji: hanzi_ke_arti, arti_ke_hanzi, hanzi_ke_pinyin, dengar_ke_hanzi, dst.
            // Untuk review dari soal, nilainya sama dengan soals.tipe. Null untuk popup flashcard.
            $table->string('arah', 50)->nullable();

            // Latihan/ujian yang menghasilkan review ini (null = dari popup flashcard).
            $table->foreignId('hasil_ujian_id')->nullable()->constrained('hasil_ujians')->nullOnDelete();

            // Jawaban yang menghasilkan review ini (null = dari popup flashcard).
            // Dengan unique(jawaban_ujian_id, jenis_item), satu jawaban hanya menghasilkan SATU review
            // per jenis item, jadi memproses ulang hasil ujian tidak membuat review ganda. Satu jawaban
            // soal karakter masih boleh menghasilkan review karakter DAN review kosakata.
            $table->foreignId('jawaban_ujian_id')->nullable()->constrained('jawaban_ujians')->nullOnDelete();

            // Status hafalan item ini SEBELUM dan SESUDAH review (nilai yang sama dengan kolom status
            // di progres_*). Dua kolom dipisah supaya jelas mana yang lama dan mana yang baru.
            $table->enum('status_sebelum', ['ingat_sepenuhnya', 'lupa_dan_ingat', 'berikutnya']);
            $table->enum('status_sesudah', ['ingat_sepenuhnya', 'lupa_dan_ingat', 'berikutnya']);

            $table->boolean('benar');
            $table->unsignedInteger('durasi_ms')->nullable(); // lama menjawab (ms); null = tidak tercatat
            $table->timestamp('direview_pada')->useCurrent();

            $table->unique(['jawaban_ujian_id', 'jenis_item'], 'riwayat_reviews_jawaban_item_unik');

            // Riwayat per item
            $table->index(['kosakata_id', 'direview_pada']);
            $table->index(['karakter_id', 'direview_pada']);
            $table->index(['radikal_id', 'direview_pada']);
            $table->index('direview_pada'); // statistik harian & streak
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_reviews');
    }
};
