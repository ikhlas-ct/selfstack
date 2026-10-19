<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Profil akun yang sedang login (edit saja, tanpa CRUD).
 *   GET admin/profil          -> admin.profil           (index)
 *   PUT admin/profil          -> admin.profil.update    (update)
 *   PUT admin/profil/password -> admin.profil.password  (password)
 */
class ProfilController extends Controller
{
    public function index(Request $request): View
    {
        return view('pages.profil.index', ['user' => $request->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'nama'     => ['required', 'string', 'max:100'],
            'moto'     => ['nullable', 'string', 'max:150'],
            'username' => [
                'required', 'string', 'max:100', 'regex:/^[A-Za-z0-9._-]+$/',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'email'    => [
                'required', 'email', 'max:100',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'foto'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'hapus_foto' => ['nullable', 'boolean'],
        ], [
            'required' => ':attribute wajib diisi.',
            'max'      => ':attribute terlalu panjang (maksimal :max).',
            'unique'   => ':attribute sudah dipakai akun lain.',
            'email'    => 'Format email belum benar.',
            'regex'    => 'Username hanya boleh huruf, angka, titik, garis bawah, dan strip (tanpa spasi).',
            'image'    => ':attribute harus berupa gambar.',
            'mimes'    => ':attribute harus berformat :values.',
            'uploaded' => ':attribute gagal diunggah. Cek ukuran file-nya.',
        ], [
            'nama' => 'Nama', 'moto' => 'Moto', 'username' => 'Username',
            'email' => 'Email', 'foto' => 'Foto',
        ]);

        // Foto: ganti / hapus / biarkan.
        $fotoLama = null;
        if ($request->hasFile('foto')) {
            $fotoLama     = $user->foto;
            $data['foto'] = $request->file('foto')->store('foto-user', 'public');
        } elseif ($request->boolean('hapus_foto')) {
            $fotoLama     = $user->foto;
            $data['foto'] = null;
        } else {
            unset($data['foto']);
        }
        unset($data['hapus_foto']);

        $user->update($data);

        // File lama baru dihapus setelah data berhasil tersimpan.
        if (filled($fotoLama)) {
            Storage::disk('public')->delete(ltrim($fotoLama, '/'));
        }

        return redirect()->route('admin.profil')->with('success', 'Profil berhasil disimpan.');
    }

    public function password(Request $request): RedirectResponse
    {
        // Error password dipisah ("bag" sendiri) supaya tidak tercampur dengan error form profil.
        $request->validateWithBag('gantiPassword', [
            'password_lama' => ['required', 'current_password'],
            'password'      => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'required'         => ':attribute wajib diisi.',
            'current_password' => 'Password lama salah.',
            'min'              => ':attribute baru minimal :min karakter.',
            'confirmed'        => 'Konfirmasi password tidak sama.',
        ], [
            'password_lama' => 'Password lama',
            'password'      => 'Password',
        ]);

        $request->user()->update(['password' => $request->input('password')]); // di-hash oleh cast 'hashed'

        return redirect()->route('admin.profil')->with('success', 'Password berhasil diganti.');
    }
}
