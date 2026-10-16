<?php

namespace Database\Seeders;

use App\Models\WebsiteSetting;
use Illuminate\Database\Seeder;

/**
 * Mengisi pengaturan website Chogwen (nama, slogan, kontak, media sosial, pengantar, tentang kami).
 *
 * - Memakai satu baris pengaturan yang sama dengan WebsiteSetting::ambil(), jadi nilai bawaan
 *   migrasi (kartu baru per hari, batas review, jam ganti hari, dst.) tetap dipakai.
 * - Hanya mengisi kolom yang MASIH KOSONG, sehingga perubahan yang sudah kamu simpan lewat
 *   halaman admin tidak tertimpa kalau seeder dijalankan ulang.
 * - Alamat, email, nomor telepon, dan akun media sosial di bawah adalah data contoh;
 *   ganti dengan data aslimu.
 */
class WebsiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $setting = WebsiteSetting::ambil();

        foreach ($this->isi() as $kolom => $nilai) {
            if (blank($setting->{$kolom})) {
                $setting->{$kolom} = $nilai;
            }
        }

        $setting->save();
    }

    /** @return array<string, string|null> */
    private function isi(): array
    {
        return [
            'nama'   => 'Chogwen',
            'slogan' => 'Belajar Mandarin pelan-pelan, hafal sampai lekat.',

            // Data contoh, ganti dengan data asli.
            'alamat'        => 'Jl. Contoh Raya No. 88, Jakarta Selatan, Indonesia',
            'email'         => 'halo@chogwen.test',
            'nomor_telepon' => '+62 812-0000-0000',

            // Diisi lewat halaman admin (unggah file); null supaya tidak menunjuk ke file yang tidak ada.
            'logo'             => null,
            'gambar_pengantar' => null,

            // Akun contoh, ganti dengan akun asli.
            'social_facebook'  => 'https://facebook.com/chogwen',
            'social_instagram' => 'https://instagram.com/chogwen',
            'social_twitter'   => 'https://x.com/chogwen',
            'social_youtube'   => 'https://youtube.com/@chogwen',

            'title_pengantar'    => 'Selamat datang di Chogwen',
            'paragraf_pengantar' => implode("\n\n", [
                'Chogwen membantumu belajar bahasa Mandarin lewat cara yang sederhana: sedikit demi sedikit, tetapi rutin. '
                . 'Mulai dari radikal, karakter, sampai kosakata dan tata bahasa, semuanya disusun mengikuti level TOCFL.',
                'Setiap hari kamu hanya perlu menyelesaikan kartu yang dijadwalkan. Sistem pengulangan berjarak akan '
                . 'mengingatkanmu pada kata yang hampir terlupa, jadi waktu belajarmu dipakai untuk hal yang benar-benar perlu.',
            ]),

            'about_us' => implode("\n\n", [
                'Chogwen adalah tempat belajar bahasa Mandarin berbahasa Indonesia untuk pemula sampai tingkat lanjut. '
                . 'Materi kami mengikuti enam level TOCFL, dengan penjelasan yang ditulis dalam bahasa Indonesia '
                . 'supaya lebih mudah dipahami.',
                'Kami percaya hafalan yang kuat datang dari pengulangan kecil yang konsisten. Karena itu Chogwen '
                . 'memadukan kartu hafalan, latihan soal, dan contoh kalimat yang bisa kamu pakai langsung.',
                'Punya saran atau menemukan kesalahan? Hubungi kami lewat email atau media sosial di atas. '
                . 'Masukanmu membantu Chogwen terus berkembang.',
            ]),
        ];
    }
}
