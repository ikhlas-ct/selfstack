<?php

namespace Database\Seeders;

use App\Models\Radikal;
use Illuminate\Database\Seeder;

/**
 * Mengisi 214 radikal Kangxi (aksara tradisional). Aman dijalankan berulang
 * (updateOrCreate per nomor_kangxi), tetapi dimaksudkan untuk tabel radikals yang masih kosong.
 *
 * Kolom `urutan` menentukan urutan tampil/belajar: radikal yang paling sering muncul sebagai
 * komponen karakter diberi urutan paling kecil (lihat frekuensi() dan urutanFrekuensi()).
 * `nomor_kangxi` tetap nomor baku Kangxi dan dipakai sebagai kunci updateOrCreate.
 *
 * Hanzi memakai karakter ideograf biasa (bukan blok "Kangxi Radicals" U+2F00),
 * supaya sama dengan karakters.hanzi dan mudah dicocokkan.
 * Kolom `keterangan` baru diisi untuk radikal yang punya petunjuk makna yang berguna;
 * sisanya null dan bisa kamu lengkapi sendiri.
 */
class RadikalSeeder extends Seeder
{
    public function run(): void
    {
        $keterangan = $this->keterangan();
        $urutan     = $this->urutanFrekuensi();

        foreach ($this->data() as [$nomor, $hanzi, $pinyin, $arti, $varian]) {
            Radikal::updateOrCreate(
                ['nomor_kangxi' => $nomor],
                [
                    'hanzi'          => $hanzi,
                    'varian'         => $varian,
                    'pinyin'         => $pinyin,
                    'arti_indonesia' => $arti,
                    'keterangan'     => $keterangan[$hanzi] ?? null,
                    'jumlah_goresan' => $this->goresan($nomor),
                    'urutan'         => $urutan[$nomor],
                ]
            );
        }
    }

    /** Jumlah goresan radikal bentuk baku menurut kelompok Kangxi (nomor 1-214). */
    private function goresan(int $nomor): int
    {
        $batas = [
            6 => 1, 29 => 2, 60 => 3, 94 => 4, 117 => 5, 146 => 6, 166 => 7, 175 => 8,
            186 => 9, 194 => 10, 200 => 11, 204 => 12, 208 => 13, 210 => 14, 211 => 15,
            213 => 16, 214 => 17,
        ];

        foreach ($batas as $sampai => $goresan) {
            if ($nomor <= $sampai) {
                return $goresan;
            }
        }

        return 17;
    }

    /**
     * Peringkat tampil per nomor Kangxi: 1 = radikal yang paling sering muncul.
     * Diurutkan dari jumlah karakter terbanyak; kalau jumlahnya sama, nomor Kangxi yang lebih kecil dulu.
     *
     * @return array<int, int> [nomor_kangxi => urutan]
     */
    private function urutanFrekuensi(): array
    {
        $frekuensi = $this->frekuensi();
        $nomor     = array_keys($frekuensi);

        usort($nomor, fn (int $a, int $b) => [$frekuensi[$b], $a] <=> [$frekuensi[$a], $b]);

        $urutan = [];
        foreach ($nomor as $indeks => $n) {
            $urutan[$n] = $indeks + 1;
        }

        return $urutan;
    }

