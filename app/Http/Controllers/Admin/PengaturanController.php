<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WebsiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Pengaturan website (hanya edit, karena datanya cuma 1 baris).
 *   GET  admin/pengaturan -> admin.pengaturan.edit
 *   PUT  admin/pengaturan -> admin.pengaturan.update
 */
class PengaturanController extends Controller
{
    private const SOSMED = ['social_facebook', 'social_instagram', 'social_twitter', 'social_youtube'];

    /** kolom gambar => nama field checkbox "hapus" */
    private const GAMBAR = ['logo' => 'hapus_logo', 'gambar_pengantar' => 'hapus_gambar_pengantar'];

    public function edit(): View
    {
        return view('pages.setting.edit', [
            'setting' => WebsiteSetting::ambil(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $setting = WebsiteSetting::ambil();

        // Link sosmed yang diketik tanpa "https://" dilengkapi otomatis.
        $request->merge(collect(self::SOSMED)->mapWithKeys(function ($kolom) use ($request) {
            $nilai = trim((string) $request->input($kolom));
            if ($nilai !== '' && ! preg_match('#^https?://#i', $nilai)) {
                $nilai = 'https://' . $nilai;
            }
            return [$kolom => $nilai === '' ? null : $nilai];
        })->all());

        $data = $request->validate([
            'nama'              => ['required', 'string', 'max:100'],
            'slogan'            => ['nullable', 'string', 'max:255'],
            'alamat'            => ['nullable', 'string', 'max:500'],
            'email'             => ['nullable', 'email', 'max:150'],
            'nomor_telepon'     => ['nullable', 'string', 'max:30'],

            'social_facebook'   => ['nullable', 'url', 'max:255'],
            'social_instagram'  => ['nullable', 'url', 'max:255'],
            'social_twitter'    => ['nullable', 'url', 'max:255'],
            'social_youtube'    => ['nullable', 'url', 'max:255'],

            'title_pengantar'     => ['nullable', 'string', 'max:150'],
            'paragraf_pengantar'  => ['nullable', 'string', 'max:5000'],
            'about_us'            => ['nullable', 'string', 'max:10000'],

            'logo'                => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'gambar_pengantar'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'hapus_logo'              => ['nullable', 'boolean'],
            'hapus_gambar_pengantar'  => ['nullable', 'boolean'],

            'kartu_baru_per_hari'    => ['required', 'integer', 'between:0,500'],
            'karakter_baru_per_hari' => ['required', 'integer', 'between:0,200'],
            'radikal_baru_per_hari'  => ['required', 'integer', 'between:0,100'],
            'batas_review_per_hari'  => ['required', 'integer', 'between:1,2000'],
            'jam_ganti_hari'         => ['required', 'integer', 'between:0,23'],
        ], [
            'required' => ':attribute wajib diisi.',
            'max'      => ':attribute terlalu panjang (maksimal :max).',
            'between'  => ':attribute harus di antara :min dan :max.',
            'integer'  => ':attribute harus berupa angka bulat.',
            'email'    => 'Format email belum benar.',
            'url'      => 'Link :attribute belum benar.',
            'image'    => ':attribute harus berupa gambar.',
            'mimes'    => ':attribute harus berformat :values.',
            'uploaded' => ':attribute gagal diunggah. Cek ukuran file-nya.',
        ], [
            'nama'                   => 'Nama website',
            'slogan'                 => 'Slogan',
            'alamat'                 => 'Alamat',
            'nomor_telepon'          => 'Nomor telepon',
            'social_facebook'        => 'Facebook',
            'social_instagram'       => 'Instagram',
            'social_twitter'         => 'Twitter / X',
            'social_youtube'         => 'YouTube',
            'title_pengantar'        => 'Judul pengantar',
            'paragraf_pengantar'     => 'Paragraf pengantar',
            'about_us'               => 'Tentang kami',
            'logo'                   => 'Logo',
            'gambar_pengantar'       => 'Gambar pengantar',
            'kartu_baru_per_hari'    => 'Kartu baru per hari',
            'karakter_baru_per_hari' => 'Karakter baru per hari',
            'radikal_baru_per_hari'  => 'Radikal baru per hari',
            'batas_review_per_hari'  => 'Batas review per hari',
            'jam_ganti_hari'         => 'Jam ganti hari',
        ]);

        // ----- gambar: ganti / hapus / biarkan -----
        $fileLama = [];
        foreach (self::GAMBAR as $kolom => $fieldHapus) {
            if ($request->hasFile($kolom)) {
                $fileLama[] = $setting->{$kolom};
                $data[$kolom] = $request->file($kolom)->store('website', 'public');
            } elseif ($request->boolean($fieldHapus)) {
                $fileLama[] = $setting->{$kolom};
                $data[$kolom] = null;
            } else {
                unset($data[$kolom]);
            }
            unset($data[$fieldHapus]);
        }

        $setting->update($data);

        // File lama baru dihapus setelah data berhasil tersimpan.
        foreach ($fileLama as $path) {
            $this->hapusFile($path);
        }

        return redirect()
            ->route('admin.pengaturan.edit')
            ->with('success', 'Pengaturan website berhasil disimpan.')
            ->with('tab', $request->input('_tab'));
    }

    private function hapusFile(?string $path): void
    {
        if (blank($path) || Str::startsWith($path, ['http://', 'https://'])) {
            return;
        }

        $path = ltrim($path, '/');
        Storage::disk('public')->delete(Str::startsWith($path, 'storage/') ? Str::after($path, 'storage/') : $path);
    }
}
