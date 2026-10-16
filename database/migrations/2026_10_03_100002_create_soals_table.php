<?php

// Berjalan SESUDAH karakters (000020) dan radikals (000019) karena punya FK ke keduanya.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Satu baris = satu soal PILIHAN GANDA dengan 4 pilihan (A-D) dan tepat satu jawaban benar.
        // Tanpa softDeletes: soal yang dihapus permanen ikut menghapus jawaban_ujians-nya.
        // Untuk "menyembunyikan" soal bermasalah tanpa menghapus, set status_validasi = 'ditolak'
        // (query soal untuk dikerjakan harus mengecualikan status 'ditolak').
        Schema::create('soals', function (Blueprint $table) {
            $table->id();

            // Paket tempat soal ini berada. Paket dihapus -> soalnya ikut terhapus.
            $table->foreignId('grup_soal_id')->constrained('grup_soals')->cascadeOnDelete();

            // ---- Item yang diujikan. Hasil jawaban masuk ke riwayat_reviews lalu memperbarui progres:
            //   kosakata_id -> progres_hafalans
            //   karakter_id -> progres_karakters
            //   radikal_id  -> progres_radikals
            // Soal level karakter / radikal boleh tetap mengisi kosakata_id (kata asal karakter itu) sebagai
            // konteks; service memutuskan apakah jawabannya juga dihitung sebagai review kata.
            $table->foreignId('kosakata_id')->nullable()->constrained('kosakatas')->nullOnDelete();

            // Pola tata bahasa yang diujikan (untuk soal grammar).
            $table->foreignId('pola_tata_bahasa_id')->nullable()->constrained('pola_tata_bahasas')->nullOnDelete();

            // Sumber kalimat (untuk soal grammar) dan bacaan (untuk soal reading) agar bisa ditelusuri.
            // Kalau ada laporan "jawaban ganda", kolom inilah yang menunjukkan kalimat mana yang harus diperbaiki.
            $table->foreignId('contoh_kalimat_id')->nullable()->constrained('contoh_kalimats')->nullOnDelete();
            $table->foreignId('paragraf_id')->nullable()->constrained('paragrafs')->nullOnDelete();

            // Karakter dan radikal yang diujikan (untuk soal level karakter / radikal).
            // Karakter / radikal dihapus -> soalnya tetap ada.
            $table->foreignId('karakter_id')->nullable()->constrained('karakters')->nullOnDelete();
            $table->foreignId('radikal_id')->nullable()->constrained('radikals')->nullOnDelete();

            // Bagian soal. Menentukan tampilan (audio / bacaan / kalimat) dan cara pengelompokan skor.
            // Daftar nilai dijaga di kode (konstanta di model), bukan enum, supaya menambah bagian
            // baru tidak perlu migrasi ulang:
            //   listening   = soal dengar (pakai audio)
            //   reading     = soal bacaan (pakai paragraf)
            //   tata_bahasa = soal grammar (isian pola)
            //   kosakata    = soal kata (hanzi_ke_arti, arti_ke_hanzi, hanzi_ke_pinyin, dst.)
            //   karakter    = soal karakter dan radikal
            $table->string('bagian', 20);

            // Tipe soal (daftar dijaga di kode). Semuanya PILIHAN GANDA A-D:
            //   Kosakata : hanzi_ke_arti, arti_ke_hanzi, hanzi_ke_pinyin, dengar_ke_hanzi
            //   Grammar  : grammar_isian
            //   Karakter : karakter_ke_arti, arti_ke_karakter, karakter_ke_radikal,
            //              radikal_ke_arti, arti_karakter_dalam_kata ("arti 生 dalam 學生?")
            // Pakai satu penamaan yang sama di generator dan di sini.
            $table->string('tipe', 50)->nullable();

            // 1 = mudah, 2 = sedang, 3 = sulit
            $table->unsignedTinyInteger('tingkat')->default(1);

            // Asal soal: 'generator' (kode), 'ai' (dibuatkan Claude / AI), atau 'manual'.
            $table->string('sumber', 20)->default('manual');

            // otomatis_lolos : lolos semua aturan penjaga di generator
            // perlu_dicek    : butuh dicek admin (soal dari AI, atau yang ditandai statistik / laporan)
            // disetujui      : sudah dicek admin
            // ditolak        : jangan dipakai (cara menyembunyikan soal tanpa menghapusnya)
            // DEFAULT 'perlu_dicek' (aman): soal otomatis / AI yang lupa diberi status tidak langsung
            // dianggap sah. Form soal buatan tangan yang mengisi 'disetujui' secara eksplisit.
            $table->string('status_validasi', 20)->default('perlu_dicek');

            // Alasan status_validasi bukan 'disetujui', mis. "Pilihan C ternyata juga benar" atau
            // "pengecoh B sinonim kunci". Diisi generator, laporan, atau kamu sendiri.
            $table->text('catatan_validasi')->nullable();

            // Kapan terakhir kamu memeriksa soal ini secara manual. NULL = belum pernah diperiksa.
            $table->timestamp('dicek_pada')->nullable();

            // Sidik jari soal (hash dari tipe + kalimat/kosakata sumber + pilihan yang sudah diurutkan).
            // Dipakai generator untuk mendeteksi soal kembar dalam satu paket. Sengaja bukan UNIQUE
            // supaya generator yang memutuskan (lewati / ganti) dan soal manual yang sengaja mirip tetap bisa disimpan.
            $table->string('sidik_soal', 64)->nullable();

            // Urutan tampil di dalam paket, dan bobot nilai soal.
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->unsignedTinyInteger('poin')->default(1);

            $table->longText('paragraf')->nullable();   // reading (isi dari Summernote); untuk isian: kalimat bertitik kosong
            $table->text('audio')->nullable();          // listening (path mp3)

            // Pertanyaan + 4 pilihan. Semua pilihan WAJIB terisi dan harus berbeda satu sama lain
            // (generator menolak soal dengan pilihan kembar). jawaban_benar = huruf pilihan yang benar.
            $table->text('pertanyaan');
            $table->text('pilihan_a');
            $table->text('pilihan_b');
            $table->text('pilihan_c');
            $table->text('pilihan_d');
            $table->enum('jawaban_benar', ['A', 'B', 'C', 'D']);

            // Pembahasan yang tampil setelah soal dijawab.
            $table->longText('penjelasan')->nullable();

            // Jejak pembuatan soal (JSON): asal tiap pengecoh, tingkat pengecoh, teks TTS, dll. Contoh:
            // {
            //   "pengecoh": [{"teks":"过","asal":"kalimat"},{"teks":"着","asal":"pola"},{"teks":"的","asal":"acak"}],
            //   "tts": "我是學生",
            //   "versi_generator": 2
            // }
            // asal: kalimat (contoh_kalimats.pengecoh), pola (pola_tata_bahasas.pengecoh_umum),
            //       relasi (kosakata_relasis / karakter_relasis), acak (kosakata lain)
            // Untuk soal dari AI, catat juga model / versi prompt di sini.
            // Berguna untuk mencari asal pengecoh yang ternyata benar.
            $table->json('meta')->nullable();

            $table->timestamps();

            // Mempercepat pengambilan soal per grup menurut urutan
            $table->index(['grup_soal_id', 'urutan']);
            // Skor per bagian dalam satu paket
            $table->index(['grup_soal_id', 'bagian']);
            $table->index(['kosakata_id', 'tipe']);
            $table->index(['karakter_id', 'tipe']);
            $table->index(['tingkat', 'status_validasi']);
            // Antrean pemeriksaan admin: soal berstatus perlu_dicek dalam satu paket
            $table->index(['grup_soal_id', 'status_validasi']);
            // Cek soal kembar saat generate
            $table->index('sidik_soal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soals');
    }
};