    /**
     * Seberapa sering tiap radikal muncul: jumlah karakter yang tercatat di bawah radikal itu
     * (berdasarkan Kamus Kangxi; kolom "Frequency" pada tabel Wikipedia "Kangxi radicals",
     * jumlah seluruhnya 40.193 karakter).
     * Ubah angka di sini kalau ingin memakai sumber frekuensi lain, urutan dihitung ulang otomatis.
     *
     * @return array<int, int> [nomor_kangxi => jumlah karakter]
     */
    private function frekuensi(): array
    {
        return [
            1 => 42, 2 => 21, 3 => 10, 4 => 33, 5 => 42, 6 => 19, 7 => 29, 8 => 38, 9 => 794, 10 => 52,
            11 => 28, 12 => 44, 13 => 50, 14 => 30, 15 => 115, 16 => 38, 17 => 23, 18 => 377, 19 => 163, 20 => 64,
            21 => 19, 22 => 64, 23 => 17, 24 => 55, 25 => 45, 26 => 40, 27 => 129, 28 => 40, 29 => 91, 30 => 1146,
            31 => 118, 32 => 580, 33 => 24, 34 => 11, 35 => 23, 36 => 34, 37 => 132, 38 => 681, 39 => 83, 40 => 246,
            41 => 40, 42 => 41, 43 => 66, 44 => 148, 45 => 38, 46 => 636, 47 => 26, 48 => 17, 49 => 20, 50 => 295,
            51 => 9, 52 => 50, 53 => 15, 54 => 9, 55 => 50, 56 => 15, 57 => 165, 58 => 25, 59 => 62, 60 => 215,
            61 => 1115, 62 => 116, 63 => 44, 64 => 1203, 65 => 26, 66 => 296, 67 => 26, 68 => 32, 69 => 55, 70 => 92,
            71 => 12, 72 => 453, 73 => 37, 74 => 69, 75 => 1369, 76 => 235, 77 => 99, 78 => 231, 79 => 93, 80 => 16,
            81 => 21, 82 => 211, 83 => 10, 84 => 17, 85 => 1595, 86 => 639, 87 => 36, 88 => 10, 89 => 16, 90 => 48,
            91 => 77, 92 => 9, 93 => 233, 94 => 444, 95 => 6, 96 => 473, 97 => 55, 98 => 174, 99 => 22, 100 => 22,
            101 => 10, 102 => 192, 103 => 15, 104 => 526, 105 => 15, 106 => 109, 107 => 94, 108 => 129, 109 => 647, 110 => 65,
            111 => 64, 112 => 499, 113 => 213, 114 => 12, 115 => 431, 116 => 298, 117 => 101, 118 => 953, 119 => 318, 120 => 823,
            121 => 77, 122 => 163, 123 => 156, 124 => 220, 125 => 22, 126 => 22, 127 => 84, 128 => 172, 129 => 19, 130 => 674,
            131 => 16, 132 => 34, 133 => 24, 134 => 71, 135 => 31, 136 => 10, 137 => 197, 138 => 5, 139 => 21, 140 => 1902,
            141 => 114, 142 => 1067, 143 => 60, 144 => 53, 145 => 607, 146 => 29, 147 => 161, 148 => 158, 149 => 861, 150 => 54,
            151 => 68, 152 => 148, 153 => 140, 154 => 277, 155 => 31, 156 => 285, 157 => 580, 158 => 97, 159 => 361, 160 => 36,
            161 => 15, 162 => 381, 163 => 350, 164 => 290, 165 => 14, 166 => 14, 167 => 806, 168 => 55, 169 => 246, 170 => 348,
            171 => 12, 172 => 233, 173 => 298, 174 => 17, 175 => 25, 176 => 66, 177 => 305, 178 => 100, 179 => 20, 180 => 43,
            181 => 372, 182 => 182, 183 => 92, 184 => 403, 185 => 20, 186 => 37, 187 => 472, 188 => 185, 189 => 34, 190 => 243,
            191 => 23, 192 => 8, 193 => 73, 194 => 141, 195 => 571, 196 => 750, 197 => 44, 198 => 104, 199 => 131, 200 => 34,
            201 => 42, 202 => 46, 203 => 172, 204 => 8, 205 => 40, 206 => 14, 207 => 46, 208 => 92, 209 => 49, 210 => 18,
            211 => 162, 212 => 14, 213 => 24, 214 => 19,
        ];
    }

