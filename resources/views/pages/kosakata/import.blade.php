@extends('layouts.user.user')

@section('title', 'Import Kosakata')

@section('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    body, .card, .table, .btn, h4, h5 { font-family: 'Plus Jakarta Sans', sans-serif; }

    /* ===== PAGE HEADER ===== */
    .ph-card { background: #fff; border: 1px solid #e9ecef; border-radius: 14px; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; margin-bottom: 1.25rem; position: relative; overflow: hidden; box-shadow: 0 1px 6px rgba(0,0,0,.05); }
    .ph-card::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; border-radius: 14px 0 0 14px; }
    .ph-card.import-page::before { background: #16a34a; }
    .ph-left { display: flex; align-items: center; gap: 12px; }
    .ph-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
    .ph-icon.import { background: #dcfce7; color: #16a34a; }
    .ph-title { font-size: 1.05rem; font-weight: 700; color: #1e293b; letter-spacing: -.2px; line-height: 1.2; margin: 0; }
    .ph-breadcrumb { display: flex; align-items: center; gap: 4px; flex-wrap: wrap; margin-top: 4px; list-style: none; padding: 0; margin-bottom: 0; }
    .ph-breadcrumb li { display: flex; align-items: center; }
    .ph-breadcrumb li + li::before { content: '›'; color: #cbd5e1; font-size: .7rem; margin: 0 4px; }
    .ph-breadcrumb a { font-size: .75rem; color: #1a73e8; text-decoration: none; }
    .ph-breadcrumb a:hover { text-decoration: underline; }
    .ph-breadcrumb .bc-active { font-size: .75rem; color: #94a3b8; }

    /* ===== FORM / CARD ===== */
    label { font-size: .875rem; font-weight: 500; }
    .required-mark { color: #dc3545; }
    .section-divider { background: #f8f9fa; border-left: 4px solid #1269db; padding: 8px 14px; border-radius: 0 6px 6px 0; font-weight: 600; font-size: .9rem; color: #1269db; margin-bottom: 1rem; }
    .form-card { border: none; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,.07); }
    .form-control { border-radius: 10px; border: 1.5px solid #e2e8f0; font-size: .83rem; padding: 7px 12px; color: #334155; background-color: #f8fafc; }
    .form-control:focus { border-color: #1a73e8; background: #fff; box-shadow: 0 0 0 3px rgba(26,115,232,.12); }

    .mode-option { border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 12px 14px; background: #fafbfc; }
    .mode-option + .mode-option { margin-top: 8px; }
    .mode-option .form-check-label { font-size: .85rem; font-weight: 600; color: #1e293b; }
    .mode-option small { display: block; color: #64748b; font-size: .78rem; margin-top: 2px; }
    /* Radio di kiri, teks membungkus di kanan (tidak keluar dari kartu) */
    .form-check.mode-option { display: flex; align-items: flex-start; gap: 12px; padding-left: 14px !important; }
    .form-check.mode-option .form-check-input { float: none; margin: 3px 0 0 0; flex-shrink: 0; }
    .form-check.mode-option .form-check-label { display: block; min-width: 0; white-space: normal; overflow-wrap: anywhere; }

    /* ===== TABEL KOLOM ===== */
    .table thead th { background: #f8fafc; color: #64748b; font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; padding: 10px 12px; border-bottom: 2px solid #e2e8f0; border-top: none; white-space: nowrap; }
    .table tbody td { padding: 9px 12px; vertical-align: middle; font-size: .8rem; color: #334155; border-bottom: 1px solid #f1f5f9; }
    .table tbody tr:last-child td { border-bottom: none; }
    code { font-size: .78rem; color: #1269db; background: #e8f0fe; padding: 1px 6px; border-radius: 5px; }

    /* ===== HASIL ===== */
    .hasil-box { background: #f8fafc; border-radius: 12px; padding: 12px 14px; text-align: center; }
    .hasil-angka { font-size: 1.5rem; font-weight: 800; line-height: 1.1; color: #1e293b; }
    .hasil-label { font-size: .72rem; font-weight: 600; text-transform: uppercase; letter-spacing: .4px; color: #64748b; margin-top: 2px; }
    .galat-list { max-height: 260px; overflow-y: auto; font-size: .8rem; }

    /* ===== BUTTONS ===== */
    .btn-primary { background: linear-gradient(135deg, #1a73e8, #1558b0); border: none; border-radius: 10px; font-weight: 600; font-size: .83rem; padding: 8px 18px; box-shadow: 0 2px 8px rgba(26,115,232,.35); transition: all .2s ease; }
    .btn-primary:hover { background: linear-gradient(135deg, #1558b0, #0f3e82); box-shadow: 0 4px 14px rgba(26,115,232,.45); transform: translateY(-1px); }
    .btn-outline-secondary { border-radius: 10px; font-size: .83rem; border-color: #e2e8f0; color: #64748b; padding: 7px 12px; }
    .btn-outline-secondary:hover { background: #f1f5f9; border-color: #cbd5e1; color: #334155; }
    .btn-excel { border-radius: 10px; font-size: .83rem; font-weight: 600; border: 1.5px solid #16a34a; color: #16a34a; background: #fff; padding: 7px 14px; }
    .btn-excel:hover { background: #16a34a; color: #fff; }
</style>
@endsection

@section('content')
    <div class="container">

        {{-- Header – di LUAR page-inner --}}
        <div class="ph-card import-page">
            <div class="ph-left">
                <div class="ph-icon import"><i class="fas fa-file-excel"></i></div>
                <div>
                    <h5 class="ph-title">Import Kosakata dari Excel</h5>
                    <ol class="ph-breadcrumb" aria-label="breadcrumb">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><a href="{{ route('admin.kosakata.index') }}">Kosakata</a></li>
                        <li><span class="bc-active">Import Excel</span></li>
                    </ol>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.kosakata.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        <div class="page-inner">

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            {{-- ===== HASIL IMPORT ===== --}}
            @if (session('hasil'))
                @php $h = session('hasil'); @endphp
                <div class="card form-card mb-4">
                    <div class="card-body">
                        <div class="section-divider"><i class="fas fa-clipboard-check me-2"></i>Hasil Import</div>

                        @php
                            $kotak = [
                                ['kosakata_baru', 'Kosakata baru', 'text-success'],
                                ['kosakata_diperbarui', 'Kosakata diperbarui', 'text-primary'],
                                ['kosakata_dilewati', 'Kosakata dilewati', ''],
                                ['kategori_baru', 'Kategori baru', ''],
                                ['contoh_baru', 'Contoh baru', 'text-success'],
                                ['contoh_diperbarui', 'Contoh diperbarui', 'text-primary'],
                                ['contoh_dilewati', 'Contoh dilewati', ''],
                                ['karakter_baru', 'Karakter baru', 'text-success'],
                                ['karakter_diperbarui', 'Karakter diperbarui', 'text-primary'],
                                ['karakter_dilewati', 'Karakter dilewati', ''],
                                ['pecahan_baru', 'Pecahan baru', 'text-success'],
                                ['pecahan_diperbarui', 'Pecahan diperbarui', 'text-primary'],
                                ['pecahan_dilewati', 'Pecahan dilewati', ''],
                                ['relasi_baru', 'Relasi baru', 'text-success'],
                                ['relasi_dilewati', 'Relasi dilewati', ''],
                                ['grup_baru', 'Grup baru', 'text-success'],
                                ['anggota_baru', 'Anggota grup baru', 'text-success'],
                                ['anggota_dilewati', 'Anggota grup dilewati', ''],
                            ];
                        @endphp

                        <div class="row g-2 mb-3">
                            @foreach ($kotak as [$kunci, $label, $warna])
                                <div class="col-6 col-md-3">
                                    <div class="hasil-box">
                                        <div class="hasil-angka {{ $warna }}">{{ $h[$kunci] ?? 0 }}</div>
                                        <div class="hasil-label">{{ $label }}</div>
                                    </div>
                                </div>
                            @endforeach
                            <div class="col-6 col-md-3">
                                <div class="hasil-box">
                                    <div class="hasil-angka {{ $h['gagal'] ? 'text-danger' : '' }}">{{ $h['gagal'] }}</div>
                                    <div class="hasil-label">Baris gagal</div>
                                </div>
                            </div>
                        </div>

                        @if ($h['gagal'] > 0)
                            <div class="alert alert-warning mb-0">
                                <div class="fw-semibold mb-2" style="font-size:.85rem;">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    {{ $h['gagal'] }} baris tidak masuk. Perbaiki di Excel lalu import ulang (baris yang sudah berhasil aman, tidak akan dobel).
                                </div>
                                <ul class="galat-list mb-0 ps-3">
                                    @foreach ($h['galat'] as $pesan)
                                        <li>{{ $pesan }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <div class="alert alert-success mb-0" style="font-size:.85rem;">
                                <i class="fas fa-check-circle me-1"></i> Semua baris berhasil diproses.
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <div class="row g-4">
                {{-- Kolom kiri: upload --}}
                <div class="col-lg-6">
                    <form action="{{ route('admin.kosakata.import.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                        @csrf

                        <div class="card form-card mb-4">
                            <div class="card-body">
                                <div class="section-divider"><i class="fas fa-upload me-2"></i>Unggah File</div>

                                <div class="mb-3">
                                    <label for="file" class="form-label">File Excel <span class="required-mark">*</span></label>
                                    <input type="file" id="file" name="file" accept=".xlsx,.xls"
                                        class="form-control @error('file') is-invalid @enderror" required>
                                    @error('file')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Format .xlsx atau .xls, maksimal 5 MB.</div>
                                </div>

                                <label class="form-label">Kalau data sudah ada di sistem</label>
                                <div class="mode-option form-check ps-5">
                                    <input class="form-check-input" type="radio" name="mode" id="mode-perbarui" value="perbarui"
                                        {{ old('mode', 'perbarui') === 'perbarui' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="mode-perbarui">
                                        Perbarui dengan isi Excel
                                        <small>Sel yang dikosongkan di Excel tidak menghapus data lama. Relasi dan anggota grup hanya ditambah, tidak pernah diubah.</small>
                                    </label>
                                </div>
                                <div class="mode-option form-check ps-5">
                                    <input class="form-check-input" type="radio" name="mode" id="mode-lewati" value="lewati"
                                        {{ old('mode') === 'lewati' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="mode-lewati">
                                        Biarkan, hanya tambah yang baru
                                        <small>Data yang sudah ada tidak diubah.</small>
                                    </label>
                                </div>
                                @error('mode')
                                    <div class="text-danger mt-1" style="font-size:.8rem;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-file-import me-2"></i> Import Sekarang
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Kolom kanan: petunjuk --}}
                <div class="col-lg-6">
                    <div class="card form-card mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                <div class="section-divider mb-0"><i class="fas fa-lightbulb me-2"></i>Format File</div>
                                <a href="{{ route('admin.kosakata.template') }}" class="btn btn-excel btn-sm">
                                    <i class="fas fa-download me-1"></i> Unduh Template
                                </a>
                            </div>

                            <p class="text-muted" style="font-size:.83rem;">
                                File boleh berisi sampai enam sheet. Hanya sheet <code>Kosakata</code> yang utama, sheet lain opsional
                                (sheet yang tidak ada di file dilewati). Nama sheet dan judul kolom (baris 1) harus persis seperti di bawah,
                                paling mudah dengan mengisi template.
                            </p>

                            <div class="fw-semibold mb-1" style="font-size:.85rem;">Sheet <code>Kosakata</code> - satu baris satu kosakata</div>
                            <div class="table-responsive mb-3">
                                <table class="table mb-0">
                                    <thead><tr><th>Kolom</th><th>Wajib</th><th>Keterangan</th></tr></thead>
                                    <tbody>
                                        <tr><td><code>hanzi</code></td><td>Ya</td><td>Maks. 20 karakter</td></tr>
                                        <tr><td><code>pinyin</code></td><td>Ya</td><td>Maks. 60 karakter. Hanzi + pinyin harus unik (huruf besar/kecil dan tanda nada dibedakan)</td></tr>
                                        <tr><td><code>zhuyin</code></td><td>-</td><td>Bopomofo, mis. ㄋㄧˇ ㄏㄠˇ</td></tr>
                                        <tr><td><code>arti_indonesia</code></td><td>-</td><td>Boleh dikosongkan dulu dan diisi belakangan</td></tr>
                                        <tr><td><code>arti_singkat</code></td><td>-</td><td>Teks pilihan jawaban di soal (maks. 100 karakter)</td></tr>
                                        <tr><td><code>baca_indonesia</code></td><td>-</td><td></td></tr>
                                        <tr><td><code>english</code></td><td>-</td><td></td></tr>
                                        <tr><td><code>kegunaan</code></td><td>-</td><td>Penjelasan kegunaan kata (maks. 2000 karakter)</td></tr>
                                        <tr><td><code>kata_pengait</code></td><td>-</td><td>Pengait ingatan untuk membantu menghafal (maks. 1000 karakter)</td></tr>
                                        <tr><td><code>jenis_kata</code></td><td>-</td><td>{{ implode(', ', $jenisKata) }}</td></tr>
                                        <tr><td><code>sub_jenis</code></td><td>-</td><td>{{ implode(', ', $subJenis) }}</td></tr>
                                        <tr><td><code>kategori</code></td><td>-</td><td>Nama kategori; dibuat otomatis kalau belum ada</td></tr>
                                        <tr><td><code>level_tocfl</code></td><td>-</td><td>Angka tingkat @if ($levels->isNotEmpty())({{ $levels->pluck('tingkat')->implode(', ') }})@endif</td></tr>
                                        <tr><td><code>urutan</code></td><td>-</td><td>Kosong = otomatis di akhir</td></tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="fw-semibold mb-1" style="font-size:.85rem;">Sheet <code>Contoh Kalimat</code> - satu baris satu kalimat</div>
                            <div class="table-responsive mb-3">
                                <table class="table mb-0">
                                    <thead><tr><th>Kolom</th><th>Wajib</th><th>Keterangan</th></tr></thead>
                                    <tbody>
                                        <tr><td><code>kosakata_hanzi</code></td><td>Ya</td><td>Harus sama persis dengan kosakatanya</td></tr>
                                        <tr><td><code>kosakata_pinyin</code></td><td>Ya</td><td></td></tr>
                                        <tr><td><code>kalimat_hanzi</code></td><td>Ya</td><td></td></tr>
                                        <tr><td><code>kalimat_pinyin</code></td><td>Ya</td><td></td></tr>
                                        <tr><td><code>kalimat_arti</code></td><td>Ya</td><td></td></tr>
                                        <tr><td><code>catatan_tata_bahasa</code></td><td>-</td><td></td></tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="fw-semibold mb-1" style="font-size:.85rem;">Sheet <code>Karakter</code> - satu baris satu karakter (hanzi + pinyin)</div>
                            <div class="table-responsive mb-3">
                                <table class="table mb-0">
                                    <thead><tr><th>Kolom</th><th>Wajib</th><th>Keterangan</th></tr></thead>
                                    <tbody>
                                        <tr><td><code>hanzi</code></td><td>Ya</td><td>Satu karakter (maks. 8)</td></tr>
                                        <tr><td><code>pinyin</code></td><td>Ya</td><td>Hanzi + pinyin harus unik. Karakter berbunyi ganda dibuat jadi beberapa baris</td></tr>
                                        <tr><td><code>baca_indonesia</code></td><td>-</td><td></td></tr>
                                        <tr><td><code>arti_indonesia</code></td><td>Ya</td><td>Arti kamus karakter</td></tr>
                                        <tr><td><code>english</code></td><td>-</td><td></td></tr>
                                        <tr><td><code>level_tocfl</code></td><td>-</td><td>Angka 1 sampai 6</td></tr>
                                        <tr><td><code>urutan</code></td><td>-</td><td>Angka, kosong = 0</td></tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="fw-semibold mb-1" style="font-size:.85rem;">Sheet <code>Pecahan Karakter</code> - satu baris satu karakter di dalam kata</div>
                            <div class="table-responsive mb-3">
                                <table class="table mb-0">
                                    <thead><tr><th>Kolom</th><th>Wajib</th><th>Keterangan</th></tr></thead>
                                    <tbody>
                                        <tr><td><code>kosakata_hanzi</code></td><td>Ya</td><td>Harus sama persis dengan kosakatanya</td></tr>
                                        <tr><td><code>kosakata_pinyin</code></td><td>Ya</td><td></td></tr>
                                        <tr><td><code>urutan</code></td><td>Ya</td><td>Posisi karakter di dalam kata, mulai dari 1 (kiri ke kanan)</td></tr>
                                        <tr><td><code>karakter_hanzi</code></td><td>Ya</td><td>Karakter harus sudah ada di data karakter, atau diisi lewat sheet <code>Karakter</code></td></tr>
                                        <tr><td><code>karakter_pinyin</code></td><td>Ya</td><td>Sama persis dengan pinyin karakter di data karakter</td></tr>
                                        <tr><td><code>pinyin_di_kata</code></td><td>-</td><td>Isi kalau bacaan di kata ini berbeda dari kamus</td></tr>
                                        <tr><td><code>arti_di_kata</code></td><td>-</td><td>Isi kalau arti di kata ini berbeda dari kamus</td></tr>
                                        <tr><td><code>penjelasan</code></td><td>-</td><td></td></tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="fw-semibold mb-1" style="font-size:.85rem;">Sheet <code>Relasi</code> - satu baris satu pasang kata</div>
                            <div class="table-responsive mb-3">
                                <table class="table mb-0">
                                    <thead><tr><th>Kolom</th><th>Wajib</th><th>Keterangan</th></tr></thead>
                                    <tbody>
                                        <tr><td><code>kosakata_hanzi</code></td><td>Ya</td><td>Kedua kata harus sudah ada (di sistem atau di sheet Kosakata)</td></tr>
                                        <tr><td><code>kosakata_pinyin</code></td><td>Ya</td><td></td></tr>
                                        <tr><td><code>terkait_hanzi</code></td><td>Ya</td><td></td></tr>
                                        <tr><td><code>terkait_pinyin</code></td><td>Ya</td><td></td></tr>
                                        <tr><td><code>jenis</code></td><td>Ya</td><td>{{ implode(', ', $jenisRelasi) }}. Relasi dibaca dua arah, jadi cukup dicatat sekali</td></tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="fw-semibold mb-1" style="font-size:.85rem;">Sheet <code>Grup Kosakata</code> - satu baris satu anggota grup</div>
                            <div class="table-responsive mb-3">
                                <table class="table mb-0">
                                    <thead><tr><th>Kolom</th><th>Wajib</th><th>Keterangan</th></tr></thead>
                                    <tbody>
                                        <tr><td><code>grup_nama</code></td><td>Ya</td><td>Grup dibuat otomatis kalau namanya belum ada</td></tr>
                                        <tr><td><code>grup_keterangan</code></td><td>-</td><td>Dipakai hanya saat grup baru dibuat</td></tr>
                                        <tr><td><code>kosakata_hanzi</code></td><td>Ya</td><td></td></tr>
                                        <tr><td><code>kosakata_pinyin</code></td><td>Ya</td><td></td></tr>
                                    </tbody>
                                </table>
                            </div>

                            <p class="text-muted mb-0" style="font-size:.8rem;">
                                Satu kosakata boleh punya banyak baris contoh kalimat. Hapus baris contoh dari template sebelum mengisi data sendiri.
                                Urutan sheet di file tidak berpengaruh: kosakata selalu diproses lebih dulu.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- end .page-inner --}}
    </div>{{-- end .container --}}
@endsection
