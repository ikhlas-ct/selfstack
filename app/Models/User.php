<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

/**
 * Akun pengguna aplikasi. Login memakai username.
 *
 * Kolom `nama` = nama tampil, `moto` = kalimat singkat milik pengguna.
 * Accessor `name` memakai `nama`, dan kalau kosong jatuh ke `username`,
 * jadi {{ auth()->user()->name }} tetap jalan untuk akun lama yang belum punya nama.
 */
class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'username',
        'nama',
        'email',
        'moto',
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

    /** Nama tampil: kolom `nama`, atau `username` kalau nama belum diisi. */
    public function getNameAttribute(): string
    {
        return filled($this->nama) ? $this->nama : $this->username;
    }

    /** Huruf pertama nama, untuk avatar kalau belum ada foto. */
    public function getInisialAttribute(): string
    {
        return Str::upper(Str::substr($this->name, 0, 1));
    }

    /** URL foto profil (disk public), atau null kalau belum ada foto. */
    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? asset('storage/' . ltrim($this->foto, '/')) : null;
    }
}
