<?php

namespace Database\Seeders;

use App\Models\LevelTocfl;
use Illuminate\Database\Seeder;

/**
 * Mengisi master level TOCFL (6 baris). Aman dijalankan berulang (updateOrCreate per tingkat).
 * Padanan CEFR bersifat perkiraan umum.
 */
class LevelTocflSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [1, 'A', 'TOCFL Level 1', 'Pemula (Entry-Level Basic), kira-kira setara CEFR A1. Kata dan kalimat paling dasar untuk kebutuhan sehari-hari.'],
            [2, 'A', 'TOCFL Level 2', 'Dasar (Basic), kira-kira setara CEFR A2. Percakapan sehari-hari yang sederhana: berbelanja, bertanya arah, hal-hal rutin.'],
            [3, 'B', 'TOCFL Level 3', 'Menengah (Intermediate), kira-kira setara CEFR B1. Topik umum sehari-hari dan pekerjaan, serta bacaan yang lebih panjang.'],
            [4, 'B', 'TOCFL Level 4', 'Menengah lanjut (Advanced), kira-kira setara CEFR B2. Menyampaikan pendapat dan memahami topik yang lebih abstrak.'],
            [5, 'C', 'TOCFL Level 5', 'Mahir (High-Advanced), kira-kira setara CEFR C1. Memahami bacaan dan pembicaraan yang kompleks dengan lancar.'],
            [6, 'C', 'TOCFL Level 6', 'Fasih (Fluency), kira-kira setara CEFR C2. Penguasaan mendekati penutur asli untuk berbagai topik.'],
        ];

        foreach ($data as [$tingkat, $band, $nama, $keterangan]) {
            LevelTocfl::updateOrCreate(
                ['tingkat' => $tingkat],
                ['band' => $band, 'nama' => $nama, 'keterangan' => $keterangan]
            );
        }
    }
}
