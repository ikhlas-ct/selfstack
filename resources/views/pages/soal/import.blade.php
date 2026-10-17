@extends('layouts.user.user')

@section('title', 'Import Soal')

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
    .form-control, .form-select { border-radius: 10px; border: 1.5px solid #e2e8f0; font-size: .83rem; padding: 7px 12px; color: #334155; background-color: #f8fafc; }
    .form-control:focus, .form-select:focus { border-color: #1a73e8; background-color: #fff; box-shadow: 0 0 0 3px rgba(26,115,232,.12); }

    .mode-option { border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 12px 14px; background: #fafbfc; }
    .mode-option + .mode-option { margin-top: 8px; }
    .mode-option .form-check-label { font-size: .85rem; font-weight: 600; color: #1e293b; }
    .mode-option small { display: block; color: #64748b; font-size: .78rem; margin-top: 2px; }
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
    @php
        $kolomSoal = [
            ['paket', 'Ya*', 'Judul paket (sama dengan sheet Paket atau paket yang sudah ada). *Boleh kosong kalau paket tujuan dipilih di form ini'],
            ['bagian', 'Ya', implode(', ', array_keys($bagian))],
            ['tipe', '-', implode(', ', array_keys($tipe))],
            ['tingkat', '-', 'Angka 1 sampai 6. Kosong = ikut level paket, atau 1'],
            ['poin', '-', 'Kosong = 1'],
            ['urutan', '-', 'Kosong = otomatis di akhir paket'],
            ['kode_bacaan', '-', 'Kode dari sheet Bacaan. Beberapa soal boleh memakai kode yang sama'],
            ['paragraf', '-', 'Teks pendek khusus untuk soal ini (alternatif kode_bacaan)'],
            ['audio', '-', 'Path atau URL file audio (soal Listening)'],
            ['pertanyaan', 'Ya', ''],
            ['pilihan_a … pilihan_d', 'Ya', 'Keempat pilihan harus berbeda'],
            ['jawaban_benar', 'Ya', 'A, B, C, atau D'],
            ['penjelasan', '-', 'Ditampilkan setelah soal dijawab'],
            ['kosakata_hanzi, kosakata_pinyin', '-', 'Wajib untuk tipe kosakata. Pinyin boleh kosong kalau hanzi-nya hanya punya satu bacaan'],
            ['karakter_hanzi, karakter_pinyin', '-', 'Wajib untuk tipe karakter (kecuali radikal_ke_arti)'],
            ['radikal_hanzi', '-', 'Wajib untuk tipe radikal_ke_arti'],
            ['pola_tata_bahasa', '-', 'Judul pola tata bahasa, harus sama persis dengan yang ada di sistem'],
            ['contoh_kalimat_hanzi', '-', 'Hanzi kalimat contoh yang sudah ada (untuk soal tata bahasa)'],
        ];
        $kolomPaket = [
            ['judul', 'Ya', 'Paket dibuat kalau judulnya belum ada; kalau sudah ada, dipakai apa adanya'],
            ['deskripsi', '-', ''],
            ['jenis', '-', implode(', ', $jenisPaket) . '. Kosong = latihan'],
            ['level_tocfl', '-', 'Angka tingkat' . ($levels->isNotEmpty() ? ' (' . $levels->pluck('tingkat')->implode(', ') . ')' : '')],
            ['grup_kosakata', '-', 'Nama grup kosakata yang sudah ada'],
            ['durasi_menit, maks_percobaan', '-', 'Kosong = tanpa batas'],
            ['nilai_lulus', '-', '0-100, hanya berlaku untuk jenis ujian'],
        ];
        $kolomBacaan = [
            ['kode', 'Ya', 'Kode bebas (mis. B1) yang dipakai kolom kode_bacaan di sheet Soal. Tidak boleh kembar'],
            ['judul, hanzi, pinyin, arti_indonesia', 'Ya', 'Isi teks bacaan'],
            ['penjelasan_tata_bahasa, level_tocfl', '-', ''],
        ];
    @endphp

    <div class="container">

        {{-- Header – di LUAR page-inner --}}
        <div class="ph-card import-page">
            <div class="ph-left">
                <div class="ph-icon import"><i class="fas fa-file-excel"></i></div>
                <div>
                    <h5 class="ph-title">Import Soal dari Excel</h5>
                    <ol class="ph-breadcrumb" aria-label="breadcrumb">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><a href="{{ route('admin.grup-soal.index') }}">Paket Soal</a></li>
                        <li><span class="bc-active">Import Excel</span></li>
                    </ol>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ $paketDipilih ? route('admin.grup-soal.show', $paketDipilih) : route('admin.grup-soal.index') }}" class="btn btn-outline-secondary btn-sm">
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
                @php
                    $h = session('hasil');
                    $kotak = [
                        ['soal_baru', 'Soal baru', 'text-success'],
                        ['soal_dilewati', 'Soal dilewati (sudah ada)', ''],
                        ['paket_baru', 'Paket baru', 'text-success'],
                        ['paket_dipakai', 'Paket sudah ada', ''],
                        ['bacaan_baru', 'Bacaan baru', 'text-success'],
                        ['bacaan_dipakai', 'Bacaan sudah ada', ''],
                    ];
                @endphp
                <div class="card form-card mb-4">
                    <div class="card-body">
                        <div class="section-divider"><i class="fas fa-clipboard-check me-2"></i>Hasil Import</div>

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

                        @if (! empty($h['paket_terpengaruh']))
                            <div class="mb-3">
                                <div class="fw-semibold mb-2" style="font-size:.85rem;">Periksa dan setujui soal barunya di:</div>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ($h['paket_terpengaruh'] as $pk)
                                        <a href="{{ route('admin.grup-soal.show', $pk['id']) }}" class="btn btn-excel btn-sm">
                                            <i class="fas fa-clipboard-list me-1"></i>{{ \Illuminate\Support\Str::limit($pk['judul'], 40) }} (+{{ $pk['jumlah'] }})
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif

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
                <div class="col-lg-5">
                    <form action="{{ route('admin.soal.import.store') }}" method="POST" enctype="multipart/form-data" novalidate>
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

                                <div class="mb-3">
                                    <label for="paket_id" class="form-label">Paket tujuan</label>
                                    <select id="paket_id" name="paket_id" class="form-select @error('paket_id') is-invalid @enderror">
                                        <option value="">— Ikuti kolom "paket" di Excel —</option>
                                        @foreach ($pakets as $pk)
                                            <option value="{{ $pk->id }}" @selected((int) old('paket_id', $paketDipilih) === $pk->id)>
                                                {{ \Illuminate\Support\Str::limit($pk->judul, 60) }} ({{ $pk->jenis }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('paket_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Kalau dipilih, semua soal di file masuk ke paket ini dan sheet Paket diabaikan.</div>
                                </div>

                                <label class="form-label">Status awal soal</label>
                                <div class="mode-option form-check ps-5">
                                    <input class="form-check-input" type="radio" name="status_awal" id="st-cek" value="perlu_dicek"
                                        {{ old('status_awal', 'perlu_dicek') === 'perlu_dicek' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="st-cek">
                                        Perlu dicek (disarankan)
                                        <small>Periksa dulu lalu setujui di halaman detail paket. Berguna untuk soal buatan AI.</small>
                                    </label>
                                </div>
                                <div class="mode-option form-check ps-5">
                                    <input class="form-check-input" type="radio" name="status_awal" id="st-ok" value="disetujui"
                                        {{ old('status_awal') === 'disetujui' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="st-ok">
                                        Langsung disetujui
                                        <small>Soal langsung bisa dikerjakan.</small>
                                    </label>
                                </div>
                                @error('status_awal')
                                    <div class="text-danger mt-1" style="font-size:.8rem;">{{ $message }}</div>
                                @enderror

                                <label class="form-label mt-3">Asal soal</label>
                                <div class="mode-option form-check ps-5">
                                    <input class="form-check-input" type="radio" name="sumber" id="sm-ai" value="ai"
                                        {{ old('sumber', 'ai') === 'ai' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sm-ai">
                                        AI
                                        <small>Soal dibuat oleh AI.</small>
                                    </label>
                                </div>
                                <div class="mode-option form-check ps-5">
                                    <input class="form-check-input" type="radio" name="sumber" id="sm-manual" value="manual"
                                        {{ old('sumber') === 'manual' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sm-manual">
                                        Manual
                                        <small>Soal ditulis sendiri.</small>
                                    </label>
                                </div>
                                @error('sumber')
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
                <div class="col-lg-7">
                    <div class="card form-card mb-4">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                <div class="section-divider mb-0"><i class="fas fa-lightbulb me-2"></i>Format File</div>
                                <a href="{{ route('admin.soal.template') }}" class="btn btn-excel btn-sm">
                                    <i class="fas fa-download me-1"></i> Unduh Template
                                </a>
                            </div>

                            <p class="text-muted" style="font-size:.83rem;">
                                File boleh berisi tiga sheet: <code>Soal</code> (utama), <code>Paket</code>, dan <code>Bacaan</code> (opsional).
                                Nama sheet dan judul kolom (baris 1) harus persis seperti di bawah, paling mudah dengan mengisi template.
                                Baris yang gagal dilaporkan dan baris lain tetap masuk. Soal yang persis sama di paket yang sama dilewati.
                            </p>

                            @foreach ([['Soal', 'satu baris satu soal', $kolomSoal], ['Paket', 'satu baris satu paket', $kolomPaket], ['Bacaan', 'satu baris satu bacaan', $kolomBacaan]] as [$sheet, $ket, $daftar])
                                <div class="fw-semibold mb-1" style="font-size:.85rem;">Sheet <code>{{ $sheet }}</code> - {{ $ket }}</div>
                                <div class="table-responsive mb-3">
                                    <table class="table mb-0">
                                        <thead><tr><th>Kolom</th><th>Wajib</th><th>Keterangan</th></tr></thead>
                                        <tbody>
                                            @foreach ($daftar as [$kolom, $wajib, $keterangan])
                                                <tr><td><code>{{ $kolom }}</code></td><td>{{ $wajib }}</td><td>{{ $keterangan }}</td></tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endforeach

                            <p class="text-muted mb-0" style="font-size:.8rem;">
                                Hapus baris contoh dari template sebelum mengisi data sendiri. Urutan sheet tidak berpengaruh:
                                Paket diproses dulu, lalu Bacaan, lalu Soal. Satu bacaan boleh dipakai banyak soal lewat <code>kode_bacaan</code> yang sama.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- end .page-inner --}}
    </div>{{-- end .container --}}
@endsection
