<?php

// Nomor 000018 (bukan 000012) karena punya FK ke pola_tata_bahasas (000017).

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Satu baris = satu kalimat contoh. Ini sumber utama soal isian (kata kunci dikosongkan,
        // pilihan A-D) dan soal tata bahasa.
        Schema::create('contoh_kalimats', function (Blueprint $table) {
            $table->id();

            // Kalimat boleh milik kosakata, milik pola tata bahasa, atau keduanya.
            // Kosakata dihapus -> kalimatnya ikut terhapus. Pola dihapus -> kalimat tetap ada.
            $table->foreignId('kosakata_id')->nullable()->constrained('kosakatas')->cascadeOnDelete();
            $table->foreignId('pola_tata_bahasa_id')->nullable()->constrained('pola_tata_bahasas')->nullOnDelete();

            // Isi kalimat: tulisan hanzi, bacaan pinyin, dan terjemahannya.
            $table->text('hanzi');
            $table->text('pinyin');
            $table->text('arti_indonesia');

            // Penjelasan khusus kalimat ini (mengapa pola dipakai di sini). Ditampilkan sebagai pembahasan soal.
            $table->text('catatan_tata_bahasa')->nullable();

            // Kalimat yang sudah dipecah per kata beserta perannya (JSON). Contoh:
            // [
            //   {"hanzi":"我","pinyin":"wǒ","peran":"subjek","kunci":false},
            //   {"hanzi":"是","pinyin":"shì","peran":"predikat","kunci":true},
            //   {"hanzi":"學生","pinyin":"xuéshēng","peran":"objek","kunci":false}
            // ]
            // peran : subjek, predikat, objek, atribut, keterangan, pelengkap
            // kunci : true = kata inti pola yang dikosongkan pada soal isian.
            // ATURAN: untuk dipakai soal isian, isi susunan ini harus punya TEPAT SATU kata kunci=true
            // (satu titik kosong per soal). Kalau satu kalimat butuh dua titik kosong, buat dua baris
            // contoh_kalimats terpisah, supaya pengecoh dan kunci_alternatif tidak bercampur.
            $table->json('susunan')->nullable();

            // ---------------------------------------------------------------
            // PENCEGAH SOAL "JAWABAN BENAR LEBIH DARI SATU"
            // ---------------------------------------------------------------

            // Kata pengecoh yang SUDAH DIPASTIKAN SALAH untuk kata kunci kalimat ini (JSON array),
            // mis. ["过","着","的"]. Dipakai generator grammar agar pilihan tidak benar ganda.
            // Generator hanya boleh memakainya kalau pengecoh_dicek_pada sudah terisi.
            $table->json('pengecoh')->nullable();

            // Kapan pengecoh di atas terakhir kamu verifikasi manual. NULL = belum dicek,
            // jadi generator tidak boleh menganggapnya aman. Isi ulang (atau kosongkan)
            // setiap kali kalimat atau daftar pengecoh diubah.
            $table->timestamp('pengecoh_dicek_pada')->nullable();

            // Kata LAIN yang juga benar kalau mengisi titik kosong kalimat ini (JSON array),
            // mis. kunci "很" -> ["非常","太"]. Generator DILARANG memakai kata di sini sebagai
            // pengecoh. Diisi manual, atau otomatis dari laporan_soals (jenis ambigu_banyak_benar).
            $table->json('kunci_alternatif')->nullable();

            // Path file audio kalimat (upload atau hasil TTS).
            $table->string('audio')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contoh_kalimats');
    }
};
