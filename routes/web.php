<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\KosakataController;
use App\Http\Controllers\Admin\ProfilController;
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
    });
});
