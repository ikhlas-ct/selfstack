@extends('layouts.user.user')

@section('title', 'Profil Saya')

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

        /* ---------- tombol & notifikasi ---------- */
        .btn-utama { display: inline-flex; align-items: center; background: #1565c0; border: 1px solid #1565c0; color: #fff; border-radius: 12px; padding: 10px 24px; font-weight: 700; font-size: .88rem; cursor: pointer; }
        .btn-utama:hover { background: #0f4f9a; color: #fff; }
        .btn-utama:disabled { opacity: .6; cursor: default; }
        .btn-aksi { border: 1px solid #e2e8f0; background: #fff; color: #334155; border-radius: 10px; padding: 9px 18px; font-weight: 600; font-size: .85rem; cursor: pointer; display: inline-flex; align-items: center; }
        .btn-aksi:hover { background: #f1f5f9; }
        .btn-aksi.bahaya { color: #b91c1c; }
        .btn-aksi.bahaya:hover { background: #fee2e2; border-color: #fecaca; }
        .notif { display: flex; align-items: center; gap: 10px; border-radius: 12px; padding: 12px 16px; font-size: .88rem; font-weight: 500; margin-bottom: 16px; }
        .notif-sukses { background: #dcfce7; color: #15803d; }
        .notif-error  { background: #fee2e2; color: #b91c1c; }

        /* ---------- avatar ---------- */
        .avatar { border-radius: 50%; background: #e0f2fe; color: #0369a1; display: flex; align-items: center; justify-content: center; font-weight: 800; overflow: hidden; flex-shrink: 0; }
        .avatar img { width: 100%; height: 100%; object-fit: cover; }

        /* ---------- form ---------- */
        .kartu-form { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px 26px; margin-bottom: 18px; }
        .kartu-form .judul { font-weight: 700; font-size: .98rem; color: #1e293b; margin: 0; }
        .kartu-form .sub { font-size: .82rem; color: #94a3b8; margin: 2px 0 20px; }
        .form-label { font-size: .82rem; font-weight: 600; color: #334155; margin-bottom: 6px; }
        .form-label .wajib { color: #dc2626; margin-left: 2px; }
        .kartu-form .form-control { border-radius: 10px; font-size: .88rem; border-color: #e2e8f0; min-height: 44px; }
        .kartu-form textarea.form-control { padding-top: 12px; line-height: 1.6; }
        .kartu-form .form-control:focus { border-color: #1a73e8; box-shadow: 0 0 0 3px rgba(26,115,232,.12); }
        .kartu-form .is-invalid { border-color: #dc2626 !important; background-image: none !important; }
        .pesan-error { color: #dc2626; font-size: .78rem; margin-top: 5px; }
        .bantuan { color: #94a3b8; font-size: .78rem; margin-top: 5px; }
        .hitung-huruf { text-align: right; color: #94a3b8; font-size: .74rem; margin-top: 4px; }

        .input-ikon { position: relative; }
        .input-ikon > i.ikon-kiri { position: absolute; left: 14px; top: 22px; transform: translateY(-50%); color: #94a3b8; font-size: .9rem; width: 18px; text-align: center; }
        .input-ikon .form-control { padding-left: 42px; }
        .input-ikon.dengan-mata .form-control { padding-right: 46px; }
        .btn-mata { position: absolute; right: 6px; top: 6px; width: 32px; height: 32px; border: none; background: transparent; color: #94a3b8; border-radius: 8px; cursor: pointer; }
        .btn-mata:hover { background: #f1f5f9; color: #334155; }

        .unggah { display: flex; align-items: center; gap: 18px; flex-wrap: wrap; }
        .unggah .avatar { width: 96px; height: 96px; font-size: 2rem; border: 1px dashed #cbd5e1; background: #f8fafc; color: #94a3b8; }
        .unggah .avatar:has(img) { border: none; }
        .unggah-aksi { display: flex; flex-wrap: wrap; gap: 10px; }
        .baris-simpan { display: flex; justify-content: flex-end; margin-top: 22px; }

        @media (max-width: 576px) {
            .kartu-form { padding: 20px 16px; }
            .baris-simpan .btn-utama { width: 100%; justify-content: center; }
        }
    </style>
@endsection

@section('content')
    <div class="container">

        <div class="ph-card">
            <div class="ph-left">
                <div class="ph-icon"><i class="fas fa-user-circle"></i></div>
                <div>
                    <h5 class="ph-title">Profil Saya</h5>
                    <ol class="ph-breadcrumb" aria-label="breadcrumb">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><span class="bc-active">Profil Saya</span></li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="page-inner">

            @if (session('success'))
                <div class="notif notif-sukses"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
            @endif
            @if ($errors->any() || $errors->gantiPassword->any())
                <div class="notif notif-error"><i class="fas fa-exclamation-circle"></i> Ada isian yang belum benar. Cek pesan merah di bawah.</div>
            @endif

            {{-- ================= FORM PROFIL ================= --}}
            <form method="POST" action="{{ route('admin.profil.update') }}" enctype="multipart/form-data" id="formProfil" novalidate>
                @csrf
                @method('PUT')

                <div class="kartu-form">
                    <h6 class="judul">Profil</h6>
                    <p class="sub">Nama dan moto yang menampilkan siapa kamu.</p>

                    <div class="mb-4">
                        <label class="form-label">Foto</label>
                        <div class="unggah" id="unggahFoto" data-inisial="{{ $user->inisial }}">
                            <div class="avatar" id="avatarPreview">
                                @if ($user->foto_url)
                                    <img src="{{ $user->foto_url }}" alt="Foto">
                                @else
                                    {{ $user->inisial }}
                                @endif
                            </div>
                            <div>
                                <div class="unggah-aksi">
                                    <label class="btn-aksi mb-0" for="foto"><i class="fas fa-upload me-2"></i>Pilih foto</label>
                                    <button type="button" class="btn-aksi bahaya" id="btnHapusFoto" {{ filled($user->foto) ? '' : 'hidden' }}><i class="fas fa-trash me-2"></i>Hapus</button>
                                </div>
                                <div class="bantuan">JPG, PNG, atau WEBP. Maksimal 2 MB. Paling bagus berbentuk persegi.</div>
                                <input type="file" id="foto" name="foto" accept="image/png,image/jpeg,image/webp" hidden>
                                <input type="hidden" name="hapus_foto" id="inputHapusFoto" value="0">
                                @error('foto') <div class="pesan-error">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" for="nama">Nama<span class="wajib">*</span></label>
                            <div class="input-ikon">
                                <i class="fas fa-id-badge ikon-kiri"></i>
                                <input type="text" id="nama" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $user->nama) }}" maxlength="100" placeholder="Nama yang tampil di aplikasi" autocomplete="off">
                            </div>
                            @error('nama') <div class="pesan-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="moto">Moto</label>
                            <textarea id="moto" name="moto" rows="2" class="form-control @error('moto') is-invalid @enderror" maxlength="150" placeholder="Kalimat singkat penyemangat belajar">{{ old('moto', $user->moto) }}</textarea>
                            <div class="hitung-huruf"><span id="angkaMoto">0</span> / 150</div>
                            @error('moto') <div class="pesan-error">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="kartu-form">
                    <h6 class="judul">Akun</h6>
                    <p class="sub">Username dan email yang dipakai untuk login.</p>

                    <div class="row g-3">
                        <div class="col-lg-6">
                            <label class="form-label" for="username">Username<span class="wajib">*</span></label>
                            <div class="input-ikon">
                                <i class="fas fa-at ikon-kiri"></i>
                                <input type="text" id="username" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $user->username) }}" maxlength="100" placeholder="tanpa spasi" autocomplete="off" autocapitalize="off">
                            </div>
                            @error('username') <div class="pesan-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label" for="email">Email<span class="wajib">*</span></label>
                            <div class="input-ikon">
                                <i class="fas fa-envelope ikon-kiri"></i>
                                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" maxlength="100" placeholder="nama@contoh.com" autocomplete="off">
                            </div>
                            @error('email') <div class="pesan-error">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="baris-simpan">
                        <button type="submit" class="btn-utama" id="btnSimpanProfil"><i class="fas fa-save me-2"></i>Simpan profil</button>
                    </div>
                </div>
            </form>

            {{-- ================= FORM GANTI PASSWORD ================= --}}
            <form method="POST" action="{{ route('admin.profil.password') }}" id="formPassword" novalidate>
                @csrf
                @method('PUT')

                <div class="kartu-form">
                    <h6 class="judul">Ganti password</h6>
                    <p class="sub">Isi password lama, lalu password baru minimal 8 karakter.</p>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" for="password_lama">Password lama<span class="wajib">*</span></label>
                            <div class="input-ikon dengan-mata">
                                <i class="fas fa-key ikon-kiri"></i>
                                <input type="password" id="password_lama" name="password_lama" class="form-control @error('password_lama', 'gantiPassword') is-invalid @enderror" autocomplete="current-password">
                                <button type="button" class="btn-mata" data-mata="password_lama" aria-label="Tampilkan password"><i class="fas fa-eye"></i></button>
                            </div>
                            @error('password_lama', 'gantiPassword') <div class="pesan-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label" for="password">Password baru<span class="wajib">*</span></label>
                            <div class="input-ikon dengan-mata">
                                <i class="fas fa-lock ikon-kiri"></i>
                                <input type="password" id="password" name="password" class="form-control @error('password', 'gantiPassword') is-invalid @enderror" placeholder="Minimal 8 karakter" autocomplete="new-password">
                                <button type="button" class="btn-mata" data-mata="password" aria-label="Tampilkan password"><i class="fas fa-eye"></i></button>
                            </div>
                            @error('password', 'gantiPassword') <div class="pesan-error">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-lg-6">
                            <label class="form-label" for="password_confirmation">Ulangi password baru<span class="wajib">*</span></label>
                            <div class="input-ikon dengan-mata">
                                <i class="fas fa-lock ikon-kiri"></i>
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" autocomplete="new-password">
                                <button type="button" class="btn-mata" data-mata="password_confirmation" aria-label="Tampilkan password"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                    </div>

                    <div class="baris-simpan">
                        <button type="submit" class="btn-utama" id="btnSimpanPassword"><i class="fas fa-key me-2"></i>Ganti password</button>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            /* ============ foto: preview + hapus ============ */
            const box      = document.getElementById('unggahFoto');
            const file     = document.getElementById('foto');
            const avatar   = document.getElementById('avatarPreview');
            const btnHapus = document.getElementById('btnHapusFoto');
            const inHapus  = document.getElementById('inputHapusFoto');
            const inisial  = box.dataset.inisial;

            function tampilFoto(src) {
                avatar.innerHTML = '';
                if (src) {
                    const im = document.createElement('img');
                    im.alt = 'Foto';
                    im.src = src;
                    avatar.appendChild(im);
                } else {
                    avatar.textContent = inisial;
                }
            }

            file.addEventListener('change', () => {
                const f = file.files[0];
                if (!f) return;
                if (f.size > 2 * 1024 * 1024) {
                    alert('Ukuran foto terlalu besar. Maksimal 2 MB.');
                    file.value = '';
                    return;
                }
                inHapus.value = '0';
                btnHapus.hidden = false;
                tampilFoto(URL.createObjectURL(f));
            });

            btnHapus.addEventListener('click', () => {
                file.value = '';
                inHapus.value = '1';
                btnHapus.hidden = true;
                tampilFoto('');
            });

            /* ============ penghitung huruf moto ============ */
            const moto   = document.getElementById('moto');
            const angka  = document.getElementById('angkaMoto');
            const hitung = () => { angka.textContent = moto.value.length; };
            moto.addEventListener('input', hitung);
            hitung();

            /* ============ tampilkan / sembunyikan password ============ */
            document.querySelectorAll('[data-mata]').forEach(b => b.addEventListener('click', () => {
                const el = document.getElementById(b.dataset.mata);
                const tampil = el.type === 'password';
                el.type = tampil ? 'text' : 'password';
                b.querySelector('i').className = tampil ? 'fas fa-eye-slash' : 'fas fa-eye';
            }));

            /* ============ cegah klik simpan dua kali ============ */
            [['formProfil', 'btnSimpanProfil'], ['formPassword', 'btnSimpanPassword']].forEach(([f, b]) => {
                document.getElementById(f).addEventListener('submit', () => {
                    const tombol = document.getElementById(b);
                    tombol.disabled = true;
                    tombol.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Menyimpan...';
                });
            });
        });
    </script>
@endsection
