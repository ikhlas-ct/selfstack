@extends('layouts.user.user')

@section('title', 'Detail Grup Kosakata')

@section('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    body, .card, .table, .btn, h4, h5 { font-family: 'Plus Jakarta Sans', sans-serif; }

    /* ===== PAGE HEADER ===== */
    .ph-card { background: #fff; border: 1px solid #e9ecef; border-radius: 14px; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; margin-bottom: 1.25rem; position: relative; overflow: hidden; box-shadow: 0 1px 6px rgba(0,0,0,.05); }
    .ph-card::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; border-radius: 14px 0 0 14px; }
    .ph-card.show-page::before { background: #0369a1; }
    .ph-left { display: flex; align-items: center; gap: 12px; }
    .ph-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
    .ph-icon.show { background: #e0f2fe; color: #0369a1; }
    .ph-title { font-size: 1.05rem; font-weight: 700; color: #1e293b; letter-spacing: -.2px; line-height: 1.2; margin: 0; }
    .ph-breadcrumb { display: flex; align-items: center; gap: 4px; flex-wrap: wrap; margin-top: 4px; list-style: none; padding: 0; margin-bottom: 0; }
    .ph-breadcrumb li { display: flex; align-items: center; }
    .ph-breadcrumb li + li::before { content: '›'; color: #cbd5e1; font-size: .7rem; margin: 0 4px; }
    .ph-breadcrumb a { font-size: .75rem; color: #1a73e8; text-decoration: none; }
    .ph-breadcrumb a:hover { text-decoration: underline; }
    .ph-breadcrumb .bc-active { font-size: .75rem; color: #94a3b8; }

    /* ===== CARD ===== */
    .form-card { border: none; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,.07); }
    .section-divider { background: #f8f9fa; border-left: 4px solid #1269db; padding: 8px 14px; border-radius: 0 6px 6px 0; font-weight: 600; font-size: .9rem; color: #1269db; margin-bottom: 1rem; }
    .filter-card { border: none; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,.07); overflow: hidden; }
    .filter-card .card-header { background: #fff; border-bottom: 1px solid #f1f5f9; padding: 18px 24px; }
    .filter-card .card-header h5 { font-size: .95rem; font-weight: 700; color: #1e293b; }

    /* ===== INFO ITEM ===== */
    .info-item { padding: 10px 0; border-bottom: 1px solid #f1f5f9; }
    .info-item:last-child { border-bottom: none; padding-bottom: 0; }
    .info-label { font-size: .72rem; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: #94a3b8; }
    .info-value { font-size: .88rem; font-weight: 500; color: #1e293b; margin-top: 2px; word-break: break-word; }

    /* ===== FORM ===== */
    label { font-size: .875rem; font-weight: 500; }
    .form-control, .form-select { border-radius: 10px; border: 1.5px solid #e2e8f0; font-size: .83rem; padding: 7px 12px; color: #334155; background-color: #f8fafc; transition: border-color .2s, box-shadow .2s; }
    .form-control:focus, .form-select:focus { border-color: #1a73e8; background: #fff; box-shadow: 0 0 0 3px rgba(26,115,232,.12); }
    .form-control::placeholder { color: #94a3b8; }
    .input-group .input-group-text { background: #f8fafc; border: 1.5px solid #e2e8f0; border-right: none; border-radius: 10px 0 0 10px; color: #94a3b8; font-size: .8rem; }
    .input-group .form-control { border-left: none; border-radius: 0 10px 10px 0; }
    .input-pelajaran { width: 110px; text-align: center; font-weight: 700; font-size: .95rem; }

    /* ===== TABLE ===== */
    .table thead th { background: #f8fafc; color: #64748b; font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; padding: 12px 16px; border-bottom: 2px solid #e2e8f0; border-top: none; white-space: nowrap; }
    .table tbody td { padding: 11px 16px; vertical-align: middle; font-size: .85rem; color: #334155; border-bottom: 1px solid #f1f5f9; }
    .table tbody tr:last-child td { border-bottom: none; }
    .table-hover tbody tr:hover td { background: #f8fafc; }
    .hanzi-text { font-size: 1.2rem; font-weight: 600; color: #1e293b; font-family: 'Noto Sans TC', 'PingFang TC', 'Microsoft JhengHei', 'Plus Jakarta Sans', sans-serif; }
    tr.baris-pelajaran td { background: #f1f5f9 !important; font-size: .75rem; font-weight: 700; color: #475569; text-transform: uppercase; letter-spacing: .5px; padding: 7px 16px; }

    /* ===== BADGES ===== */
    .badge { font-size: .7rem; font-weight: 600; padding: 4px 9px; border-radius: 6px; letter-spacing: .2px; }
    .badge-aktif    { background: #dcfce7; color: #15803d; }
    .badge-nonaktif { background: #f1f5f9; color: #64748b; }
    .badge-level    { background: #e8f0fe; color: #1a73e8; }
    .badge-pelajaran { background: #fff7ed; color: #c2410c; }

    /* ===== TAB PELAJARAN ===== */
    .tab-pel { display: flex; flex-wrap: wrap; gap: 6px; padding: 14px 24px; background: #fafbfc; border-bottom: 1px solid #f1f5f9; }
    .tab-pel a { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 20px; font-size: .78rem; font-weight: 600; color: #64748b; background: #fff; border: 1.5px solid #e2e8f0; text-decoration: none; transition: all .15s; }
    .tab-pel a:hover { border-color: #1a73e8; color: #1a73e8; }
    .tab-pel a.aktif { background: #1a73e8; border-color: #1a73e8; color: #fff; }
    .tab-pel a .n { font-size: .68rem; font-weight: 700; padding: 1px 7px; border-radius: 10px; background: #f1f5f9; color: #64748b; }
    .tab-pel a.aktif .n { background: rgba(255,255,255,.25); color: #fff; }

    /* ===== BAR AKSI TERPILIH ===== */
    .bar-pilih { display: none; align-items: center; gap: 10px; flex-wrap: wrap; padding: 10px 24px; background: #e8f0fe; border-bottom: 1px solid #dbeafe; font-size: .82rem; }
    .bar-pilih.tampil { display: flex; }

    /* ===== HASIL CARI ===== */
    .hasil-cari { margin-top: 12px; border: 1.5px solid #e2e8f0; border-radius: 12px; overflow: hidden; max-height: 340px; overflow-y: auto; }
    .hasil-cari:empty { display: none; }
    .hasil-item { display: flex; align-items: center; gap: 12px; padding: 9px 14px; border-bottom: 1px solid #f1f5f9; background: #fff; }
    .hasil-item:last-child { border-bottom: none; }
    .hasil-item .info { flex: 1; min-width: 0; }
    .hasil-item .arti { font-size: .78rem; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .hasil-kosong { padding: 14px; font-size: .82rem; color: #94a3b8; text-align: center; background: #fff; }

    /* ===== BUTTONS ===== */
    .btn-action { width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; font-size: .75rem; padding: 0; border: none; transition: all .15s ease; }
    .btn-detail { background: #e0f2fe; color: #0369a1; } .btn-detail:hover { background: #0369a1; color: #fff; }
    .btn-hapus  { background: #fee2e2; color: #dc2626; } .btn-hapus:hover  { background: #dc2626; color: #fff; }
    .btn-primary { background: linear-gradient(135deg, #1a73e8, #1558b0); border: none; border-radius: 10px; font-weight: 600; font-size: .83rem; padding: 8px 18px; box-shadow: 0 2px 8px rgba(26,115,232,.35); transition: all .2s ease; }
    .btn-primary:hover { background: linear-gradient(135deg, #1558b0, #0f3e82); box-shadow: 0 4px 14px rgba(26,115,232,.45); transform: translateY(-1px); }
    .btn-outline-secondary { border-radius: 10px; font-size: .83rem; border-color: #e2e8f0; color: #64748b; padding: 7px 12px; }
    .btn-outline-secondary:hover { background: #f1f5f9; border-color: #cbd5e1; color: #334155; }
    .btn-outline-danger { border-radius: 10px; font-size: .83rem; padding: 7px 12px; }
    .btn-tambah-kecil { border-radius: 8px; font-size: .75rem; font-weight: 600; padding: 4px 12px; }

    /* ===== NAV TAB MODE TAMBAH ===== */
    .nav-mode { border-bottom: 2px solid #f1f5f9; gap: 4px; margin-bottom: 14px; }
    .nav-mode .nav-link { border: none; border-radius: 8px 8px 0 0; font-size: .82rem; font-weight: 600; color: #64748b; padding: 8px 14px; }
    .nav-mode .nav-link.active { color: #1a73e8; background: #e8f0fe; }

    /* ===== EMPTY STATE ===== */
    .empty-state { padding: 50px 20px; }
    .empty-state-icon { width: 72px; height: 72px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 1.6rem; color: #94a3b8; }
</style>
@endsection

@section('content')
    <div class="container">

        {{-- Header – di LUAR page-inner --}}
        <div class="ph-card show-page">
            <div class="ph-left">
                <div class="ph-icon show"><i class="fas fa-book"></i></div>
                <div>
                    <h5 class="ph-title">{{ $grup->nama }}</h5>
                    <ol class="ph-breadcrumb" aria-label="breadcrumb">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><a href="{{ route('admin.grup-kosakata.index') }}">Grup Kosakata</a></li>
                        <li><span class="bc-active">Detail</span></li>
                    </ol>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.grup-kosakata.edit', $grup) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-pencil-alt me-1"></i> Edit
                </a>
                <a href="{{ route('admin.grup-kosakata.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        <div class="page-inner">

            {{-- Alert dari redirect (mis. setelah grup dibuat) --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible alert-flash fade show mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            {{-- Tempat pesan hasil aksi AJAX (tambah / pindah / keluarkan) --}}
            <div id="area-pesan"></div>

            <div class="row g-4">

                {{-- ===== KIRI: info grup + form tambah ===== --}}
                <div class="col-lg-4">

                    <div class="card form-card mb-4">
                        <div class="card-body">
                            <div class="section-divider"><i class="fas fa-info-circle me-2"></i>Informasi Grup</div>

                            <div class="info-item">
                                <div class="info-label">Nama Grup / Buku</div>
                                <div class="info-value">{{ $grup->nama }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Keterangan</div>
                                <div class="info-value" style="white-space:pre-line;">{{ $grup->keterangan ?: '-' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Paket Soal</div>
                                <div class="info-value">
                                    @if ($jumlahPaketSoal > 0)
                                        {{ $jumlahPaketSoal }} paket memakai grup ini
                                    @else
                                        Belum dipakai paket soal
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card form-card mb-4">
                        <div class="card-body">
                            <div class="section-divider"><i class="fas fa-plus-circle me-2"></i>Tambah Kosakata ke Buku</div>

                            {{-- Nomor pelajaran tujuan, dipakai kedua cara tambah di bawah --}}
                            <div class="d-flex align-items-center gap-3 flex-wrap mb-3">
                                <label for="input-pelajaran" class="mb-0">Masuk ke pelajaran</label>
                                <input type="number" id="input-pelajaran" class="form-control input-pelajaran"
                                    min="1" max="999" value="{{ $pelajaranBawaan }}" placeholder="–">
                            </div>
                            <div class="form-text mb-3" style="margin-top:-6px;">
                                Kosongkan angkanya kalau belum tahu pelajaran keberapa.
                            </div>

                            <ul class="nav nav-mode" role="tablist">
                                <li class="nav-item">
                                    <button type="button" class="nav-link active" data-bs-toggle="tab" data-bs-target="#mode-cari" role="tab">
                                        <i class="fas fa-search me-1"></i> Cari
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button type="button" class="nav-link" data-bs-toggle="tab" data-bs-target="#mode-tempel" role="tab">
                                        <i class="fas fa-paste me-1"></i> Tempel Daftar
                                    </button>
                                </li>
                            </ul>

                            <div class="tab-content">
                                {{-- Cari satu per satu --}}
                                <div class="tab-pane fade show active" id="mode-cari" role="tabpanel">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text" id="input-cari" class="form-control" autocomplete="off"
                                            placeholder="Hanzi / pinyin / arti...">
                                    </div>
                                    <div class="form-text">Hanya kosakata yang sudah ada di data. Yang belum ada, tambahkan dulu di menu Kosakata.</div>
                                    <div id="hasil-cari" class="hasil-cari"></div>
                                </div>

                                {{-- Tempel banyak sekaligus --}}
                                <div class="tab-pane fade" id="mode-tempel" role="tabpanel">
                                    <textarea id="input-daftar" rows="6" class="form-control"
                                        placeholder="老師&#10;學生&#10;朋友&#10;（satu hanzi per baris, atau pisahkan dengan spasi / koma）"></textarea>
                                    <div class="form-text">
                                        Maksimal 300 kata. Hanzi yang belum ada di data atau punya lebih dari satu bacaan akan dilewati dan ditampilkan untuk dicek.
                                    </div>
                                    <button type="button" id="btn-tempel" class="btn btn-primary btn-sm mt-3">
                                        <i class="fas fa-plus me-1"></i> Masukkan ke Buku
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== KANAN: anggota per pelajaran ===== --}}
                <div class="col-lg-8">
                    <div class="card filter-card shadow-sm">
                        <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <h5 class="mb-0">
                                <i class="fas fa-list text-primary me-2 opacity-75"></i>Isi Buku per Pelajaran
                            </h5>
                        </div>

                        <div id="anggota-wrap">
                            @fragment('anggota')
                            {{-- Tab pelajaran --}}
                            <div class="tab-pel">
                                <a href="{{ route('admin.grup-kosakata.show', $grup) }}"
                                    class="{{ $filter === null || $filter === '' ? 'aktif' : '' }}">
                                    Semua <span class="n">{{ $totalKata }}</span>
                                </a>
                                @foreach ($ringkasan as $r)
                                    @php
                                        $nilai = $r->pelajaran === null ? 'kosong' : (string) $r->pelajaran;
                                    @endphp
                                    <a href="{{ route('admin.grup-kosakata.show', [$grup, 'pelajaran' => $nilai]) }}"
                                        data-pelajaran="{{ $r->pelajaran }}"
                                        class="{{ (string) $filter === $nilai ? 'aktif' : '' }}">
                                        {{ $r->pelajaran === null ? 'Tanpa pelajaran' : 'Pelajaran ' . $r->pelajaran }}
                                        <span class="n">{{ $r->jumlah }}</span>
                                    </a>
                                @endforeach
                            </div>

                            {{-- Bar aksi untuk baris yang dicentang --}}
                            <div class="bar-pilih" id="bar-pilih">
                                <strong><span id="jumlah-pilih">0</span> dipilih</strong>
                                <span class="text-muted">|</span>
                                <label for="input-pindah" class="mb-0">Pindahkan ke pelajaran</label>
                                <input type="number" id="input-pindah" class="form-control input-pelajaran" min="1" max="999"
                                    style="width:90px;padding:4px 8px;" placeholder="–">
                                <button type="button" id="btn-pindah" class="btn btn-primary btn-sm">
                                    <i class="fas fa-arrows-alt-h me-1"></i> Pindahkan
                                </button>
                                <button type="button" id="btn-lepas" class="btn btn-outline-danger btn-sm ms-auto">
                                    <i class="fas fa-times me-1"></i> Keluarkan dari Buku
                                </button>
                            </div>

                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table-hover mb-0 table">
                                        <thead>
                                            <tr>
                                                <th width="36"><input type="checkbox" class="form-check-input cek-semua" title="Pilih semua di halaman ini"></th>
                                                <th>Hanzi</th>
                                                <th>Pinyin</th>
                                                <th>Arti</th>
                                                <th>Level</th>
                                                <th>Contoh</th>
                                                <th width="70">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $sebelumnya = '__awal__'; @endphp
                                            @forelse ($anggota as $k)
                                                @php $pel = $k->pivot->pelajaran; @endphp

                                                {{-- Pembatas tiap ganti pelajaran --}}
                                                @if ($pel !== $sebelumnya)
                                                    <tr class="baris-pelajaran">
                                                        <td colspan="7">
                                                            <i class="fas fa-bookmark me-1"></i>
                                                            {{ $pel === null ? 'Tanpa pelajaran' : 'Pelajaran ' . $pel }}
                                                        </td>
                                                    </tr>
                                                    @php $sebelumnya = $pel; @endphp
                                                @endif

                                                <tr>
                                                    <td><input type="checkbox" class="form-check-input cek-baris" value="{{ $k->id }}"></td>
                                                    <td><span class="hanzi-text">{{ $k->hanzi }}</span></td>
                                                    <td>
                                                        {{ $k->pinyin }}
                                                        @if ($k->zhuyin)
                                                            <div class="text-muted" style="font-size:.75rem;">{{ $k->zhuyin }}</div>
                                                        @endif
                                                    </td>
                                                    <td style="max-width:220px;">
                                                        @if ($k->arti_indonesia)
                                                            {{ $k->arti_indonesia }}
                                                        @else
                                                            <span class="text-muted fst-italic">Belum ada arti</span>
                                                        @endif
                                                        @if ($k->english)
                                                            <div class="text-muted" style="font-size:.75rem;">{{ $k->english }}</div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($k->levelTocfl)
                                                            <span class="badge badge-level">{{ $k->levelTocfl->nama }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge {{ $k->contoh_kalimats_count > 0 ? 'badge-aktif' : 'badge-nonaktif' }}">
                                                            {{ $k->contoh_kalimats_count }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex gap-1">
                                                            <a href="{{ route('admin.kosakata.show', $k) }}" class="btn btn-action btn-detail"
                                                                title="Lihat kosakata" target="_blank" rel="noopener">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-action btn-hapus btn-lepas-satu"
                                                                data-id="{{ $k->id }}" data-hanzi="{{ $k->hanzi }}" title="Keluarkan dari buku">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="p-0 text-center">
                                                        <div class="empty-state">
                                                            <div class="empty-state-icon"><i class="fas fa-book-open"></i></div>
                                                            <div class="fw-semibold text-secondary mb-1">
                                                                {{ $totalKata === 0 ? 'Buku ini belum berisi kosakata' : 'Tidak ada kosakata di pelajaran ini' }}
                                                            </div>
                                                            <div class="text-muted" style="font-size:.8rem;">
                                                                Tambahkan lewat kotak di sebelah kiri: cari satu per satu, atau tempel daftar hanzi dari bukumu
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            @if ($anggota->hasPages())
                                <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
                                    <small class="text-muted">
                                        Menampilkan
                                        <strong>{{ $anggota->firstItem() }}</strong>–<strong>{{ $anggota->lastItem() }}</strong>
                                        dari <strong>{{ $anggota->total() }}</strong> kosakata
                                    </small>
                                    {{ $anggota->links() }}
                                </div>
                            @endif
                            @endfragment
                        </div>
                    </div>
                </div>

            </div>{{-- end .row --}}
        </div>{{-- end .page-inner --}}
    </div>{{-- end .container --}}
@endsection

@section('scripts')
    <script>
        (function () {
            const URL_CARI   = @json(route('admin.grup-kosakata.cari', $grup));
            const URL_TAMBAH = @json(route('admin.grup-kosakata.kosakata.tambah', $grup));
            const URL_MASSAL = @json(route('admin.grup-kosakata.kosakata.massal', $grup));
            const URL_PINDAH = @json(route('admin.grup-kosakata.kosakata.pindah', $grup));
            const URL_LEPAS  = @json(route('admin.grup-kosakata.kosakata.lepas', $grup));
            const CSRF       = @json(csrf_token());

            const wrap       = document.getElementById('anggota-wrap');
            const areaPesan  = document.getElementById('area-pesan');
            const inPelajaran = document.getElementById('input-pelajaran');
            const inCari     = document.getElementById('input-cari');
            const hasilCari  = document.getElementById('hasil-cari');
            const inDaftar   = document.getElementById('input-daftar');

            /* ------------------------------------------------------------ util */
            const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
            const angka = v => (v === '' || v === null || v === undefined) ? null : parseInt(v, 10);

            // Kirim JSON ke server; lempar Error dengan pesan yang bisa ditampilkan.
            async function kirim(url, method, body) {
                const r = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify(body),
                });
                const j = await r.json().catch(() => ({}));
                if (!r.ok) {
                    const galat = j.errors ? Object.values(j.errors)[0][0] : null;
                    throw new Error(galat || j.message || 'Terjadi kesalahan (' + r.status + ').');
                }
                return j;
            }

            let timerPesan = null;
            function tampilPesan(html, jenis = 'success', otomatis = true) {
                const ikon = jenis === 'success' ? 'check-circle' : (jenis === 'warning' ? 'exclamation-triangle' : 'exclamation-circle');
                areaPesan.innerHTML =
                    `<div class="alert alert-${jenis} alert-dismissible fade show mb-4" role="alert">
                        <i class="fas fa-${ikon} me-2"></i>${html}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                    </div>`;
                clearTimeout(timerPesan);
                if (otomatis) timerPesan = setTimeout(() => { areaPesan.innerHTML = ''; }, 4000);
            }

            /* ------------------------------------------- muat ulang daftar anggota */
            let kontrol = null;
            function muat(url, push = true) {
                if (kontrol) kontrol.abort();
                kontrol = new AbortController();
                wrap.style.opacity = '.5';

                return fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, signal: kontrol.signal })
                    .then(r => { if (!r.ok) throw new Error(r.status); return r.text(); })
                    .then(html => {
                        wrap.innerHTML = html;
                        wrap.style.opacity = '1';
                        if (push) history.pushState(null, '', url);
                        perbaruiBar();
                    })
                    .catch(e => { if (e.name !== 'AbortError') wrap.style.opacity = '1'; });
            }

            // Tab pelajaran & pagination tanpa reload halaman.
            wrap.addEventListener('click', e => {
                const tab = e.target.closest('.tab-pel a');
                const hal = e.target.closest('.pagination a');
                const a = tab || hal;
                if (!a) return;
                e.preventDefault();

                // Buka tab pelajaran N -> kotak "masuk ke pelajaran" ikut N.
                if (tab && tab.dataset.pelajaran) inPelajaran.value = tab.dataset.pelajaran;

                muat(a.href);
            });
            window.addEventListener('popstate', () => muat(location.href, false));

            const muatSaatIni = () => muat(location.href, false);

            /* --------------------------------------------------- pilih baris */
            function terpilih() {
                return [...wrap.querySelectorAll('.cek-baris:checked')].map(c => parseInt(c.value, 10));
            }
            function perbaruiBar() {
                const bar = document.getElementById('bar-pilih');
                if (!bar) return;
                const n = terpilih().length;
                document.getElementById('jumlah-pilih').textContent = n;
                bar.classList.toggle('tampil', n > 0);
            }
            wrap.addEventListener('change', e => {
                if (e.target.classList.contains('cek-semua')) {
                    wrap.querySelectorAll('.cek-baris').forEach(c => { c.checked = e.target.checked; });
                }
                if (e.target.classList.contains('cek-baris') || e.target.classList.contains('cek-semua')) perbaruiBar();
            });

            /* --------------------------------------- pindah & keluarkan anggota */
            wrap.addEventListener('click', async e => {
                const btnPindah = e.target.closest('#btn-pindah');
                const btnLepas  = e.target.closest('#btn-lepas');
                const btnSatu   = e.target.closest('.btn-lepas-satu');

                try {
                    if (btnPindah) {
                        const ids = terpilih();
                        if (!ids.length) return;
                        const tujuan = angka(document.getElementById('input-pindah').value);
                        const j = await kirim(URL_PINDAH, 'PATCH', { kosakata_ids: ids, pelajaran: tujuan });
                        tampilPesan(esc(j.pesan));
                        await muatSaatIni();
                    }

                    if (btnLepas) {
                        const ids = terpilih();
                        if (!ids.length) return;
                        konfirmasiLepas(ids, `${ids.length} kosakata`);
                    }

                    if (btnSatu) {
                        konfirmasiLepas([parseInt(btnSatu.dataset.id, 10)], `"${btnSatu.dataset.hanzi}"`);
                    }
                } catch (err) {
                    tampilPesan(esc(err.message), 'danger', false);
                }
            });

            function konfirmasiLepas(ids, label) {
                swal({
                    title: 'Keluarkan dari buku?',
                    text: `${label} akan dikeluarkan dari buku ini. Kosakatanya sendiri tidak dihapus.`,
                    icon: 'warning',
                    buttons: { cancel: 'Batal', confirm: { text: 'Ya, Keluarkan', className: 'btn-danger' } },
                    dangerMode: true,
                }).then(async ok => {
                    if (!ok) return;
                    try {
                        const j = await kirim(URL_LEPAS, 'DELETE', { kosakata_ids: ids });
                        tampilPesan(esc(j.pesan));
                        await muatSaatIni();
                        if (inCari.value.trim()) cari();   // tandai ulang hasil cari
                    } catch (err) {
                        tampilPesan(esc(err.message), 'danger', false);
                    }
                });
            }

            /* ---------------------------------------------------- cari & tambah */
            let timerCari = null;
            let kontrolCari = null;
            let hasilTerakhir = [];

            function gambarHasil() {
                if (!inCari.value.trim()) { hasilCari.innerHTML = ''; return; }
                if (!hasilTerakhir.length) {
                    hasilCari.innerHTML = '<div class="hasil-kosong">Tidak ada kosakata yang cocok.</div>';
                    return;
                }
                hasilCari.innerHTML = hasilTerakhir.map(h => `
                    <div class="hasil-item">
                        <span class="hanzi-text">${esc(h.hanzi)}</span>
                        <div class="info">
                            <div>${esc(h.pinyin)} ${h.level ? `<span class="badge badge-level ms-1">${esc(h.level)}</span>` : ''}</div>
                            <div class="arti">${esc(h.arti || '—')}</div>
                        </div>
                        ${h.sudah
                            ? `<span class="badge ${h.pelajaran ? 'badge-pelajaran' : 'badge-nonaktif'}">
                                   ${h.pelajaran ? 'Sudah di pelajaran ' + esc(h.pelajaran) : 'Sudah di buku'}
                               </span>`
                            : `<button type="button" class="btn btn-primary btn-tambah-kecil" data-id="${h.id}">
                                   <i class="fas fa-plus me-1"></i>Tambah
                               </button>`}
                    </div>`).join('');
            }

            async function cari() {
                const q = inCari.value.trim();
                if (!q) { hasilTerakhir = []; gambarHasil(); return; }

                if (kontrolCari) kontrolCari.abort();
                kontrolCari = new AbortController();
                try {
                    const r = await fetch(URL_CARI + '?q=' + encodeURIComponent(q), {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                        signal: kontrolCari.signal,
                    });
                    if (!r.ok) throw new Error(r.status);
                    hasilTerakhir = await r.json();
                    gambarHasil();
                } catch (e) {
                    if (e.name !== 'AbortError') hasilCari.innerHTML = '<div class="hasil-kosong">Gagal mencari, coba lagi.</div>';
                }
            }

            inCari.addEventListener('input', () => { clearTimeout(timerCari); timerCari = setTimeout(cari, 300); });

            hasilCari.addEventListener('click', async e => {
                const btn = e.target.closest('.btn-tambah-kecil');
                if (!btn) return;

                const id = parseInt(btn.dataset.id, 10);
                const pel = angka(inPelajaran.value);
                btn.disabled = true;

                try {
                    const j = await kirim(URL_TAMBAH, 'POST', { kosakata_ids: [id], pelajaran: pel });
                    tampilPesan(esc(j.pesan));

                    // Tandai di hasil cari tanpa mencari ulang.
                    const h = hasilTerakhir.find(x => x.id === id);
                    if (h) { h.sudah = true; h.pelajaran = pel; }
                    gambarHasil();

                    await muatSaatIni();
                } catch (err) {
                    btn.disabled = false;
                    tampilPesan(esc(err.message), 'danger', false);
                }
            });

            /* ------------------------------------------------ tempel daftar hanzi */
            document.getElementById('btn-tempel').addEventListener('click', async function () {
                const daftar = inDaftar.value.trim();
                if (!daftar) { inDaftar.focus(); return; }

                const btn = this;
                btn.disabled = true;

                try {
                    const j = await kirim(URL_MASSAL, 'POST', { daftar, pelajaran: angka(inPelajaran.value) });

                    let html = esc(j.pesan);
                    const catatan = [];
                    if (j.tidak_ada && j.tidak_ada.length) {
                        catatan.push(`<div class="mt-2"><strong>Belum ada di data kosakata (${j.tidak_ada.length}):</strong> ${j.tidak_ada.map(esc).join('、')}</div>`);
                    }
                    if (j.ambigu && j.ambigu.length) {
                        catatan.push(`<div class="mt-2"><strong>Bacaan lebih dari satu, tambahkan lewat Cari (${j.ambigu.length}):</strong> ${j.ambigu.map(esc).join('、')}</div>`);
                    }

                    if (catatan.length) {
                        tampilPesan(html + catatan.join(''), 'warning', false);
                        // Sisakan hanya yang bermasalah supaya bisa diperbaiki / dicari ulang.
                        const sisa = [...(j.tidak_ada || []), ...(j.ambigu || []).map(a => a.split(' (')[0])];
                        inDaftar.value = sisa.join('\n');
                    } else {
                        tampilPesan(html);
                        inDaftar.value = '';
                    }

                    await muatSaatIni();
                } catch (err) {
                    tampilPesan(esc(err.message), 'danger', false);
                } finally {
                    btn.disabled = false;
                }
            });
        })();
    </script>
@endsection
