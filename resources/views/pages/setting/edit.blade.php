@extends('layouts.user.user')

@section('title', 'Pengaturan Website')

@section('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    body, .card, .btn, h4, h5 { font-family: 'Plus Jakarta Sans', sans-serif; }

    [hidden] { display: none !important; }

    /* ---------- header ---------- */
    .ph-card { background: #fff; border: 1px solid #e9ecef; border-radius: 14px; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; margin-bottom: 1.25rem; position: relative; overflow: hidden; box-shadow: 0 1px 6px rgba(0,0,0,.05); }
    .ph-card::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; border-radius: 14px 0 0 14px; background: #0369a1; }
    .ph-left { display: flex; align-items: center; gap: 12px; }
    .ph-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; background: #e0f2fe; color: #0369a1; }
    .ph-title { font-size: 1.05rem; font-weight: 700; color: #1e293b; letter-spacing: -.2px; line-height: 1.2; margin: 0; }
    .ph-breadcrumb { display: flex; align-items: center; gap: 4px; flex-wrap: wrap; margin-top: 4px; list-style: none; padding: 0; margin-bottom: 0; }
    .ph-breadcrumb li { display: flex; align-items: center; }
    .ph-breadcrumb li + li::before { content: '›'; color: #cbd5e1; font-size: .7rem; margin: 0 4px; }
    .ph-breadcrumb a { font-size: .75rem; color: #1a73e8; text-decoration: none; }
    .ph-breadcrumb a:hover { text-decoration: underline; }
    .ph-breadcrumb .bc-active { font-size: .75rem; color: #94a3b8; }

    /* ---------- notifikasi ---------- */
    .notif { display: flex; align-items: center; gap: 10px; border-radius: 12px; padding: 12px 16px; font-size: .88rem; font-weight: 500; margin-bottom: 16px; }
    .notif-sukses { background: #dcfce7; color: #15803d; }
    .notif-error  { background: #fee2e2; color: #b91c1c; }

    /* ---------- tab (gaya pil) ---------- */
    .pil-status { display: flex; flex-wrap: wrap; gap: 12px; margin: 8px 0 18px; }
    .pil { position: relative; border: 1px solid #e2e8f0; background: #fff; color: #334155; border-radius: 999px; padding: 10px 24px; font-size: .88rem; font-weight: 600; cursor: pointer; transition: border-color .15s, background .15s, color .15s; }
    .pil:hover { border-color: #93c5fd; }
    .pil.aktif { background: #eaf2ff; border-color: #1a73e8; color: #1a73e8; }
    .pil i { margin-right: 8px; }
    .pil .titik-error { display: none; position: absolute; top: 4px; right: 8px; width: 9px; height: 9px; border-radius: 50%; background: #dc2626; }
    .pil.ada-error .titik-error { display: block; }

    .panel-tab { display: none; }
    .panel-tab.aktif { display: block; }

    /* ---------- kartu form ---------- */
    .kartu-form { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px 26px; margin-bottom: 18px; }
    .kartu-form .judul { font-weight: 700; font-size: .98rem; color: #1e293b; margin: 0; }
    .kartu-form .sub { font-size: .82rem; color: #94a3b8; margin: 2px 0 20px; }

    .form-label { font-size: .82rem; font-weight: 600; color: #334155; margin-bottom: 6px; }
    .form-label .wajib { color: #dc2626; margin-left: 2px; }
    .kartu-form .form-control, .kartu-form .form-select { border-radius: 10px; font-size: .88rem; border-color: #e2e8f0; min-height: 44px; }
    .kartu-form textarea.form-control { padding-top: 12px; line-height: 1.6; }
    .kartu-form .form-control:focus, .kartu-form .form-select:focus { border-color: #1a73e8; box-shadow: 0 0 0 3px rgba(26,115,232,.12); }
    .kartu-form .is-invalid { border-color: #dc2626 !important; }
    .pesan-error { color: #dc2626; font-size: .78rem; margin-top: 5px; }
    .bantuan { color: #94a3b8; font-size: .78rem; margin-top: 5px; }
    .hitung-huruf { text-align: right; color: #94a3b8; font-size: .74rem; margin-top: 4px; }

    .input-ikon { position: relative; }
    .input-ikon > i { position: absolute; left: 14px; top: 22px; transform: translateY(-50%); color: #94a3b8; font-size: .9rem; width: 18px; text-align: center; }
    .input-ikon .form-control { padding-left: 42px; }

    /* ---------- unggah gambar ---------- */
    .unggah { display: flex; align-items: center; gap: 18px; flex-wrap: wrap; }
    .unggah-preview { width: 112px; height: 112px; border-radius: 16px; border: 1px dashed #cbd5e1; background: #f8fafc; display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; color: #94a3b8; font-size: 1.6rem; }
    .unggah-preview.lebar { width: 200px; height: 112px; }
    .unggah-preview img { width: 100%; height: 100%; object-fit: contain; background: #fff; }
    .unggah-preview.lebar img { object-fit: cover; }
    .unggah-aksi { display: flex; flex-wrap: wrap; gap: 10px; }
    .btn-aksi { border: 1px solid #e2e8f0; background: #fff; color: #334155; border-radius: 10px; padding: 9px 18px; font-weight: 600; font-size: .85rem; cursor: pointer; }
    .btn-aksi:hover { background: #f1f5f9; }
    .btn-aksi.bahaya { color: #b91c1c; }
    .btn-aksi.bahaya:hover { background: #fee2e2; border-color: #fecaca; }

    /* ---------- target belajar ---------- */
    .target-item { display: flex; align-items: center; justify-content: space-between; gap: 20px; flex-wrap: wrap; padding: 20px 0; border-bottom: 1px solid #f1f5f9; }
    .target-item:first-of-type { padding-top: 4px; }
    .target-item:last-child { border-bottom: none; padding-bottom: 0; }
    .target-info { display: flex; align-items: flex-start; gap: 14px; flex: 1 1 280px; min-width: 0; }
    .target-ikon { width: 40px; height: 40px; border-radius: 10px; background: #e0f2fe; color: #0369a1; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .target-nama { font-weight: 700; font-size: .92rem; color: #1e293b; }
    .target-ket { font-size: .8rem; color: #94a3b8; margin-top: 2px; line-height: 1.5; }
    .target-kontrol { display: flex; flex-direction: column; align-items: flex-end; gap: 10px; }

    .stepper { display: flex; align-items: center; gap: 8px; }
    .stepper .stp { width: 44px; height: 44px; border: 1px solid #e2e8f0; background: #fff; border-radius: 10px; color: #334155; font-size: 1.1rem; font-weight: 700; line-height: 1; cursor: pointer; }
    .stepper .stp:hover { border-color: #93c5fd; background: #eaf2ff; color: #1a73e8; }
    .stepper .stp:disabled { opacity: .4; cursor: default; background: #fff; border-color: #e2e8f0; color: #334155; }
    .stepper input { width: 92px; text-align: center; font-weight: 800; font-size: 1.1rem; color: #1e293b; -moz-appearance: textfield; }
    .stepper input::-webkit-outer-spin-button, .stepper input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }

    .preset { display: flex; flex-wrap: wrap; gap: 6px; justify-content: flex-end; }
    .preset button { border: 1px solid #e2e8f0; background: #fff; color: #64748b; border-radius: 999px; padding: 3px 12px; font-size: .76rem; font-weight: 600; cursor: pointer; }
    .preset button:hover { border-color: #93c5fd; }
    .preset button.aktif { background: #eaf2ff; border-color: #1a73e8; color: #1a73e8; }

    .target-kontrol .form-select { width: 150px; font-weight: 700; }

    /* ---------- bar simpan ---------- */
    .bar-simpan { position: sticky; bottom: 14px; z-index: 20; background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 12px 18px; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; box-shadow: 0 6px 22px rgba(0,0,0,.09); margin-top: 6px; }
    .status-simpan { font-size: .84rem; color: #94a3b8; display: flex; align-items: center; gap: 8px; }
    .status-simpan.berubah { color: #b45309; font-weight: 600; }
    .btn-simpan { background: #1565c0; border: 1px solid #1565c0; color: #fff; border-radius: 12px; padding: 11px 26px; font-weight: 700; font-size: .92rem; cursor: pointer; }
    .btn-simpan:hover { background: #0f4f9a; }
    .btn-simpan:disabled { opacity: .6; cursor: default; }
    .btn-batal { border: 1px solid #e2e8f0; background: #fff; color: #64748b; border-radius: 12px; padding: 11px 20px; font-weight: 600; font-size: .88rem; cursor: pointer; }
    .btn-batal:hover { background: #f1f5f9; color: #334155; }

    @media (max-width: 576px) {
        .kartu-form { padding: 20px 16px; }
        .pil { padding: 8px 16px; }
        .target-kontrol { align-items: flex-start; width: 100%; }
        .preset { justify-content: flex-start; }
        .unggah-preview.lebar { width: 100%; }
        .bar-simpan > div:last-child { width: 100%; display: flex; gap: 10px; }
        .bar-simpan > div:last-child button { flex: 1; }
    }
</style>
@endsection

@section('content')
    @php
        // Field apa saja yang ada di tiap tab (dipakai untuk menandai tab yang berisi error)
        $fieldTab = [
            'umum'    => ['nama', 'slogan', 'logo', 'hapus_logo'],
            'kontak'  => ['alamat', 'email', 'nomor_telepon', 'social_facebook', 'social_instagram', 'social_twitter', 'social_youtube'],
            'beranda' => ['title_pengantar', 'paragraf_pengantar', 'gambar_pengantar', 'hapus_gambar_pengantar', 'about_us'],
            'target'  => ['kartu_baru_per_hari', 'karakter_baru_per_hari', 'radikal_baru_per_hari', 'batas_review_per_hari', 'jam_ganti_hari'],
        ];
        $tabError  = collect($fieldTab)->map(fn ($f) => $errors->hasAny($f))->all();
        $tabAktif  = collect($tabError)->filter()->keys()->first() ?? old('_tab', session('tab', 'umum'));
        $tabAktif  = array_key_exists($tabAktif, $fieldTab) ? $tabAktif : 'umum';

        $punyaLogo   = filled($setting->logo);
        $punyaGambar = filled($setting->gambar_pengantar);

        // Kolom sosial media: field => [label, ikon, contoh isi]
        $sosmed = [
            'social_facebook'  => ['Facebook', 'fab fa-facebook-f', 'facebook.com/namaakun'],
            'social_instagram' => ['Instagram', 'fab fa-instagram', 'instagram.com/namaakun'],
            'social_twitter'   => ['Twitter / X', 'fab fa-twitter', 'x.com/namaakun'],
            'social_youtube'   => ['YouTube', 'fab fa-youtube', 'youtube.com/@namaakun'],
        ];

        // Isi kartu target belajar: field => [judul, keterangan, ikon, min, max, pilihan cepat]
        $targets = [
            'kartu_baru_per_hari'    => ['Kartu kosakata baru per hari', 'Jumlah kata baru yang masuk ke flashcard tiap hari.', 'fa-language', 0, 500, [5, 10, 20, 30]],
            'karakter_baru_per_hari' => ['Karakter baru per hari', 'Jumlah karakter hanzi baru yang dipelajari tiap hari.', 'fa-pen-fancy', 0, 200, [3, 5, 10, 15]],
            'radikal_baru_per_hari'  => ['Radikal baru per hari', 'Jumlah radikal baru yang dipelajari tiap hari.', 'fa-cubes', 0, 100, [1, 2, 3, 5]],
            'batas_review_per_hari'  => ['Batas review per hari', 'Jumlah maksimal kartu ulangan dalam sehari.', 'fa-redo', 1, 2000, [50, 100, 200, 300]],
        ];
    @endphp

    <div class="container">

        <div class="ph-card">
            <div class="ph-left">
                <div class="ph-icon"><i class="fas fa-cog"></i></div>
                <div>
                    <h5 class="ph-title">Pengaturan Website</h5>
                    <ol class="ph-breadcrumb" aria-label="breadcrumb">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><span class="bc-active">Pengaturan Website</span></li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="page-inner">

            @if (session('success'))
                <div class="notif notif-sukses"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="notif notif-error"><i class="fas fa-exclamation-circle"></i> Ada isian yang belum benar. Cek tab yang bertanda merah.</div>
            @endif

            {{-- Tab --}}
            <div class="pil-status" id="pilTab" role="tablist">
                <button type="button" class="pil {{ $tabAktif === 'umum' ? 'aktif' : '' }} {{ $tabError['umum'] ? 'ada-error' : '' }}" data-tab="umum"><i class="fas fa-id-card"></i>Umum<span class="titik-error"></span></button>
                <button type="button" class="pil {{ $tabAktif === 'kontak' ? 'aktif' : '' }} {{ $tabError['kontak'] ? 'ada-error' : '' }}" data-tab="kontak"><i class="fas fa-address-book"></i>Kontak &amp; Sosial Media<span class="titik-error"></span></button>
                <button type="button" class="pil {{ $tabAktif === 'beranda' ? 'aktif' : '' }} {{ $tabError['beranda'] ? 'ada-error' : '' }}" data-tab="beranda"><i class="fas fa-home"></i>Halaman Depan<span class="titik-error"></span></button>
                <button type="button" class="pil {{ $tabAktif === 'target' ? 'aktif' : '' }} {{ $tabError['target'] ? 'ada-error' : '' }}" data-tab="target"><i class="fas fa-bullseye"></i>Target Belajar<span class="titik-error"></span></button>
            </div>

            <form method="POST" action="{{ route('admin.pengaturan.update') }}" enctype="multipart/form-data" id="formPengaturan" novalidate>
                @csrf
                @method('PUT')
                <input type="hidden" name="_tab" id="inputTab" value="{{ $tabAktif }}">

                {{-- ================= UMUM ================= --}}
                <div class="panel-tab {{ $tabAktif === 'umum' ? 'aktif' : '' }}" data-panel="umum">
                    <div class="kartu-form">
                        <h6 class="judul">Identitas website</h6>
                        <p class="sub">Nama dan logo tampil di sidebar dan halaman login.</p>

                        <div class="row g-3">
                            <div class="col-lg-6">
                                <label class="form-label" for="nama">Nama website<span class="wajib">*</span></label>
                                <input type="text" id="nama" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $setting->nama) }}" maxlength="100" placeholder="Contoh: Chogwen">
                                @error('nama') <div class="pesan-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label" for="slogan">Slogan</label>
                                <input type="text" id="slogan" name="slogan" class="form-control @error('slogan') is-invalid @enderror" value="{{ old('slogan', $setting->slogan) }}" maxlength="255" placeholder="Kalimat singkat tentang website">
                                @error('slogan') <div class="pesan-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="form-label">Logo</label>
                            <div class="unggah" data-unggah data-ada="{{ $punyaLogo ? 1 : 0 }}" data-asli="{{ $setting->logo_url }}" data-bawaan="{{ asset('default-image/default-logo.png') }}">
                                <div class="unggah-preview">
                                    <img src="{{ $setting->logo_url }}" alt="Logo" onerror="this.onerror=null;this.src='{{ asset('default-image/default-logo.png') }}';">
                                    <i class="fas fa-image" hidden></i>
                                </div>
                                <div>
                                    <div class="unggah-aksi">
                                        <label class="btn-aksi mb-0" for="logo"><i class="fas fa-upload me-2"></i>Pilih logo</label>
                                        <button type="button" class="btn-aksi bahaya" data-hapus {{ $punyaLogo ? '' : 'hidden' }}><i class="fas fa-trash me-2"></i>Hapus</button>
                                    </div>
                                    <div class="bantuan">JPG, PNG, WEBP, atau SVG. Maksimal 2 MB. Paling bagus berbentuk persegi.</div>
                                    <input type="file" id="logo" name="logo" accept="image/png,image/jpeg,image/webp,image/svg+xml" hidden data-maks="2">
                                    <input type="hidden" name="hapus_logo" value="0" data-input-hapus>
                                    @error('logo') <div class="pesan-error">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ================= KONTAK & SOSMED ================= --}}
                <div class="panel-tab {{ $tabAktif === 'kontak' ? 'aktif' : '' }}" data-panel="kontak">
                    <div class="kartu-form">
                        <h6 class="judul">Kontak</h6>
                        <p class="sub">Ditampilkan sebagai cara orang menghubungimu.</p>

                        <div class="row g-3">
                            <div class="col-lg-6">
                                <label class="form-label" for="email">Email</label>
                                <div class="input-ikon">
                                    <i class="fas fa-envelope"></i>
                                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $setting->email) }}" maxlength="150" placeholder="halo@contoh.com">
                                </div>
                                @error('email') <div class="pesan-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-lg-6">
                                <label class="form-label" for="nomor_telepon">Nomor telepon</label>
                                <div class="input-ikon">
                                    <i class="fas fa-phone"></i>
                                    <input type="text" id="nomor_telepon" name="nomor_telepon" class="form-control @error('nomor_telepon') is-invalid @enderror" value="{{ old('nomor_telepon', $setting->nomor_telepon) }}" maxlength="30" placeholder="+62 812-0000-0000">
                                </div>
                                @error('nomor_telepon') <div class="pesan-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="alamat">Alamat</label>
                                <textarea id="alamat" name="alamat" rows="3" class="form-control @error('alamat') is-invalid @enderror" maxlength="500" placeholder="Alamat lengkap">{{ old('alamat', $setting->alamat) }}</textarea>
                                @error('alamat') <div class="pesan-error">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="kartu-form">
                        <h6 class="judul">Sosial media</h6>
                        <p class="sub">Isi link akunnya saja. Kalau lupa mengetik https://, otomatis ditambahkan. Kosongkan kalau tidak dipakai.</p>

                        <div class="row g-3">
                            @foreach ($sosmed as $kolom => [$label, $ikon, $contoh])
                                <div class="col-lg-6">
                                    <label class="form-label" for="{{ $kolom }}">{{ $label }}</label>
                                    <div class="input-ikon">
                                        <i class="{{ $ikon }}"></i>
                                        <input type="text" id="{{ $kolom }}" name="{{ $kolom }}" class="form-control @error($kolom) is-invalid @enderror" value="{{ old($kolom, $setting->{$kolom}) }}" maxlength="255" placeholder="{{ $contoh }}">
                                    </div>
                                    @error($kolom) <div class="pesan-error">{{ $message }}</div> @enderror
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- ================= HALAMAN DEPAN ================= --}}
                <div class="panel-tab {{ $tabAktif === 'beranda' ? 'aktif' : '' }}" data-panel="beranda">
                    <div class="kartu-form">
                        <h6 class="judul">Pengantar</h6>
                        <p class="sub">Sambutan di halaman depan website.</p>

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" for="title_pengantar">Judul pengantar</label>
                                <input type="text" id="title_pengantar" name="title_pengantar" class="form-control @error('title_pengantar') is-invalid @enderror" value="{{ old('title_pengantar', $setting->title_pengantar) }}" maxlength="150" placeholder="Contoh: Selamat datang di Chogwen">
                                @error('title_pengantar') <div class="pesan-error">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="paragraf_pengantar">Paragraf pengantar</label>
                                <textarea id="paragraf_pengantar" name="paragraf_pengantar" rows="6" class="form-control @error('paragraf_pengantar') is-invalid @enderror" maxlength="5000" data-hitung>{{ old('paragraf_pengantar', $setting->paragraf_pengantar) }}</textarea>
                                <div class="hitung-huruf"><span data-angka>0</span> / 5000</div>
                                <div class="bantuan">Tekan Enter dua kali untuk memisahkan paragraf.</div>
                                @error('paragraf_pengantar') <div class="pesan-error">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="form-label">Gambar pengantar</label>
                            <div class="unggah" data-unggah data-ada="{{ $punyaGambar ? 1 : 0 }}" data-asli="{{ $setting->gambar_pengantar_url }}" data-bawaan="">
                                <div class="unggah-preview lebar">
                                    <img src="{{ $setting->gambar_pengantar_url }}" alt="Gambar pengantar" {{ $punyaGambar ? '' : 'hidden' }}>
                                    <i class="fas fa-image" {{ $punyaGambar ? 'hidden' : '' }}></i>
                                </div>
                                <div>
                                    <div class="unggah-aksi">
                                        <label class="btn-aksi mb-0" for="gambar_pengantar"><i class="fas fa-upload me-2"></i>Pilih gambar</label>
                                        <button type="button" class="btn-aksi bahaya" data-hapus {{ $punyaGambar ? '' : 'hidden' }}><i class="fas fa-trash me-2"></i>Hapus</button>
                                    </div>
                                    <div class="bantuan">JPG, PNG, atau WEBP. Maksimal 4 MB. Paling bagus berbentuk lebar.</div>
                                    <input type="file" id="gambar_pengantar" name="gambar_pengantar" accept="image/png,image/jpeg,image/webp" hidden data-maks="4">
                                    <input type="hidden" name="hapus_gambar_pengantar" value="0" data-input-hapus>
                                    @error('gambar_pengantar') <div class="pesan-error">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="kartu-form">
                        <h6 class="judul">Tentang kami</h6>
                        <p class="sub">Penjelasan lengkap tentang website ini.</p>

                        <textarea id="about_us" name="about_us" rows="9" class="form-control @error('about_us') is-invalid @enderror" maxlength="10000" data-hitung>{{ old('about_us', $setting->about_us) }}</textarea>
                        <div class="hitung-huruf"><span data-angka>0</span> / 10000</div>
                        @error('about_us') <div class="pesan-error">{{ $message }}</div> @enderror
                    </div>
                </div>

                {{-- ================= TARGET BELAJAR ================= --}}
                <div class="panel-tab {{ $tabAktif === 'target' ? 'aktif' : '' }}" data-panel="target">
                    <div class="kartu-form">
                        <h6 class="judul">Target belajar harian</h6>
                        <p class="sub">Klik tombol − dan + atau pilih angka cepat. Isi 0 kalau ingin berhenti menambah materi baru.</p>

                        @foreach ($targets as $kolom => [$judul, $ket, $ikon, $min, $maks, $pilihan])
                            @php $nilai = (int) old($kolom, $setting->{$kolom}); @endphp
                            <div class="target-item">
                                <div class="target-info">
                                    <div class="target-ikon"><i class="fas {{ $ikon }}"></i></div>
                                    <div>
                                        <div class="target-nama">{{ $judul }}</div>
                                        <div class="target-ket">{{ $ket }}</div>
                                        @error($kolom) <div class="pesan-error">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="target-kontrol">
                                    <div class="stepper" data-stepper data-min="{{ $min }}" data-max="{{ $maks }}">
                                        <button type="button" class="stp" data-langkah="-1" aria-label="Kurangi">−</button>
                                        <input type="number" name="{{ $kolom }}" class="form-control @error($kolom) is-invalid @enderror" value="{{ $nilai }}" min="{{ $min }}" max="{{ $maks }}" inputmode="numeric" aria-label="{{ $judul }}">
                                        <button type="button" class="stp" data-langkah="1" aria-label="Tambah">+</button>
                                    </div>
                                    <div class="preset">
                                        @foreach ($pilihan as $p)
                                            <button type="button" data-nilai="{{ $p }}">{{ $p }}</button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <div class="target-item">
                            <div class="target-info">
                                <div class="target-ikon"><i class="fas fa-clock"></i></div>
                                <div>
                                    <div class="target-nama">Jam ganti hari</div>
                                    <div class="target-ket">Hari belajar baru dimulai pada jam ini. Belajar sebelum jam ini masih dihitung sebagai hari kemarin.</div>
                                    @error('jam_ganti_hari') <div class="pesan-error">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="target-kontrol">
                                <select name="jam_ganti_hari" class="form-select @error('jam_ganti_hari') is-invalid @enderror" aria-label="Jam ganti hari">
                                    @for ($h = 0; $h < 24; $h++)
                                        <option value="{{ $h }}" @selected((int) old('jam_ganti_hari', $setting->jam_ganti_hari) === $h)>{{ sprintf('%02d.00', $h) }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ================= BAR SIMPAN ================= --}}
                <div class="bar-simpan">
                    <div class="status-simpan" id="statusSimpan"><i class="fas fa-check-circle"></i><span>Semua perubahan sudah tersimpan</span></div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn-batal" id="btnBatal"><i class="fas fa-undo me-1"></i> Batalkan</button>
                        <button type="submit" class="btn-simpan" id="btnSimpan"><i class="fas fa-save me-2"></i>Simpan perubahan</button>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form       = document.getElementById('formPengaturan');
            const inputTab   = document.getElementById('inputTab');
            const pils       = document.querySelectorAll('#pilTab .pil');
            const panels     = document.querySelectorAll('.panel-tab');
            const statusEl   = document.getElementById('statusSimpan');
            const btnSimpan  = document.getElementById('btnSimpan');
            let berubah = false, menyimpan = false;

            /* ============ status "ada perubahan" ============ */
            function tandaiBerubah() {
                if (berubah) return;
                berubah = true;
                statusEl.classList.add('berubah');
                statusEl.innerHTML = '<i class="fas fa-exclamation-circle"></i><span>Ada perubahan yang belum disimpan</span>';
            }
            form.addEventListener('input', tandaiBerubah);
            form.addEventListener('change', tandaiBerubah);

            window.addEventListener('beforeunload', (e) => {
                if (berubah && !menyimpan) { e.preventDefault(); e.returnValue = ''; }
            });

            document.getElementById('btnBatal').addEventListener('click', () => {
                if (!berubah || confirm('Buang semua perubahan yang belum disimpan?')) {
                    menyimpan = true;
                    window.location.href = window.location.pathname;
                }
            });

            form.addEventListener('submit', () => {
                menyimpan = true;
                btnSimpan.disabled = true;
                btnSimpan.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
            });

            /* ============ tab ============ */
            function bukaTab(nama) {
                pils.forEach(p => p.classList.toggle('aktif', p.dataset.tab === nama));
                panels.forEach(p => p.classList.toggle('aktif', p.dataset.panel === nama));
                inputTab.value = nama;
            }
            pils.forEach(p => p.addEventListener('click', () => bukaTab(p.dataset.tab)));

            /* ============ stepper + angka cepat ============ */
            document.querySelectorAll('[data-stepper]').forEach(box => {
                const input  = box.querySelector('input');
                const min    = Number(box.dataset.min);
                const maks   = Number(box.dataset.max);
                const minus  = box.querySelector('[data-langkah="-1"]');
                const plus   = box.querySelector('[data-langkah="1"]');
                const preset = box.parentElement.querySelectorAll('.preset button');

                const jepit = (n) => Math.min(Math.max(Number.isFinite(n) ? n : min, min), maks);

                function sinkron() {
                    const n = Number(input.value);
                    minus.disabled = n <= min;
                    plus.disabled  = n >= maks;
                    preset.forEach(b => b.classList.toggle('aktif', Number(b.dataset.nilai) === n));
                }
                function atur(n) {
                    input.value = jepit(Math.round(n));
                    sinkron();
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                }

                minus.addEventListener('click', () => atur(Number(input.value) - 1));
                plus.addEventListener('click',  () => atur(Number(input.value) + 1));
                preset.forEach(b => b.addEventListener('click', () => atur(Number(b.dataset.nilai))));
                input.addEventListener('input', sinkron);
                input.addEventListener('blur', () => { input.value = jepit(Math.round(Number(input.value))); sinkron(); });
                sinkron();
            });

            /* ============ unggah gambar (preview + hapus) ============ */
            document.querySelectorAll('[data-unggah]').forEach(box => {
                const file     = box.querySelector('input[type=file]');
                const img      = box.querySelector('img');
                const ikon     = box.querySelector('.unggah-preview i');
                const btnHapus = box.querySelector('[data-hapus]');
                const inHapus  = box.querySelector('[data-input-hapus]');
                const asli     = box.dataset.asli || '';
                const bawaan   = box.dataset.bawaan || '';

                function tampil(src) {
                    if (src) { img.src = src; img.hidden = false; ikon.hidden = true; }
                    else     { img.hidden = true; ikon.hidden = false; }
                }

                file.addEventListener('change', () => {
                    const f = file.files[0];
                    if (!f) return;
                    if (f.size > Number(file.dataset.maks) * 1024 * 1024) {
                        alert('Ukuran file terlalu besar. Maksimal ' + file.dataset.maks + ' MB.');
                        file.value = '';
                        return;
                    }
                    inHapus.value = '0';
                    btnHapus.hidden = false;
                    tampil(URL.createObjectURL(f));
                });

                btnHapus.addEventListener('click', () => {
                    file.value = '';
                    inHapus.value = '1';
                    btnHapus.hidden = true;
                    tampil(bawaan);
                    tandaiBerubah();
                });
            });

            /* ============ penghitung huruf ============ */
            document.querySelectorAll('textarea[data-hitung]').forEach(ta => {
                const angka = ta.parentElement.querySelector('[data-angka]');
                const hitung = () => { angka.textContent = ta.value.length; };
                ta.addEventListener('input', hitung);
                hitung();
            });
        });
    </script>
@endsection