    /** [nomor Kangxi, hanzi, pinyin, arti Indonesia, bentuk lain (varian) atau null] */
    private function data(): array
    {
        return [
            [1, '一', 'yī', 'satu', null],
            [2, '丨', 'gǔn', 'garis tegak', null],
            [3, '丶', 'zhǔ', 'titik', null],
            [4, '丿', 'piě', 'goresan miring ke kiri', null],
            [5, '乙', 'yǐ', 'ke-2 (batang langit); bengkok', ['乚', '乛']],
            [6, '亅', 'jué', 'kait', null],
            [7, '二', 'èr', 'dua', null],
            [8, '亠', 'tóu', 'tutup', null],
            [9, '人', 'rén', 'orang', ['亻']],
            [10, '儿', 'ér', 'kaki manusia', null],
            [11, '入', 'rù', 'masuk', null],
            [12, '八', 'bā', 'delapan', null],
            [13, '冂', 'jiōng', 'wilayah; perbatasan', null],
            [14, '冖', 'mì', 'penutup', null],
            [15, '冫', 'bīng', 'es', null],
            [16, '几', 'jī', 'meja kecil', null],
            [17, '凵', 'kǎn', 'wadah terbuka', null],
            [18, '刀', 'dāo', 'pisau', ['刂']],
            [19, '力', 'lì', 'kekuatan', null],
            [20, '勹', 'bāo', 'membungkus', null],
            [21, '匕', 'bǐ', 'sendok', null],
            [22, '匚', 'fāng', 'kotak', null],
            [23, '匸', 'xì', 'menyembunyikan', null],
            [24, '十', 'shí', 'sepuluh', null],
            [25, '卜', 'bǔ', 'meramal', null],
            [26, '卩', 'jié', 'segel', ['㔾']],
            [27, '厂', 'hàn', 'tebing', null],
            [28, '厶', 'sī', 'pribadi', null],
            [29, '又', 'yòu', 'lagi; tangan kanan', null],
            [30, '口', 'kǒu', 'mulut', null],
            [31, '囗', 'wéi', 'kurungan', null],
            [32, '土', 'tǔ', 'tanah', null],
            [33, '士', 'shì', 'cendekiawan', null],
            [34, '夂', 'zhǐ', 'mengikuti dari belakang', null],
            [35, '夊', 'suī', 'berjalan pelan', null],
            [36, '夕', 'xī', 'senja; malam', null],
            [37, '大', 'dà', 'besar', null],
            [38, '女', 'nǚ', 'wanita', null],
            [39, '子', 'zǐ', 'anak', null],
            [40, '宀', 'mián', 'atap', null],
            [41, '寸', 'cùn', 'inci (ukuran)', null],
            [42, '小', 'xiǎo', 'kecil', null],
            [43, '尢', 'wāng', 'pincang', ['尣', '兀']],
            [44, '尸', 'shī', 'jenazah; tubuh', null],
            [45, '屮', 'chè', 'tunas', null],
            [46, '山', 'shān', 'gunung', null],
            [47, '巛', 'chuān', 'sungai', ['川']],
            [48, '工', 'gōng', 'pekerjaan', null],
            [49, '己', 'jǐ', 'diri sendiri', null],
            [50, '巾', 'jīn', 'kain', null],
            [51, '干', 'gān', 'perisai; kering', null],
            [52, '幺', 'yāo', 'kecil; halus', null],
            [53, '广', 'yǎn', 'bangunan beratap', null],
            [54, '廴', 'yǐn', 'melangkah panjang', null],
            [55, '廾', 'gǒng', 'dua tangan', null],
            [56, '弋', 'yì', 'menembak; anak panah berikat', null],
            [57, '弓', 'gōng', 'busur', null],
            [58, '彐', 'jì', 'kepala babi hutan', ['彑']],
            [59, '彡', 'shān', 'bulu; rambut; sinar', null],
            [60, '彳', 'chì', 'melangkah', null],
            [61, '心', 'xīn', 'hati', ['忄', '⺗']],
            [62, '戈', 'gē', 'tombak', null],
            [63, '戶', 'hù', 'pintu', ['户']],
            [64, '手', 'shǒu', 'tangan', ['扌']],
            [65, '支', 'zhī', 'cabang', null],
            [66, '攴', 'pū', 'mengetuk', ['攵']],
            [67, '文', 'wén', 'tulisan; budaya', null],
            [68, '斗', 'dǒu', 'gayung; takaran', null],
            [69, '斤', 'jīn', 'kapak; kati', null],
            [70, '方', 'fāng', 'persegi; arah', null],
            [71, '无', 'wú', 'tidak ada', ['旡']],
            [72, '日', 'rì', 'matahari; hari', null],
            [73, '曰', 'yuē', 'berkata', null],
            [74, '月', 'yuè', 'bulan', null],
            [75, '木', 'mù', 'pohon; kayu', null],
            [76, '欠', 'qiàn', 'menguap; kurang', null],
            [77, '止', 'zhǐ', 'berhenti', null],
            [78, '歹', 'dǎi', 'kematian; buruk', ['歺']],
            [79, '殳', 'shū', 'senjata; memukul', null],
            [80, '毋', 'wú', 'jangan', null],
            [81, '比', 'bǐ', 'membandingkan', null],
            [82, '毛', 'máo', 'bulu', null],
            [83, '氏', 'shì', 'marga; klan', null],
            [84, '气', 'qì', 'uap; udara', null],
            [85, '水', 'shuǐ', 'air', ['氵', '氺']],
            [86, '火', 'huǒ', 'api', ['灬']],
            [87, '爪', 'zhǎo', 'cakar', ['爫']],
            [88, '父', 'fù', 'ayah', null],
            [89, '爻', 'yáo', 'garis ramalan', null],
            [90, '爿', 'qiáng', 'papan kayu; ranjang', null],
            [91, '片', 'piàn', 'serpihan; lembaran', null],
            [92, '牙', 'yá', 'gigi taring', null],
            [93, '牛', 'niú', 'sapi', ['牜']],
            [94, '犬', 'quǎn', 'anjing', ['犭']],
            [95, '玄', 'xuán', 'gelap; misterius', null],
            [96, '玉', 'yù', 'giok', ['王']],
            [97, '瓜', 'guā', 'melon', null],
            [98, '瓦', 'wǎ', 'genteng', null],
            [99, '甘', 'gān', 'manis', null],
            [100, '生', 'shēng', 'lahir; hidup', null],
            [101, '用', 'yòng', 'memakai', null],
            [102, '田', 'tián', 'ladang', null],
            [103, '疋', 'pǐ', 'gulungan kain; kaki', null],
            [104, '疒', 'nè', 'sakit', null],
            [105, '癶', 'bō', 'membuka kaki', null],
            [106, '白', 'bái', 'putih', null],
            [107, '皮', 'pí', 'kulit', null],
            [108, '皿', 'mǐn', 'piring; wadah', null],
            [109, '目', 'mù', 'mata', null],
            [110, '矛', 'máo', 'tombak', null],
            [111, '矢', 'shǐ', 'anak panah', null],
            [112, '石', 'shí', 'batu', null],
            [113, '示', 'shì', 'memperlihatkan; altar', ['礻']],
            [114, '禸', 'róu', 'jejak kaki binatang', null],
            [115, '禾', 'hé', 'padi; biji-bijian', null],
            [116, '穴', 'xué', 'gua; lubang', null],
            [117, '立', 'lì', 'berdiri', null],
            [118, '竹', 'zhú', 'bambu', ['⺮']],
            [119, '米', 'mǐ', 'beras', null],
            [120, '糸', 'mì', 'benang sutra', ['糹']],
            [121, '缶', 'fǒu', 'guci tembikar', null],
            [122, '网', 'wǎng', 'jaring', ['罒', '⺳', '罓']],
            [123, '羊', 'yáng', 'domba; kambing', ['⺶', '⺷']],
            [124, '羽', 'yǔ', 'bulu; sayap', null],
            [125, '老', 'lǎo', 'tua', ['耂']],
            [126, '而', 'ér', 'dan; janggut', null],
            [127, '耒', 'lěi', 'bajak', null],
            [128, '耳', 'ěr', 'telinga', null],
            [129, '聿', 'yù', 'kuas tulis', null],
            [130, '肉', 'ròu', 'daging', ['⺼']],
            [131, '臣', 'chén', 'pejabat; menteri', null],
            [132, '自', 'zì', 'diri sendiri', null],
            [133, '至', 'zhì', 'tiba', null],
            [134, '臼', 'jiù', 'lesung', null],
            [135, '舌', 'shé', 'lidah', null],
            [136, '舛', 'chuǎn', 'saling bertentangan', null],
            [137, '舟', 'zhōu', 'perahu', null],
            [138, '艮', 'gèn', 'berhenti; tegar', null],
            [139, '色', 'sè', 'warna', null],
            [140, '艸', 'cǎo', 'rumput', ['艹', '⺾']],
            [141, '虍', 'hū', 'belang harimau', null],
            [142, '虫', 'chóng', 'serangga', null],
            [143, '血', 'xuè', 'darah', null],
            [144, '行', 'xíng', 'berjalan; jalan', null],
            [145, '衣', 'yī', 'pakaian', ['衤']],
            [146, '襾', 'yà', 'menutupi', ['覀', '西']],
            [147, '見', 'jiàn', 'melihat', null],
            [148, '角', 'jiǎo', 'tanduk; sudut', null],
            [149, '言', 'yán', 'ucapan', ['訁']],
            [150, '谷', 'gǔ', 'lembah', null],
            [151, '豆', 'dòu', 'kacang', null],
            [152, '豕', 'shǐ', 'babi', null],
            [153, '豸', 'zhì', 'binatang buas bertubuh panjang', null],
            [154, '貝', 'bèi', 'kerang; harta', null],
            [155, '赤', 'chì', 'merah', null],
            [156, '走', 'zǒu', 'berjalan; berlari', null],
            [157, '足', 'zú', 'kaki', ['⻊']],
            [158, '身', 'shēn', 'tubuh', null],
            [159, '車', 'chē', 'kereta; kendaraan', null],
            [160, '辛', 'xīn', 'pedih; keras', null],
            [161, '辰', 'chén', 'waktu pagi; cabang bumi ke-5', null],
            [162, '辵', 'chuò', 'berjalan', ['辶', '⻌', '⻍']],
            [163, '邑', 'yì', 'kota', ['阝']],
            [164, '酉', 'yǒu', 'arak; cabang bumi ke-10', null],
            [165, '釆', 'biàn', 'membedakan', null],
            [166, '里', 'lǐ', 'desa; jarak li', null],
            [167, '金', 'jīn', 'logam; emas', ['釒']],
            [168, '長', 'cháng', 'panjang', ['镸']],
            [169, '門', 'mén', 'pintu gerbang', null],
            [170, '阜', 'fù', 'bukit; gundukan', ['阝']],
            [171, '隶', 'dài', 'menangkap', null],
            [172, '隹', 'zhuī', 'burung berekor pendek', null],
            [173, '雨', 'yǔ', 'hujan', null],
            [174, '青', 'qīng', 'biru-hijau', ['靑']],
            [175, '非', 'fēi', 'bukan', null],
            [176, '面', 'miàn', 'wajah', null],
            [177, '革', 'gé', 'kulit samak', null],
            [178, '韋', 'wéi', 'kulit lunak', null],
            [179, '韭', 'jiǔ', 'kucai', null],
            [180, '音', 'yīn', 'bunyi', null],
            [181, '頁', 'yè', 'halaman; kepala', null],
            [182, '風', 'fēng', 'angin', null],
            [183, '飛', 'fēi', 'terbang', null],
            [184, '食', 'shí', 'makanan', ['飠']],
            [185, '首', 'shǒu', 'kepala', null],
            [186, '香', 'xiāng', 'harum', null],
            [187, '馬', 'mǎ', 'kuda', null],
            [188, '骨', 'gǔ', 'tulang', null],
            [189, '高', 'gāo', 'tinggi', null],
            [190, '髟', 'biāo', 'rambut panjang', null],
            [191, '鬥', 'dòu', 'berkelahi', null],
            [192, '鬯', 'chàng', 'arak ritual', null],
            [193, '鬲', 'lì', 'kuali berkaki tiga', null],
            [194, '鬼', 'guǐ', 'hantu', null],
            [195, '魚', 'yú', 'ikan', null],
            [196, '鳥', 'niǎo', 'burung', null],
            [197, '鹵', 'lǔ', 'garam; asin', null],
            [198, '鹿', 'lù', 'rusa', null],
            [199, '麥', 'mài', 'gandum', null],
            [200, '麻', 'má', 'rami', null],
            [201, '黃', 'huáng', 'kuning', null],
            [202, '黍', 'shǔ', 'jawawut', null],
            [203, '黑', 'hēi', 'hitam', null],
            [204, '黹', 'zhǐ', 'menyulam', null],
            [205, '黽', 'mǐn', 'katak', null],
            [206, '鼎', 'dǐng', 'kuali ritual', null],
            [207, '鼓', 'gǔ', 'gendang', null],
            [208, '鼠', 'shǔ', 'tikus', null],
            [209, '鼻', 'bí', 'hidung', null],
            [210, '齊', 'qí', 'rata; seragam', null],
            [211, '齒', 'chǐ', 'gigi', null],
            [212, '龍', 'lóng', 'naga', null],
            [213, '龜', 'guī', 'kura-kura', null],
            [214, '龠', 'yuè', 'seruling', null],
        ];
    }

