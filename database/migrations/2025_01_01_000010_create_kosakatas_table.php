<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Satu baris = satu kata yang dihafal.
        Schema::create('kosakatas', function (Blueprint $table) {
            $table->id();

            // kategori = TEMA / kelompok kata (kata ganti orang, keluarga, makanan, ...)
            $table->foreignId('kategori_id')->nullable()->constrained('kategoris')->nullOnDelete();
            $table->foreignId('level_tocfl_id')->nullable()->constrained('level_tocfls')->nullOnDelete();

            // Hanzi tradisional. Kata dua karakter atau lebih (學生) dipecah per karakter di tabel
            // kosakata_karakter -> karakters -> radikals, jadi tidak perlu kolom pecahan di sini.
            // utf8mb4_bin: SAMA dengan karakters/radikals, supaya karakter yang mirip tidak dianggap
            // sama oleh unique dan pencarian.
            $table->string('hanzi', 255)->collation('utf8mb4_bin');
            $table->text('zhuyin')->nullable();             // bopomofo (ㄋㄧˇ ㄏㄠˇ), opsional
            // utf8mb4_bin: membedakan tanda nada (wèi / wéi) agar unique hanzi+pinyin tidak bentrok di MySQL
            $table->string('pinyin', 255)->collation('utf8mb4_bin');
            $table->text('baca_indonesia')->nullable();
            $table->text('english')->nullable();

            // Arti LENGKAP untuk ditampilkan di kartu / popup. Boleh panjang dan multi-makna,
            // mis. "belajar; mempelajari".
            $table->text('arti_indonesia')->nullable();

            // Arti SINGKAT (satu glos pendek) khusus untuk PILIHAN JAWABAN soal pilihan ganda.
            // Kalau kosong, generator memakai arti_indonesia (lebih berisiko panjang / ambigu).
            // ATURAN generator: pengecoh dilarang punya arti_singkat yang sama dengan kunci
            // (dua kata beda tapi artinya sama = dua pilihan benar).
            $table->string('arti_singkat', 100)->nullable();

            // JENIS KATA (tata bahasa), terpisah dari tema di atas.
            // Daftar nilai dijaga di kode (konstanta di model), mis.:
            // kata_benda, kata_kerja, kata_sifat, kata_ganti, kata_bilangan,
            // kata_penggolong, kata_keterangan, kata_depan, kata_sambung, partikel, kata_seru
            $table->string('jenis_kata', 50)->nullable();

            // Klasifikasi lanjutan, mis. untuk kata kerja:
            // transitif, intransitif, modal, penghubung, terpisah
            $table->string('sub_jenis', 30)->nullable();

            // Penjelasan kegunaan kata: kapan / dalam situasi apa kata ini dipakai.
            $table->text('kegunaan')->nullable();

            // Kata pengait / mnemonic / catatan pribadi untuk membantu hafalan.
            $table->text('kata_pengait')->nullable();

            // Path file audio (upload atau hasil TTS), dipakai soal listening.
            $table->string('audio')->nullable();

            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();

            // Kata dihapus permanen (tanpa softDeletes): progres_hafalans, kalimat contoh, pecahan kata,
            // dan keanggotaan grup ikut terhapus; soals.kosakata_id menjadi kosong; riwayat_reviews TETAP
            // ada (kosakata_id menjadi kosong) supaya statistik harian dan streak tidak berubah.

            // Hanzi yang sama boleh muncul lagi selama pinyin-nya berbeda (行 xíng / háng).
            // Konsekuensi: satu hanzi + satu pinyin = SATU baris. Kalau kata itu punya beberapa makna
            // (會 huì "bisa" / "rapat"), gabungkan di arti_indonesia dan isi arti_singkat dengan makna utama.
            $table->unique(['hanzi', 'pinyin']);

            // Mempercepat pencarian pengecoh: kata sejenis (tema sama / jenis sama)
            $table->index(['kategori_id', 'jenis_kata']);

            // Filter per level TOCFL + urutan tampil
            $table->index(['level_tocfl_id', 'urutan']);
        });

        // Relasi antar kosakata: sumber pengecoh yang lebih baik daripada sekadar "tema sama",
        // sekaligus daftar kata yang DILARANG jadi pengecoh.
        //   sinonim    = arti sama / dekat  -> DILARANG jadi pengecoh soal arti (bisa ikut benar)
        //   antonim    = lawan arti         -> pengecoh yang bagus
        //   mirip      = mudah tertukar (bentuk / arti)
        //   mirip_bunyi = bunyi mudah tertukar -> DILARANG jadi pengecoh soal dengar (listening),
        //                 pilihannya bisa terdengar sama dengan kunci
        // Relasi tidak otomatis dua arah: "A sinonim B" tidak berarti "B sinonim A".
        // Saat query, baca KEDUA arah (kosakata_id dan kosakata_terkait_id).
        Schema::create('kosakata_relasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kosakata_id')->constrained('kosakatas')->cascadeOnDelete();
            $table->foreignId('kosakata_terkait_id')->constrained('kosakatas')->cascadeOnDelete();
            $table->enum('jenis', ['sinonim', 'antonim', 'mirip', 'mirip_bunyi']);
            $table->timestamps();

            $table->unique(['kosakata_id', 'kosakata_terkait_id', 'jenis'], 'kosakata_relasis_unik');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kosakata_relasis');
        Schema::dropIfExists('kosakatas');
    }
};
