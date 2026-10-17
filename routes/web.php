<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FlashcardController;
use App\Http\Controllers\Admin\GrupSoalController;
use App\Http\Controllers\Admin\HafalanController;
use App\Http\Controllers\Admin\KerjakanSoalController;
use App\Http\Controllers\Admin\KosakataController;
use App\Http\Controllers\Admin\ProfilController;
use App\Http\Controllers\Admin\SoalController;
use App\Http\Controllers\Login\AuthController;
use Illuminate\Support\Facades\Route;

// Hanya untuk yang belum login
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'login_post'])->name('login.post');
});

// Logout harus POST dan sudah login
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Halaman admin (harus login, tanpa middleware 'admin')
Route::middleware('auth')->group(function () {
    Route::get('/', fn () => redirect()->route('admin.dashboard'));
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // Profil akun yang sedang login
    Route::get('/admin/profil', [ProfilController::class, 'index'])->name('admin.profil');
    Route::put('/admin/profil', [ProfilController::class, 'update'])->name('admin.profil.update');
    Route::put('/admin/profil/password', [ProfilController::class, 'password'])->name('admin.profil.password');

    // Kosakata -> URL /admin/kosakata..., nama route admin.kosakata.*
    Route::prefix('admin')->name('admin.')->group(function () {
        // Import & template HARUS di atas resource, kalau tidak "/admin/kosakata/import"
        // dianggap sebagai {kosakata}.
        Route::get('kosakata/import', [KosakataController::class, 'import'])->name('kosakata.import');
        Route::post('kosakata/import', [KosakataController::class, 'importStore'])->name('kosakata.import.store');
        Route::get('kosakata/template', [KosakataController::class, 'template'])->name('kosakata.template');

        Route::resource('kosakata', KosakataController::class)
            ->parameters(['kosakata' => 'kosakata']);

        // Progres hafalan kosakata -> admin.hafalan.index / riwayat / status
        Route::get('hafalan', [HafalanController::class, 'index'])->name('hafalan.index');
        Route::get('hafalan/{kosakata}/riwayat', [HafalanController::class, 'riwayat'])->name('hafalan.riwayat');
        // Tombol "Pindahkan ke" di popup (dipanggil lewat route('admin.hafalan.status') di HafalanController::riwayat)
        Route::post('hafalan/{kosakata}/status', [HafalanController::class, 'pindahStatus'])->name('hafalan.status');

        // Flashcard hafalan kosakata -> admin.flashcard.index / mulai / jawab
        Route::get('flashcard', [FlashcardController::class, 'index'])->name('flashcard.index');
        Route::get('flashcard/mulai', [FlashcardController::class, 'mulai'])->name('flashcard.mulai');
        Route::post('flashcard/jawab', [FlashcardController::class, 'jawab'])->name('flashcard.jawab');

        // Paket soal (GrupSoal) -> admin.grup-soal.index / create / store / show / edit / update / destroy / aktif
        Route::patch('grup-soal/{grupSoal}/aktif', [GrupSoalController::class, 'toggleAktif'])->name('grup-soal.aktif');
        Route::resource('grup-soal', GrupSoalController::class)
            ->parameters(['grup-soal' => 'grupSoal']);

        // Soal pilihan ganda di dalam paket. Shallow: create/store butuh paket, edit/update/destroy cukup id soal.
        //   admin.grup-soal.soal.create / store, admin.soal.edit / update / destroy
        // Daftar & detail soal tampil di halaman detail paket, jadi index/show tidak dipakai.
        Route::patch('soal/{soal}/status', [SoalController::class, 'ubahStatus'])->name('soal.status');
        Route::resource('grup-soal.soal', SoalController::class)
            ->shallow()
            ->except(['index', 'show'])
            ->parameters(['grup-soal' => 'grupSoal', 'soal' => 'soal']);

        // Mengerjakan soal dari paket (GrupSoal) -> admin.kerjakan.*
        //   index / detail / mulai          : daftar paket, halaman paket, mulai percobaan
        //   soal / jawab / selesai          : satu soal per halaman, simpan jawaban, akhiri percobaan
        //   hasil / lapor                   : skor + pembahasan, laporkan soal bermasalah
        Route::prefix('kerjakan')->name('kerjakan.')->group(function () {
            Route::get('/', [KerjakanSoalController::class, 'index'])->name('index');
            Route::get('paket/{grupSoal}', [KerjakanSoalController::class, 'detail'])->name('detail');
            Route::post('paket/{grupSoal}/mulai', [KerjakanSoalController::class, 'mulai'])->name('mulai');

            Route::get('percobaan/{hasilUjian}', [KerjakanSoalController::class, 'kerjakan'])->name('soal');
            Route::post('percobaan/{hasilUjian}/jawab', [KerjakanSoalController::class, 'jawab'])->name('jawab');
            Route::post('percobaan/{hasilUjian}/selesai', [KerjakanSoalController::class, 'selesai'])->name('selesai');
            Route::get('percobaan/{hasilUjian}/hasil', [KerjakanSoalController::class, 'hasil'])->name('hasil');
            Route::post('percobaan/{hasilUjian}/soal/{soal}/lapor', [KerjakanSoalController::class, 'lapor'])->name('lapor');
        });
    });
});
