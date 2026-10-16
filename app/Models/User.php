<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Akun pengguna aplikasi. Login memakai username.
 *
 * Catatan: tabel users tidak punya kolom `name`, jadi accessor `name`
 * memetakannya ke `username` agar {{ auth()->user()->name }} tetap jalan.
 */
class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'username',
        'email',
        'password',
        'foto',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed', // otomatis di-hash saat diisi (Laravel 10+)
    ];

    /**
     * Aplikasi ini dipakai satu orang saja, jadi setiap akun yang login
     * otomatis admin. Kalau nanti ada banyak pengguna, ganti dengan
     * pengecekan kolom role.
     */
    public function isAdmin(): bool
    {
        return true;
    }

    /** Alias nama tampil = username. */
    public function getNameAttribute(): string
    {
        return $this->username;
    }

    /** URL foto profil (disk public), atau null kalau belum ada foto. */
    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? asset('storage/' . ltrim($this->foto, '/')) : null;
    }
}
