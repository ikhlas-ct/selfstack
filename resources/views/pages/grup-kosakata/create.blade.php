@extends('layouts.user.user')

@section('title', 'Tambah Grup Kosakata')

@section('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    body, .card, .btn, h4, h5 { font-family: 'Plus Jakarta Sans', sans-serif; }

    /* ===== PAGE HEADER ===== */
    .ph-card { background: #fff; border: 1px solid #e9ecef; border-radius: 14px; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; margin-bottom: 1.25rem; position: relative; overflow: hidden; box-shadow: 0 1px 6px rgba(0,0,0,.05); }
    .ph-card::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; border-radius: 14px 0 0 14px; }
    .ph-card.create-page::before { background: #16a34a; }
    .ph-card.edit-page::before { background: #ca8a04; }
    .ph-left { display: flex; align-items: center; gap: 12px; }
    .ph-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
    .ph-icon.create { background: #dcfce7; color: #16a34a; }
    .ph-icon.edit { background: #fef9c3; color: #a16207; }
    .ph-title { font-size: 1.05rem; font-weight: 700; color: #1e293b; letter-spacing: -.2px; line-height: 1.2; margin: 0; }
    .ph-breadcrumb { display: flex; align-items: center; gap: 4px; flex-wrap: wrap; margin-top: 4px; list-style: none; padding: 0; margin-bottom: 0; }
    .ph-breadcrumb li { display: flex; align-items: center; }
    .ph-breadcrumb li + li::before { content: '›'; color: #cbd5e1; font-size: .7rem; margin: 0 4px; }
    .ph-breadcrumb a { font-size: .75rem; color: #1a73e8; text-decoration: none; }
    .ph-breadcrumb a:hover { text-decoration: underline; }
    .ph-breadcrumb .bc-active { font-size: .75rem; color: #94a3b8; }

    /* ===== FORM ===== */
    label { font-size: .875rem; font-weight: 500; }
    .required-mark { color: #dc3545; }
    .section-divider { background: #f8f9fa; border-left: 4px solid #1269db; padding: 8px 14px; border-radius: 0 6px 6px 0; font-weight: 600; font-size: .9rem; color: #1269db; margin-bottom: 1rem; }
    .form-card { border: none; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,.07); }
    .form-control { border-radius: 10px; border: 1.5px solid #e2e8f0; font-size: .83rem; padding: 7px 12px; color: #334155; background-color: #f8fafc; transition: border-color .2s, box-shadow .2s; }
    .form-control:focus { border-color: #1a73e8; background: #fff; box-shadow: 0 0 0 3px rgba(26,115,232,.12); }
    .form-control::placeholder { color: #94a3b8; }

    /* ===== BUTTONS ===== */
    .btn-primary { background: linear-gradient(135deg, #1a73e8, #1558b0); border: none; border-radius: 10px; font-weight: 600; font-size: .83rem; padding: 8px 18px; box-shadow: 0 2px 8px rgba(26,115,232,.35); transition: all .2s ease; }
    .btn-primary:hover { background: linear-gradient(135deg, #1558b0, #0f3e82); box-shadow: 0 4px 14px rgba(26,115,232,.45); transform: translateY(-1px); }
    .btn-outline-secondary { border-radius: 10px; font-size: .83rem; border-color: #e2e8f0; color: #64748b; padding: 7px 12px; }
    .btn-outline-secondary:hover { background: #f1f5f9; border-color: #cbd5e1; color: #334155; }
</style>
@endsection

@section('content')
    <div class="container">

        {{-- Header – di LUAR page-inner --}}
        <div class="ph-card create-page">
            <div class="ph-left">
                <div class="ph-icon create"><i class="fas fa-plus"></i></div>
                <div>
                    <h5 class="ph-title">Tambah Grup Kosakata</h5>
                    <ol class="ph-breadcrumb" aria-label="breadcrumb">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><a href="{{ route('admin.grup-kosakata.index') }}">Grup Kosakata</a></li>
                        <li><span class="bc-active">Tambah</span></li>
                    </ol>
                </div>
            </div>
            <a href="{{ route('admin.grup-kosakata.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="page-inner">
            <form action="{{ route('admin.grup-kosakata.store') }}" method="POST" novalidate>
                @csrf

                <div class="row">
                    <div class="col-lg-8">
                        <div class="card form-card mb-4">
                            <div class="card-body">
                                <div class="section-divider"><i class="fas fa-book me-2"></i>Data Grup / Buku</div>

                                <div class="row g-3">
                                    <div class="col-12">
                                        <label for="nama" class="form-label">Nama Grup / Buku <span class="required-mark">*</span></label>
                                        <input type="text" id="nama" name="nama" maxlength="255"
                                            class="form-control @error('nama') is-invalid @enderror"
                                            value="{{ old('nama') }}"
                                            placeholder="Contoh: 實用視聽華語 1" required autofocus>
                                        <div class="form-text">Satu grup mewakili satu buku. Nama harus unik.</div>
                                        @error('nama')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label for="keterangan" class="form-label">Keterangan</label>
                                        <textarea id="keterangan" name="keterangan" rows="4" maxlength="2000"
                                            class="form-control @error('keterangan') is-invalid @enderror"
                                            placeholder="Contoh: Buku pegangan kelas, pelajaran 1–15. Dibeli tahun 2026.">{{ old('keterangan') }}</textarea>
                                        <div class="form-text">Opsional. Catatan bebas tentang buku ini.</div>
                                        @error('keterangan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info" style="font-size:.83rem;border-radius:12px;">
                            <i class="fas fa-info-circle me-1"></i>
                            Setelah disimpan, kamu langsung diarahkan ke halaman grup untuk memasukkan kosakata dan nomor pelajarannya.
                        </div>

                        <div class="d-flex gap-2 mb-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan &amp; Isi Kosakata
                            </button>
                            <a href="{{ route('admin.grup-kosakata.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
