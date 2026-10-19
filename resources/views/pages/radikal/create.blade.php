@extends('layouts.user.user')

@section('title', 'Tambah Radikal')

@section('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        body, .card, .table, .btn, h4, h5 { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* ===== PAGE HEADER ===== */
        .ph-card { background: #fff; border: 1px solid #e9ecef; border-radius: 14px; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; margin-bottom: 1.25rem; position: relative; overflow: hidden; box-shadow: 0 1px 6px rgba(0,0,0,.05); }
        .ph-card::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; border-radius: 14px 0 0 14px; }
        .ph-card.create-page::before { background: #16a34a; }
        .ph-left { display: flex; align-items: center; gap: 12px; }
        .ph-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
        .ph-icon.create { background: #dcfce7; color: #16a34a; }
        .ph-title { font-size: 1.05rem; font-weight: 700; color: #1e293b; letter-spacing: -.2px; line-height: 1.2; margin: 0; }
        .ph-breadcrumb { display: flex; align-items: center; gap: 4px; flex-wrap: wrap; margin-top: 4px; list-style: none; padding: 0; margin-bottom: 0; }
        .ph-breadcrumb li { display: flex; align-items: center; }
        .ph-breadcrumb li + li::before { content: '›'; color: #cbd5e1; font-size: .7rem; margin: 0 4px; }
        .ph-breadcrumb a { font-size: .75rem; color: #1a73e8; text-decoration: none; }
        .ph-breadcrumb a:hover { text-decoration: underline; }
        .ph-breadcrumb .bc-active { font-size: .75rem; color: #94a3b8; }

        /* ===== CARD, FILTER, FORM ===== */
        .form-card { border: none; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,.07); }
        .section-divider { background: #f8f9fa; border-left: 4px solid #1269db; padding: 8px 14px; border-radius: 0 6px 6px 0; font-weight: 600; font-size: .9rem; color: #1269db; margin-bottom: 1rem; }
        label { font-size: .875rem; font-weight: 500; }
        .required-mark { color: #dc3545; }
        .form-control, .form-select { border-radius: 10px; border: 1.5px solid #e2e8f0; font-size: .83rem; padding: 7px 12px; color: #334155; background-color: #f8fafc; transition: border-color .2s, box-shadow .2s; }
        .form-control:focus, .form-select:focus { border-color: #1a73e8; background: #fff; box-shadow: 0 0 0 3px rgba(26,115,232,.12); }
        .form-control::placeholder { color: #94a3b8; }
        .hanzi-input { font-size: 1.15rem; }

        /* ===== BADGES ===== */
        .chip-varian { display: inline-block; font-size: 1.05rem; line-height: 1; padding: 3px 7px; margin: 0 4px 2px 0; border-radius: 7px; background: #f5f3ff; color: #7c3aed; font-family: 'Noto Sans TC', 'PingFang TC', 'Microsoft JhengHei', sans-serif; }

        /* ===== ACTION BUTTONS ===== */
        .btn-primary { background: linear-gradient(135deg, #1a73e8, #1558b0); border: none; border-radius: 10px; font-weight: 600; font-size: .83rem; padding: 8px 18px; box-shadow: 0 2px 8px rgba(26,115,232,.35); transition: all .2s ease; }
        .btn-primary:hover { background: linear-gradient(135deg, #1558b0, #0f3e82); box-shadow: 0 4px 14px rgba(26,115,232,.45); transform: translateY(-1px); }
        .btn-outline-secondary { border-radius: 10px; font-size: .83rem; border-color: #e2e8f0; color: #64748b; padding: 7px 12px; }
        .btn-outline-secondary:hover { background: #f1f5f9; border-color: #cbd5e1; color: #334155; }
    </style>
@endsection

@section('content')
    <div class="container">

        <div class="ph-card create-page">
            <div class="ph-left">
                <div class="ph-icon create"><i class="fas fa-plus"></i></div>
                <div>
                    <h5 class="ph-title">Tambah Radikal</h5>
                    <ol class="ph-breadcrumb" aria-label="breadcrumb">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><a href="{{ route('admin.radikal.index') }}">Radikal</a></li>
                        <li><span class="bc-active">Tambah</span></li>
                    </ol>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.radikal.import') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-file-excel me-1"></i> Import Excel
                </a>
                <a href="{{ route('admin.radikal.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        <div class="page-inner">
            <form action="{{ route('admin.radikal.store') }}" method="POST" novalidate>
                @csrf

                <div class="row g-4">
                    <div class="col-lg-8">
                        {{-- Isian radikal --}}
                        @php
                            $varianNilai = old('varian', $radikal->varian ?? []);
                            if (is_array($varianNilai)) {
                                $varianNilai = implode(', ', $varianNilai);
                            }
                            $galatVarian = $errors->first('varian') ?: $errors->first('varian.*');
                        @endphp

                        <div class="card form-card mb-4">
                            <div class="card-body">
                                <div class="section-divider"><i class="fas fa-cubes me-2"></i>Data Radikal</div>

                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="hanzi" class="form-label">Hanzi <span class="required-mark">*</span></label>
                                        <input type="text" id="hanzi" name="hanzi" maxlength="8"
                                            class="form-control hanzi-input @error('hanzi') is-invalid @enderror"
                                            value="{{ old('hanzi', $radikal->hanzi) }}" placeholder="例：水" required autofocus>
                                        @error('hanzi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="pinyin" class="form-label">Pinyin <span class="required-mark">*</span></label>
                                        <input type="text" id="pinyin" name="pinyin" maxlength="100"
                                            class="form-control @error('pinyin') is-invalid @enderror"
                                            value="{{ old('pinyin', $radikal->pinyin) }}" placeholder="shuǐ" required>
                                        @error('pinyin')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="nomor_kangxi" class="form-label">Nomor Kangxi</label>
                                        <input type="number" id="nomor_kangxi" name="nomor_kangxi" min="1" max="214"
                                            class="form-control @error('nomor_kangxi') is-invalid @enderror"
                                            value="{{ old('nomor_kangxi', $radikal->nomor_kangxi) }}" placeholder="1 - 214">
                                        @error('nomor_kangxi')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label for="arti_indonesia" class="form-label">Arti Indonesia <span class="required-mark">*</span></label>
                                        <input type="text" id="arti_indonesia" name="arti_indonesia" maxlength="500"
                                            class="form-control @error('arti_indonesia') is-invalid @enderror"
                                            value="{{ old('arti_indonesia', $radikal->arti_indonesia) }}" placeholder="air" required>
                                        @error('arti_indonesia')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="varian" class="form-label">Varian</label>
                                        <input type="text" id="varian" name="varian" autocomplete="off"
                                            class="form-control hanzi-input @if($galatVarian) is-invalid @endif"
                                            value="{{ $varianNilai }}" placeholder="氵, 氺">
                                        <div class="form-text">Bentuk lain radikal ini saat dipakai sebagai komponen. Pisahkan dengan koma atau tulis berurutan.</div>
                                        @if ($galatVarian)
                                            <div class="invalid-feedback">{{ $galatVarian }}</div>
                                        @endif
                                    </div>

                                    <div class="col-md-3">
                                        <label for="jumlah_goresan" class="form-label">Jumlah Goresan</label>
                                        <input type="number" id="jumlah_goresan" name="jumlah_goresan" min="1" max="30"
                                            class="form-control @error('jumlah_goresan') is-invalid @enderror"
                                            value="{{ old('jumlah_goresan', $radikal->jumlah_goresan) }}" placeholder="4">
                                        @error('jumlah_goresan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-3">
                                        <label for="urutan" class="form-label">Urutan Belajar</label>
                                        <input type="number" id="urutan" name="urutan" min="0"
                                            class="form-control @error('urutan') is-invalid @enderror"
                                            value="{{ old('urutan', $radikal->exists ? $radikal->urutan : null) }}"
                                            placeholder="{{ $radikal->exists ? 'Tetap' : 'Otomatis' }}">
                                        @error('urutan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-12">
                                        <label for="keterangan" class="form-label">Keterangan</label>
                                        <textarea id="keterangan" name="keterangan" rows="3" maxlength="2000"
                                            class="form-control @error('keterangan') is-invalid @enderror"
                                            placeholder="Contoh: Karakter dengan 氵 biasanya berhubungan dengan air atau cairan, mis. 河, 海, 洗.">{{ old('keterangan', $radikal->keterangan) }}</textarea>
                                        <div class="form-text">Cara mengenali radikal dan contoh karakter yang memakainya (opsional).</div>
                                        @error('keterangan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card form-card mb-4">
                            <div class="card-body">
                                <div class="section-divider"><i class="fas fa-lightbulb me-2"></i>Petunjuk</div>
                                <ul class="text-muted ps-3 mb-0" style="font-size:.83rem;">
                                    <li class="mb-2">Hanzi, pinyin, dan arti Indonesia wajib diisi. Hanzi tidak boleh sama dengan radikal lain.</li>
                                    <li class="mb-2">Nomor Kangxi (1-214) juga unik. Kosongkan kalau bukan bagian dari 214 radikal Kangxi.</li>
                                    <li class="mb-2">Varian diisi bentuk lain radikal, mis. <span class="chip-varian">氵</span> untuk 水. Pisahkan dengan koma.</li>
                                    <li>Urutan dikosongkan = otomatis ditaruh di bagian akhir.</li>
                                </ul>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i> Simpan Radikal
                            </button>
                            <a href="{{ route('admin.radikal.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
