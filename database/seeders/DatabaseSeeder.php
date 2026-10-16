<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Urutan tidak masalah di sini (tidak ada FK antar ketiganya),
        // tetapi ketiganya harus dipanggil agar datanya masuk.
        $this->call([
            LevelTocflSeeder::class,      // 6 level TOCFL
            RadikalSeeder::class,         // 214 radikal Kangxi
            WebsiteSettingSeeder::class,  // 1 baris pengaturan + target belajar
        ]);
    }
}
