@extends('layouts.user.user')

@section('title', 'Ubah Paket Soal')

@section('styles')
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    body, .card, .btn, h4, h5, h6 { font-family: 'Plus Jakarta Sans', sans-serif; }
    .hanzi-text { font-family: 'Noto Sans TC', 'PingFang TC', 'Microsoft JhengHei', 'Plus Jakarta Sans', sans-serif; }

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
    .ph-aksi { display: flex; gap: 8px; flex-wrap: wrap; }

    /* ---------- tombol & panel ---------- */
    .btn-utama, .btn-polos { display: inline-flex; align-items: center; justify-content: center; border-radius: 10px; padding: 0 18px; height: 40px; font-weight: 600; font-size: .85rem; text-decoration: none; cursor: pointer; white-space: nowrap; }
    .btn-utama { background: #1a73e8; color: #fff; border: 1px solid #1a73e8; }
    .btn-utama:hover { background: #1560c4; border-color: #1560c4; color: #fff; }
    .btn-polos { background: #fff; color: #334155; border: 1px solid #e2e8f0; }
    .btn-polos:hover { background: #f1f5f9; color: #1e293b; }
    .panel { background: #fff; border: 1px solid #e9ecef; border-radius: 14px; box-shadow: 0 1px 6px rgba(0,0,0,.05); margin-bottom: 18px; }
    .panel-pad { padding: 20px 24px; }

    /* ---------- form ---------- */
    .f-label { display: block; font-size: .82rem; font-weight: 700; color: #334155; margin-bottom: 6px; }
    .f-hint { font-size: .76rem; color: #94a3b8; margin-top: 4px; }
    .f-hint b { color: #475569; }
    .panel .form-control, .panel .form-select { border-radius: 10px; font-size: .88rem; border-color: #e2e8f0; }
    .panel .form-control:focus, .panel .form-select:focus { border-color: #1a73e8; box-shadow: 0 0 0 3px rgba(26,115,232,.12); }
    .form-aksi { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; margin-bottom: 24px; }
</style>
@endsection

@section('content')
    <div class="container">
        <div class="ph-card">
            <div class="ph-left">
                <div class="ph-icon"><i class="fas fa-clipboard-list"></i></div>
                <div>
                    <h5 class="ph-title">Ubah Paket Soal</h5>
                    <ol class="ph-breadcrumb" aria-label="breadcrumb">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><a href="{{ route('admin.grup-soal.index') }}">Paket Soal</a></li>
                        <li><a href="{{ route('admin.grup-soal.show', $grupSoal) }}">{{ \Illuminate\Support\Str::limit($grupSoal->judul, 40) }}</a></li>
                        <li><span class="bc-active">Ubah</span></li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="page-inner">

            <form method="POST" action="{{ route('admin.grup-soal.update', $grupSoal) }}">
                @csrf
                @method('PUT')

                @if ($errors->any())
                    <div class="alert alert-danger">Ada isian yang perlu diperbaiki. Periksa kolom yang bertanda merah.</div>
                @endif

                <div class="panel panel-pad">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="f-label" for="judul">Judul paket <span class="text-danger">*</span></label>
                            <input type="text" id="judul" name="judul" maxlength="500"
                                   class="form-control hanzi-text @error('judul') is-invalid @enderror"
                                   value="{{ old('judul', $grupSoal->judul) }}" placeholder="mis. Latihan Kosakata TOCFL 1 — Keluarga" autofocus>
                            @error('judul')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <label class="f-label" for="deskripsi">Deskripsi</label>
                            <textarea id="deskripsi" name="deskripsi" rows="3"
                                      class="form-control hanzi-text @error('deskripsi') is-invalid @enderror"
                                      placeholder="Opsional. Tujuan paket, materi yang dicakup, dan sebagainya.">{{ old('deskripsi', $grupSoal->deskripsi) }}</textarea>
                            @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label class="f-label" for="jenis">Jenis <span class="text-danger">*</span></label>
                            <select id="jenis" name="jenis" class="form-select @error('jenis') is-invalid @enderror">
                                @foreach (\App\Support\LabelSoal::JENIS as $k => $label)
                                    <option value="{{ $k }}" @selected(old('jenis', $grupSoal->jenis) === $k)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('jenis')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label class="f-label" for="level_tocfl_id">Level TOCFL</label>
                            <select id="level_tocfl_id" name="level_tocfl_id" class="form-select @error('level_tocfl_id') is-invalid @enderror">
                                <option value="">— Tidak dibatasi —</option>
                                @foreach ($levels as $lv)
                                    <option value="{{ $lv->id }}" @selected((int) old('level_tocfl_id', $grupSoal->level_tocfl_id) === $lv->id)>TOCFL {{ $lv->tingkat }}</option>
                                @endforeach
                            </select>
                            @error('level_tocfl_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4">
                            <label class="f-label" for="grup_kosakata_id">Grup kosakata sumber</label>
                            <select id="grup_kosakata_id" name="grup_kosakata_id" class="form-select @error('grup_kosakata_id') is-invalid @enderror">
                                <option value="">— Tidak ada —</option>
                                @foreach ($grupKosakatas as $g)
                                    <option value="{{ $g->id }}" @selected((int) old('grup_kosakata_id', $grupSoal->grup_kosakata_id) === $g->id)>{{ $g->nama }}</option>
                                @endforeach
                            </select>
                            @error('grup_kosakata_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="f-hint">Hanya catatan asal paket; tidak membatasi soal yang bisa ditambahkan.</div>
                        </div>

                        <div class="col-md-4">
                            <label class="f-label" for="durasi_menit">Durasi (menit)</label>
                            <input type="number" id="durasi_menit" name="durasi_menit" min="1" max="1440"
                                   class="form-control @error('durasi_menit') is-invalid @enderror"
                                   value="{{ old('durasi_menit', $grupSoal->durasi_menit) }}" placeholder="Kosong = bebas waktu">
                            @error('durasi_menit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-4" id="kolomNilaiLulus">
                            <label class="f-label" for="nilai_lulus">Nilai lulus (0–100)</label>
                            <input type="number" id="nilai_lulus" name="nilai_lulus" min="0" max="100"
                                   class="form-control @error('nilai_lulus') is-invalid @enderror"
                                   value="{{ old('nilai_lulus', $grupSoal->nilai_lulus) }}" placeholder="mis. 70">
                            @error('nilai_lulus')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="f-hint">Hanya berlaku untuk jenis Ujian.</div>
                        </div>

                        <div class="col-md-4">
                            <label class="f-label" for="maks_percobaan">Maksimal percobaan</label>
                            <input type="number" id="maks_percobaan" name="maks_percobaan" min="1" max="255"
                                   class="form-control @error('maks_percobaan') is-invalid @enderror"
                                   value="{{ old('maks_percobaan', $grupSoal->maks_percobaan) }}" placeholder="Kosong = tanpa batas">
                            @error('maks_percobaan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-12">
                            <input type="hidden" name="aktif" value="0">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="aktif" name="aktif" value="1"
                                       @checked(old('aktif', $grupSoal->aktif))>
                                <label class="form-check-label" for="aktif">Aktif (boleh dikerjakan)</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-aksi mb-4">
                    <button type="submit" class="btn-utama"><i class="fas fa-floppy-disk me-2"></i>Simpan</button>
                    <a href="{{ route('admin.grup-soal.show', $grupSoal) }}" class="btn-polos">Batal</a>
                </div>
            </form>

            <script>
                (function () {
                    const jenis = document.getElementById('jenis');
                    const kolom = document.getElementById('kolomNilaiLulus');
                    const input = document.getElementById('nilai_lulus');
                    function atur() {
                        const ujian = jenis.value === 'ujian';
                        kolom.style.opacity = ujian ? '1' : '.45';
                        input.disabled = ! ujian;
                    }
                    jenis.addEventListener('change', atur);
                    atur();
                })();
            </script>
        </div>
    </div>
@endsection
