<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * Profil akun yang sedang login (tabel users: username, email, foto, password).
 * R : index    (tampil profil)
 * U : update   (ubah username, email, foto)
 * + : password (ganti password akun)
 */
class ProfilController extends Controller
{
    public function index()
    {
        return view('pages.admin.profil', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($user->id)],
            'email'    => ['required', 'email', 'max:100', Rule::unique('users', 'email')->ignore($user->id)],
            'foto'     => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'username.required' => 'Username wajib diisi.',
            'username.unique'   => 'Username sudah dipakai.',
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'email.unique'      => 'Email sudah dipakai.',
            'foto.image'        => 'File harus berupa gambar.',
            'foto.max'          => 'Ukuran foto maksimal 2 MB.',
        ]);

        // Foto dikelola terpisah agar file lama ikut terhapus saat diganti.
        unset($data['foto']);

        if ($request->hasFile('foto')) {
            if ($user->foto && ! str_starts_with($user->foto, 'http')) {
                Storage::disk('public')->delete($user->foto);
            }
            $data['foto'] = $request->file('foto')->store('profil', 'public');
        }

        $user->update($data);

        return redirect()->route('admin.profil')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    public function password(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()],
        ], [
            'current_password.required'         => 'Password saat ini wajib diisi.',
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'password.required'                 => 'Password baru wajib diisi.',
            'password.confirmed'                => 'Konfirmasi password tidak cocok.',
        ]);

        // Cast 'hashed' di model User otomatis meng-hash password.
        Auth::user()->update(['password' => $request->password]);

        return redirect()->route('admin.profil')
            ->with('success', 'Password berhasil diubah.')
            ->with('tab', 'password');
    }
}
