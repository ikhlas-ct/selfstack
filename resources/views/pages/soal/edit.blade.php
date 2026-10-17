@extends('layouts.user.user')

@section('title', 'Ubah Soal')

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

    .panel-judul { font-size: .95rem; font-weight: 700; color: #1e293b; margin: 0 0 14px; }
    .form-bagian + .form-bagian { border-top: 1px dashed #e2e8f0; padding-top: 22px; margin-top: 22px; }

    .opsi { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 10px; }
    .opsi-radio { position: relative; flex-shrink: 0; margin: 0; cursor: pointer; }
    .opsi-radio input { position: absolute; opacity: 0; inset: 0; cursor: pointer; }
    .opsi-radio span { width: 40px; height: 40px; border-radius: 10px; border: 1.5px solid #e2e8f0; background: #fff; color: #64748b; display: flex; align-items: center; justify-content: center; font-weight: 800; transition: .12s; }
    .opsi-radio input:checked + span { background: #16a34a; border-color: #16a34a; color: #fff; }
    .opsi-radio input:focus-visible + span { box-shadow: 0 0 0 3px rgba(22,163,74,.25); }
</style>
@endsection

@section('content')
    @php $L = \App\Support\LabelSoal::class; @endphp

    <div class="container">
        <div class="ph-card">
            <div class="ph-left">
                <div class="ph-icon"><i class="fas fa-circle-question"></i></div>
                <div>
                    <h5 class="ph-title">Ubah Soal</h5>
                    <ol class="ph-breadcrumb" aria-label="breadcrumb">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><a href="{{ route('admin.grup-soal.index') }}">Paket Soal</a></li>
                        <li><a href="{{ route('admin.grup-soal.show', $grupSoal) }}">{{ \Illuminate\Support\Str::limit($grupSoal->judul, 40) }}</a></li>
                        <li><span class="bc-active">Ubah soal</span></li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="page-inner">

            <form method="POST" action="{{ route('admin.soal.update', $soal) }}">
                @csrf
                @method('PUT')

                @if ($errors->any())
                    <div class="alert alert-danger">Ada isian yang perlu diperbaiki. Periksa kolom yang bertanda merah.</div>
                @endif

                <div class="panel panel-pad">

                    {{-- ================= 1. Klasifikasi ================= --}}
                    <div class="form-bagian">
                        <h6 class="panel-judul">1. Klasifikasi</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="f-label" for="bagian">Bagian <span class="text-danger">*</span></label>
                                <select id="bagian" name="bagian" class="form-select @error('bagian') is-invalid @enderror">
                                    @foreach ($L::BAGIAN as $k => $label)
                                        <option value="{{ $k }}" @selected(old('bagian', $soal->bagian) === $k)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('bagian')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label class="f-label" for="tipe">Tipe soal</label>
                                <select id="tipe" name="tipe" class="form-select @error('tipe') is-invalid @enderror">
                                    <option value="">— Tanpa tipe —</option>
                                    @foreach ($L::TIPE_GROUP as $grup => $daftar)
                                        <optgroup label="{{ $grup }}">
                                            @foreach ($daftar as $kunci)
                                                <option value="{{ $kunci }}" @selected(old('tipe', $soal->tipe) === $kunci)>{{ $L::TIPE[$kunci] ?? $kunci }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                @error('tipe')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="f-hint">Soal tipe kosakata / karakter / radikal harus ditautkan ke itemnya (bagian 5).</div>
                            </div>

                            <div class="col-md-4">
                                <label class="f-label" for="tingkat">Tingkat (1–6) <span class="text-danger">*</span></label>
                                <input type="number" id="tingkat" name="tingkat" min="1" max="6"
                                       class="form-control @error('tingkat') is-invalid @enderror"
                                       value="{{ old('tingkat', $soal->tingkat) }}">
                                @error('tingkat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label class="f-label" for="poin">Poin <span class="text-danger">*</span></label>
                                <input type="number" id="poin" name="poin" min="1" max="255"
                                       class="form-control @error('poin') is-invalid @enderror"
                                       value="{{ old('poin', $soal->poin) }}">
                                @error('poin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label class="f-label" for="urutan">Urutan di paket <span class="text-danger">*</span></label>
                                <input type="number" id="urutan" name="urutan" min="0" max="65535"
                                       class="form-control @error('urutan') is-invalid @enderror"
                                       value="{{ old('urutan', $soal->urutan) }}">
                                @error('urutan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- ================= 2. Isi soal ================= --}}
                    <div class="form-bagian">
                        <h6 class="panel-judul">2. Isi soal</h6>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="f-label" for="pertanyaan">Pertanyaan <span class="text-danger">*</span></label>
                                <textarea id="pertanyaan" name="pertanyaan" rows="3"
                                          class="form-control hanzi-text @error('pertanyaan') is-invalid @enderror"
                                          placeholder="mis. 「學生」的意思是什麼？">{{ old('pertanyaan', $soal->pertanyaan) }}</textarea>
                                @error('pertanyaan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="f-label" for="paragraf_id">Bacaan (untuk soal Reading)</label>
                                <select id="paragraf_id" name="paragraf_id" class="form-select @error('paragraf_id') is-invalid @enderror">
                                    <option value="">— Tidak ada —</option>
                                    @foreach ($paragrafs as $pg)
                                        <option value="{{ $pg->id }}" @selected((int) old('paragraf_id', $soal->paragraf_id) === $pg->id)>#{{ $pg->id }} — {{ \Illuminate\Support\Str::limit($pg->judul, 50) }}</option>
                                    @endforeach
                                </select>
                                @error('paragraf_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="f-label" for="audio">Audio (Listening)</label>
                                <input type="text" id="audio" name="audio" maxlength="1000"
                                       class="form-control @error('audio') is-invalid @enderror"
                                       value="{{ old('audio', $soal->audio) }}" placeholder="Path atau URL file audio">
                                @error('audio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-12">
                                <label class="f-label" for="paragraf">Teks bacaan khusus soal ini</label>
                                <textarea id="paragraf" name="paragraf" rows="3"
                                          class="form-control hanzi-text @error('paragraf') is-invalid @enderror"
                                          placeholder="Opsional. Teks pendek yang ditampilkan di atas pertanyaan.">{{ old('paragraf', $soal->paragraf) }}</textarea>
                                @error('paragraf')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- ================= 3. Pilihan jawaban ================= --}}
                    <div class="form-bagian">
                        <h6 class="panel-judul">3. Pilihan jawaban <span class="f-hint" style="font-weight:500">— klik huruf untuk menandai jawaban yang benar</span></h6>

                        @foreach (['A', 'B', 'C', 'D'] as $h)
                            @php $kolom = 'pilihan_' . strtolower($h); @endphp
                            <div class="opsi">
                                <label class="opsi-radio" title="Tandai {{ $h }} sebagai jawaban benar">
                                    <input type="radio" name="jawaban_benar" value="{{ $h }}" @checked(old('jawaban_benar', $soal->jawaban_benar) === $h)>
                                    <span>{{ $h }}</span>
                                </label>
                                <div class="flex-grow-1">
                                    <input type="text" name="{{ $kolom }}" maxlength="1000"
                                           class="form-control hanzi-text @error($kolom) is-invalid @enderror"
                                           value="{{ old($kolom, $soal->$kolom) }}" placeholder="Pilihan {{ $h }}">
                                    @error($kolom)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        @endforeach
                        @error('jawaban_benar')<div class="text-danger small mb-2">Pilih salah satu huruf sebagai jawaban benar.</div>@enderror

                        <label class="f-label mt-3" for="penjelasan">Penjelasan jawaban</label>
                        <textarea id="penjelasan" name="penjelasan" rows="3"
                                  class="form-control hanzi-text @error('penjelasan') is-invalid @enderror"
                                  placeholder="Opsional. Ditampilkan setelah soal dijawab.">{{ old('penjelasan', $soal->penjelasan) }}</textarea>
                        @error('penjelasan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- ================= 4. Pola tata bahasa ================= --}}
                    <div class="form-bagian">
                        <h6 class="panel-judul">4. Pola tata bahasa <span class="f-hint" style="font-weight:500">— untuk soal bagian Tata Bahasa</span></h6>
                        <select name="pola_tata_bahasa_id" class="form-select @error('pola_tata_bahasa_id') is-invalid @enderror">
                            <option value="">— Tidak ada —</option>
                            @foreach ($polaTataBahasas as $pola)
                                <option value="{{ $pola->id }}" @selected((int) old('pola_tata_bahasa_id', $soal->pola_tata_bahasa_id) === $pola->id)>#{{ $pola->id }} — {{ \Illuminate\Support\Str::limit($pola->judul, 60) }}</option>
                            @endforeach
                        </select>
                        @error('pola_tata_bahasa_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- ================= 5. Tautan item hafalan ================= --}}
                    <div class="form-bagian">
                        <h6 class="panel-judul">5. Tautan ke item hafalan</h6>
                        <p class="f-hint mb-3" style="margin-top:-8px">
                            Isi ID-nya (lihat di halaman Kosakata / Karakter). Jawaban soal akan memperbarui hafalan item yang ditautkan:
                            jika karakter terisi → hafalan karakter, jika radikal terisi → hafalan radikal, selain itu → hafalan kosakata.
                        </p>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="f-label" for="kosakata_id">ID Kosakata</label>
                                <input type="number" id="kosakata_id" name="kosakata_id" min="1"
                                       class="form-control @error('kosakata_id') is-invalid @enderror"
                                       value="{{ old('kosakata_id', $soal->kosakata_id) }}">
                                @error('kosakata_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                @if ($soal->kosakata)
                                    <div class="f-hint">Saat ini: <b class="hanzi-text">{{ $soal->kosakata->hanzi }}</b> — {{ $soal->kosakata->arti_pilihan }}</div>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label class="f-label" for="karakter_id">ID Karakter</label>
                                <input type="number" id="karakter_id" name="karakter_id" min="1"
                                       class="form-control @error('karakter_id') is-invalid @enderror"
                                       value="{{ old('karakter_id', $soal->karakter_id) }}">
                                @error('karakter_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                @if ($soal->karakter)
                                    <div class="f-hint">Saat ini: <b class="hanzi-text">{{ $soal->karakter->hanzi }}</b></div>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label class="f-label" for="radikal_id">ID Radikal</label>
                                <input type="number" id="radikal_id" name="radikal_id" min="1"
                                       class="form-control @error('radikal_id') is-invalid @enderror"
                                       value="{{ old('radikal_id', $soal->radikal_id) }}">
                                @error('radikal_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                @if ($soal->radikal)
                                    <div class="f-hint">Saat ini: <b class="hanzi-text">{{ $soal->radikal->hanzi }}</b></div>
                                @endif
                            </div>

                            <div class="col-md-4">
                                <label class="f-label" for="contoh_kalimat_id">ID Contoh kalimat</label>
                                <input type="number" id="contoh_kalimat_id" name="contoh_kalimat_id" min="1"
                                       class="form-control @error('contoh_kalimat_id') is-invalid @enderror"
                                       value="{{ old('contoh_kalimat_id', $soal->contoh_kalimat_id) }}">
                                @error('contoh_kalimat_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                @if ($soal->contohKalimat)
                                    <div class="f-hint">Saat ini: <b class="hanzi-text">{{ \Illuminate\Support\Str::limit($soal->contohKalimat->hanzi, 30) }}</b></div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ================= 6. Validasi ================= --}}
                    <div class="form-bagian">
                        <h6 class="panel-judul">6. Validasi</h6>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="f-label" for="status_validasi">Status <span class="text-danger">*</span></label>
                                <select id="status_validasi" name="status_validasi" class="form-select @error('status_validasi') is-invalid @enderror">
                                    @foreach ($L::STATUS as $k => $label)
                                        <option value="{{ $k }}" @selected(old('status_validasi', $soal->status_validasi) === $k)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('status_validasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="f-hint">"Ditolak" menyembunyikan soal dari pengerjaan tanpa menghapusnya.</div>
                            </div>

                            <div class="col-md-8">
                                <label class="f-label" for="catatan_validasi">Catatan validasi</label>
                                <textarea id="catatan_validasi" name="catatan_validasi" rows="2"
                                          class="form-control @error('catatan_validasi') is-invalid @enderror"
                                          placeholder="Opsional. Mis. alasan soal ditolak.">{{ old('catatan_validasi', $soal->catatan_validasi) }}</textarea>
                                @error('catatan_validasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-aksi mb-4">
                    <button type="submit" class="btn-utama"><i class="fas fa-floppy-disk me-2"></i>Simpan</button>
                    <a href="{{ route('admin.grup-soal.show', $grupSoal) }}" class="btn-polos">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