    /** Petunjuk makna untuk radikal yang sering dipakai (ditampilkan saat belajar). */
    private function keterangan(): array
    {
        return [
            '人' => 'Karakter dengan 亻 biasanya berhubungan dengan manusia atau sifat manusia, mis. 你, 他, 住.',
            '刀' => 'Karakter dengan 刂 biasanya berhubungan dengan pisau atau memotong, mis. 別, 割, 利.',
            '力' => 'Karakter dengan 力 biasanya berhubungan dengan kekuatan atau usaha, mis. 動, 努, 男.',
            '口' => 'Karakter dengan 口 biasanya berhubungan dengan mulut, ucapan, atau bunyi, mis. 吃, 叫, 唱.',
            '囗' => 'Karakter dengan 囗 biasanya berhubungan dengan batas atau sesuatu yang dikelilingi, mis. 國, 圖, 園.',
            '土' => 'Karakter dengan 土 biasanya berhubungan dengan tanah atau tempat, mis. 地, 場, 城.',
            '女' => 'Karakter dengan 女 biasanya berhubungan dengan wanita atau hubungan keluarga, mis. 媽, 姐, 好.',
            '子' => 'Karakter dengan 子 biasanya berhubungan dengan anak atau belajar, mis. 孩, 學, 字.',
            '宀' => 'Karakter dengan 宀 biasanya berhubungan dengan atap atau rumah, mis. 家, 室, 安.',
            '寸' => 'Karakter dengan 寸 biasanya berhubungan dengan ukuran atau tindakan tangan, mis. 對, 寺, 射.',
            '山' => 'Karakter dengan 山 biasanya berhubungan dengan gunung atau pulau, mis. 岸, 島.',
            '巾' => 'Karakter dengan 巾 biasanya berhubungan dengan kain, mis. 帽, 帶.',
            '广' => 'Karakter dengan 广 biasanya berhubungan dengan bangunan, mis. 店, 床, 廳.',
            '彳' => 'Karakter dengan 彳 biasanya berhubungan dengan berjalan atau tindakan, mis. 往, 很, 後.',
            '心' => 'Karakter dengan 忄 biasanya berhubungan dengan hati, pikiran, atau perasaan, mis. 想, 愛, 忙.',
            '戈' => 'Karakter dengan 戈 biasanya berhubungan dengan senjata atau perang, mis. 戰, 戲.',
            '手' => 'Karakter dengan 扌 biasanya berhubungan dengan tangan atau gerakan tangan, mis. 打, 拿, 找.',
            '攴' => 'Karakter dengan 攵 biasanya berhubungan dengan memukul atau mengerjakan sesuatu, mis. 教, 收, 放.',
            '日' => 'Karakter dengan 日 biasanya berhubungan dengan matahari, siang, atau waktu, mis. 時, 明, 晚.',
            '月' => 'Karakter dengan 月 sering berhubungan dengan bulan atau waktu, mis. 期. Pada banyak karakter 月 sebenarnya bentuk 肉 (bagian tubuh), mis. 腦, 臉.',
            '木' => 'Karakter dengan 木 biasanya berhubungan dengan pohon atau kayu, mis. 樹, 桌, 校.',
            '欠' => 'Karakter dengan 欠 biasanya berhubungan dengan mulut terbuka atau kekurangan, mis. 歡, 歌.',
            '歹' => 'Karakter dengan 歹 biasanya berhubungan dengan kematian atau keburukan, mis. 死, 殘.',
            '水' => 'Karakter dengan 氵 biasanya berhubungan dengan air atau cairan, mis. 河, 海, 洗.',
            '火' => 'Karakter dengan 火 atau 灬 biasanya berhubungan dengan api atau panas, mis. 燈, 熱, 煮.',
            '牛' => 'Karakter dengan 牛 atau 牜 biasanya berhubungan dengan sapi atau hewan ternak, mis. 物, 牧.',
            '犬' => 'Karakter dengan 犭 biasanya berhubungan dengan hewan, mis. 狗, 貓.',
            '玉' => 'Karakter dengan 王 di sisi kiri biasanya berhubungan dengan giok atau batu berharga, mis. 玩, 球.',
            '田' => 'Karakter dengan 田 biasanya berhubungan dengan ladang, mis. 男, 界, 留.',
            '疒' => 'Karakter dengan 疒 biasanya berhubungan dengan penyakit, mis. 病, 痛, 疼.',
            '目' => 'Karakter dengan 目 biasanya berhubungan dengan mata atau melihat, mis. 看, 睡, 眼.',
            '石' => 'Karakter dengan 石 biasanya berhubungan dengan batu, mis. 破, 碗, 砂.',
            '示' => 'Karakter dengan 礻 biasanya berhubungan dengan ritual, doa, atau keberuntungan, mis. 祝, 禮, 福.',
            '禾' => 'Karakter dengan 禾 biasanya berhubungan dengan padi atau biji-bijian, mis. 種, 秋, 程.',
            '穴' => 'Karakter dengan 穴 biasanya berhubungan dengan lubang atau gua, mis. 空, 窗, 突.',
            '竹' => 'Karakter dengan ⺮ biasanya berhubungan dengan bambu atau alat dari bambu, mis. 筆, 笑, 第.',
            '米' => 'Karakter dengan 米 biasanya berhubungan dengan beras atau makanan, mis. 糖, 精.',
            '糸' => 'Karakter dengan 糹 biasanya berhubungan dengan benang atau kain, mis. 紅, 練, 紙.',
            '肉' => 'Karakter dengan ⺼ (mirip 月) biasanya berhubungan dengan daging atau bagian tubuh, mis. 腦, 臉, 腿.',
            '艸' => 'Karakter dengan 艹 biasanya berhubungan dengan rumput atau tumbuhan, mis. 花, 草, 茶.',
            '虫' => 'Karakter dengan 虫 biasanya berhubungan dengan serangga atau binatang kecil, mis. 蚊, 蝴.',
            '衣' => 'Karakter dengan 衤 biasanya berhubungan dengan pakaian, mis. 褲, 裙, 衫.',
            '言' => 'Karakter dengan 訁 biasanya berhubungan dengan ucapan atau bahasa, mis. 說, 話, 語.',
            '貝' => 'Karakter dengan 貝 biasanya berhubungan dengan uang atau harta, mis. 買, 賣, 貴.',
            '足' => 'Karakter dengan ⻊ biasanya berhubungan dengan kaki atau gerakan kaki, mis. 跑, 路, 跳.',
            '車' => 'Karakter dengan 車 biasanya berhubungan dengan kendaraan, mis. 輛, 輪.',
            '辵' => 'Karakter dengan 辶 biasanya berhubungan dengan berjalan atau bergerak, mis. 過, 進, 送.',
            '邑' => 'Bentuk 阝 di SISI KANAN karakter berasal dari 邑 (kota atau daerah), mis. 都, 部, 鄉.',
            '阜' => 'Bentuk 阝 di SISI KIRI karakter berasal dari 阜 (bukit atau gundukan), mis. 院, 陽, 隊.',
            '金' => 'Karakter dengan 釒 biasanya berhubungan dengan logam, mis. 銀, 鐵, 錢.',
            '門' => 'Karakter dengan 門 biasanya berhubungan dengan pintu atau gerbang, mis. 開, 問, 間.',
            '雨' => 'Karakter dengan 雨 biasanya berhubungan dengan hujan atau cuaca, mis. 雪, 雲, 電.',
            '食' => 'Karakter dengan 飠 biasanya berhubungan dengan makanan atau makan, mis. 飯, 餓, 餅.',
            '馬' => 'Karakter dengan 馬 biasanya berhubungan dengan kuda, mis. 騎, 驚.',
            '魚' => 'Karakter dengan 魚 biasanya berhubungan dengan ikan, mis. 鮮, 鯨.',
            '鳥' => 'Karakter dengan 鳥 biasanya berhubungan dengan burung, mis. 鳴, 鵝.',
            '見' => 'Karakter dengan 見 biasanya berhubungan dengan melihat atau mengamati, mis. 親, 覺, 觀.',
            '頁' => 'Karakter dengan 頁 biasanya berhubungan dengan kepala, mis. 頭, 題, 順.',
        ];
    }
}
