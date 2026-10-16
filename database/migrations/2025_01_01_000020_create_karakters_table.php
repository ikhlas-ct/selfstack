<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Harus berjalan SESUDAH radikals (000019) dan level_tocfls (000009),
// dan SEBELUM kosakata_karakter (000021), progres_karakters (000022), serta soals (100002).
return new class extends Migration
{
    public function up(): void
    {
        // Satu baris = satu KARAKTER tunggal (學, 生, 行) beserta artinya sendiri.
        // Kata 學生 (xuésheng) disusun dari dua baris di sini lewat tabel kosakata_karakter.
        // Progres hafalan karakter ada di progres_karakters (000022).
        Schema::create('karakters', function (Blueprint $table) {
            $table->id();

            // Radikal utama menurut kamus. Radikal dihapus -> karakter tetap ada.
            $table->foreignId('radikal_id')->nullable()->constrained('radikals')->nullOnDelete();

            // Level TOCFL tempat karakter ini mulai dipelajari.
            $table->foreignId('level_tocfl_id')->nullable()->constrained('level_tocfls')->nullOnDelete();

            // Satu karakter. utf8mb4_bin agar karakter yang mirip tidak dianggap sama oleh UNIQUE.
            $table->string('hanzi', 8)->collation('utf8mb4_bin');
            $table->text('zhuyin')->nullable();         // bopomofo, opsional
            // Bacaan KAMUS karakter ini. Karakter berbunyi ganda (行 xíng / háng) dibuat jadi
            // beberapa baris (satu per bacaan) karena artinya juga berbeda.
            $table->string('pinyin', 255)->collation('utf8mb4_bin');
            $table->text('baca_indonesia')->nullable();
            $table->text('arti_indonesia');             // arti karakter sendirian, mis. 學 = "belajar"
            $table->text('english')->nullable();

            $table->unsignedTinyInteger('jumlah_goresan')->nullable(); // total goresan karakter

            // Bentuk radikal yang tampil di dalam karakter ini, mis. "氵" untuk 河 (radikal 水).
            // Hanya diisi kalau berbeda dari radikals.hanzi.
            $table->string('bentuk_radikal', 8)->nullable();

            // Komponen lain di luar radikal utama (JSON), berguna untuk menghafal. Contoh untuk 語:
            // [{"hanzi":"言","peran":"makna"},{"hanzi":"吾","peran":"bunyi"}]
            // peran: makna (memberi arti), bunyi (memberi petunjuk bunyi), lainnya
            // Dipakai generator: komponen di sini TIDAK boleh jadi pengecoh pada soal
            // "radikal dari karakter ini", karena bisa ikut dianggap benar.
            $table->json('komponen')->nullable();

            // Kata pengait / cerita untuk mengingat karakter ini.
            $table->text('kata_pengait')->nullable();
            $table->text('catatan')->nullable();

            // Path file audio karakter (upload atau hasil TTS).
            $table->string('audio')->nullable();

            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();

            // Hanzi yang sama boleh muncul lagi selama pinyin-nya berbeda.
            $table->unique(['hanzi', 'pinyin']);

            // Filter per level + urutan tampil
            $table->index(['level_tocfl_id', 'urutan']);
            // Cari semua karakter dengan radikal tertentu sudah tercakup indeks FK radikal_id.
        });

        // Relasi antar karakter: sumber pengecoh yang lebih baik daripada karakter acak.
        // sinonim      = arti sama / dekat
        // mirip_bentuk = bentuk mudah tertukar (己 / 已 / 巳, 未 / 末)
        // mirip_bunyi  = bunyi mudah tertukar
        // Untuk soal "arti karakter", sinonim TIDAK boleh dipakai sebagai pengecoh.
        // Untuk soal dengar (listening), mirip_bunyi TIDAK boleh dipakai sebagai pengecoh.
        // Relasi tidak otomatis dua arah: baca kedua arah saat query.
        Schema::create('karakter_relasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karakter_id')->constrained('karakters')->cascadeOnDelete();
            $table->foreignId('karakter_terkait_id')->constrained('karakters')->cascadeOnDelete();
            $table->enum('jenis', ['sinonim', 'mirip_bentuk', 'mirip_bunyi']);
            $table->timestamps();

            $table->unique(['karakter_id', 'karakter_terkait_id', 'jenis'], 'karakter_relasis_unik');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('karakter_relasis');
        Schema::dropIfExists('karakters');
    }
};
