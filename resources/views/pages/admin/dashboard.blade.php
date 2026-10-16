@extends('layouts.user.user')
@section('title', 'Dashboard Admin')

@section('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');
    *, .card, .table, .btn, h1, h2, h3, h4, h5, h6 { font-family: 'Plus Jakarta Sans', sans-serif; }

    /* ── WELCOME BANNER ── */
    .welcome-banner { background: linear-gradient(135deg, #1269db 0%, #7c3aed 100%); border-radius: 20px; padding: 2rem 2.5rem; position: relative; overflow: hidden; box-shadow: 0 8px 32px rgba(18,105,219,.25); margin-bottom: 1.75rem; }
    .welcome-banner::before { content:''; position:absolute; top:-60px; right:-60px; width:260px; height:260px; border-radius:50%; background:rgba(255,255,255,.07); }
    .welcome-banner::after  { content:''; position:absolute; bottom:-80px; left:-40px; width:300px; height:300px; border-radius:50%; background:rgba(255,255,255,.05); }
    .welcome-banner .z1 { position:relative; z-index:1; }

    /* ── STAT CARDS ── */
    .stat-card { border:none; border-radius:16px; padding:22px; position:relative; overflow:hidden; box-shadow:0 2px 12px rgba(0,0,0,.07); transition:transform .2s, box-shadow .2s; }
    .stat-card:hover { transform:translateY(-3px); box-shadow:0 8px 24px rgba(0,0,0,.12); }
    .stat-card::after { content:''; position:absolute; right:-18px; top:-18px; width:90px; height:90px; border-radius:50%; opacity:.1; }
    .sc-blue   { background:linear-gradient(135deg,#e8f0fe,#dbeafe); } .sc-blue::after   { background:#1a73e8; }
    .sc-green  { background:linear-gradient(135deg,#e6f9f0,#d1fae5); } .sc-green::after  { background:#16a34a; }
    .sc-purple { background:linear-gradient(135deg,#f5f3ff,#ede9fe); } .sc-purple::after { background:#7c3aed; }
    .sc-orange { background:linear-gradient(135deg,#fff7ed,#ffedd5); } .sc-orange::after { background:#ea580c; }
    .sc-yellow { background:linear-gradient(135deg,#fffbeb,#fef9c3); } .sc-yellow::after { background:#ca8a04; }
    .sc-red    { background:linear-gradient(135deg,#fef2f2,#fee2e2); } .sc-red::after    { background:#dc2626; }
    .sc-teal   { background:linear-gradient(135deg,#f0fdfa,#ccfbf1); } .sc-teal::after   { background:#0d9488; }
    .sc-indigo { background:linear-gradient(135deg,#eef2ff,#e0e7ff); } .sc-indigo::after { background:#4f46e5; }

    .stat-icon { width:50px; height:50px; border-radius:13px; display:inline-flex; align-items:center; justify-content:center; font-size:1.25rem; flex-shrink:0; }
    .si-blue   { background:#1a73e8; color:#fff; } .si-green  { background:#16a34a; color:#fff; }
    .si-purple { background:#7c3aed; color:#fff; } .si-orange { background:#ea580c; color:#fff; }
    .si-yellow { background:#ca8a04; color:#fff; } .si-red    { background:#dc2626; color:#fff; }
    .si-teal   { background:#0d9488; color:#fff; } .si-indigo { background:#4f46e5; color:#fff; }

    .stat-value { font-size:1.9rem; font-weight:800; line-height:1; color:#1e293b; }
    .stat-label { font-size:.73rem; font-weight:600; text-transform:uppercase; letter-spacing:.5px; color:#64748b; margin-top:4px; }
    .stat-sub   { font-size:.75rem; color:#94a3b8; margin-top:3px; }

    /* ── SECTION HEADER ── */
    .sec-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.1rem; }
    .sec-title { font-size:.95rem; font-weight:700; color:#1e293b; display:flex; align-items:center; gap:8px; }
    .sec-title-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; }

    /* ── CARDS ── */
    .dash-card { border:none; border-radius:16px; box-shadow:0 2px 12px rgba(0,0,0,.07); overflow:hidden; }
    .dash-card .card-header { background:#fff; border-bottom:1px solid #f1f5f9; padding:16px 22px; }
    .dash-card .card-body { padding:22px; }

    /* ── TABLE ── */
    .dash-table thead th { background:#f8fafc; color:#64748b; font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.6px; padding:11px 16px; border-bottom:2px solid #e2e8f0; border-top:none; white-space:nowrap; }
    .dash-table tbody td { padding:12px 16px; vertical-align:middle; font-size:.84rem; color:#334155; border-bottom:1px solid #f1f5f9; }
    .dash-table tbody tr:last-child td { border-bottom:none; }
    .dash-table tbody tr:hover td { background:#f8fafc; }

    /* ── BADGES ── */
    .badge { font-size:.68rem; font-weight:600; padding:4px 9px; border-radius:6px; letter-spacing:.2px; }
    .bdg-nonaktif { background:#f1f5f9; color:#64748b; }
    .bdg-benar    { background:#dcfce7; color:#15803d; }
    .bdg-salah    { background:#fee2e2; color:#dc2626; }
    .bdg-popup    { background:#f5f3ff; color:#7c3aed; }
    .bdg-soal     { background:#fff7ed; color:#c2410c; }
    .bdg-kuning   { background:#fef9c3; color:#a16207; }

    /* ── LIST ITEMS ── */
    .rank-item { display:flex; align-items:center; gap:14px; padding:12px 0; border-bottom:1px solid #f1f5f9; }
    .rank-item:last-child { border-bottom:none; padding-bottom:0; }
    .rank-no { width:28px; height:28px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:.75rem; font-weight:700; flex-shrink:0; }
    .rank-1 { background:#fef9c3; color:#ca8a04; }
    .rank-2 { background:#f1f5f9; color:#64748b; }
    .rank-3 { background:#fff7ed; color:#c2410c; }
    .rank-n { background:#f8fafc; color:#94a3b8; }

    .vis-item { display:flex; align-items:center; gap:12px; padding:11px 0; border-bottom:1px solid #f1f5f9; }
    .vis-item:last-child { border-bottom:none; }
    .vis-icon { width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:.85rem; flex-shrink:0; background:#fee2e2; color:#dc2626; }

    /* ── TARGET & BAR ── */
    .target-row { padding:11px 0; border-bottom:1px solid #f1f5f9; }
    .target-row:last-child { border-bottom:none; padding-bottom:0; }
    .target-row:first-child { padding-top:0; }
    .bar-slim { height:7px; border-radius:7px; background:#f1f5f9; overflow:hidden; margin-top:7px; }
    .bar-slim > div { height:100%; border-radius:7px; }

    /* ── ALERT ── */
    .pending-alert { background:linear-gradient(135deg,#fffbeb,#fef9c3); border:1.5px solid #fde68a; border-radius:14px; padding:18px 22px; margin-bottom:1.75rem; }

    /* ── QUICK LINK ── */
    .quick-link { display:flex; flex-direction:column; align-items:center; justify-content:center; padding:18px 10px; border-radius:14px; text-decoration:none; transition:all .2s; border:1.5px solid transparent; gap:8px; }
    .quick-link:hover { transform:translateY(-2px); box-shadow:0 6px 18px rgba(0,0,0,.1); border-color:rgba(255,255,255,.3); color:#fff; }
    .quick-link i { font-size:1.4rem; }
    .quick-link span { font-size:.78rem; font-weight:700; }
    .ql-blue   { background:linear-gradient(135deg,#1a73e8,#1558b0); color:#fff; }
    .ql-green  { background:linear-gradient(135deg,#16a34a,#15803d); color:#fff; }
    .ql-purple { background:linear-gradient(135deg,#7c3aed,#6d28d9); color:#fff; }
    .ql-teal   { background:linear-gradient(135deg,#0d9488,#0f766e); color:#fff; }

    .empty-sm { text-align:center; padding:30px 16px; color:#94a3b8; font-size:.82rem; }
    .hanzi { font-size:1.15rem; line-height:1.2; color:#1e293b; }
</style>
@endsection

@section('content')
@php
    use Illuminate\Support\Facades\Route;
    use Illuminate\Support\Str;

    $angka = fn ($n) => number_format((int) $n, 0, ',', '.');
    $user  = Auth::user();

    // Kartu ringkasan baris 1: [warna, ikon, nilai, label, keterangan]
    $ringkasan = [
        ['green',  'fa-check-double',    $angka($stats['review_hari_ini']), 'Review Hari Ini',   'Akurasi ' . $stats['akurasi_hari_ini'] . '%'],
        ['orange', 'fa-fire',            $angka($stats['streak']),          'Streak Belajar',    'hari berturut-turut'],
        ['blue',   'fa-hourglass-half',  $angka($stats['jatuh_tempo']),     'Jatuh Tempo',       $progres['kosakata']['jatuh_tempo'] . ' kosakata, ' . $progres['karakter']['jatuh_tempo'] . ' karakter, ' . $progres['radikal']['jatuh_tempo'] . ' radikal'],
        ['purple', 'fa-brain',          $angka($stats['dipelajari']),      'Sudah Dipelajari',  $progres['kosakata']['dipelajari'] . ' kosakata, ' . $progres['karakter']['dipelajari'] . ' karakter, ' . $progres['radikal']['dipelajari'] . ' radikal'],
        ['red',    'fa-flag',            $angka($stats['laporan_baru']),    'Laporan Soal Baru', $stats['perlu_dicek'] . ' soal perlu dicek'],
    ];

    // Kartu data master (dipecah 4 per baris)
    $master = [
        ['purple', 'fa-language',       $angka($stats['kosakata']),   'Kosakata',          $stats['kategori'] . ' kategori'],
        ['indigo', 'fa-pen-nib',        $angka($stats['karakter']),   'Karakter',          'Karakter tunggal'],
        ['teal',   'fa-puzzle-piece',   $angka($stats['radikal']),    'Radikal',           'Kangxi'],
        ['red',    'fa-comment-dots',   $angka($stats['kalimat']),    'Contoh Kalimat',    'Sumber soal isian'],
        ['yellow', 'fa-sitemap',        $angka($stats['pola']),       'Pola Tata Bahasa',  'Sumber soal grammar'],
        ['orange', 'fa-book-open',      $angka($stats['paragraf']),   'Paragraf',          'Bahan bacaan'],
        ['green',  'fa-clipboard-list', $angka($stats['paket_soal']), 'Paket Soal',        $stats['paket_aktif'] . ' aktif'],
        ['blue',   'fa-circle-question', $angka($stats['soal']),      'Soal',              'Pilihan ganda A-D'],
    ];

    // Akses cepat: hanya tampil kalau route-nya sudah ada.
    $akses = collect([
        ['admin.kosakata.index',       'fa-language',       'Kosakata'],
        ['admin.karakter.index',       'fa-pen-nib',        'Karakter'],
        ['admin.grup-soal.index',      'fa-clipboard-list', 'Paket Soal'],
        ['admin.laporan-soal.index',   'fa-flag',           'Laporan Soal'],
        ['admin.pengaturan',           'fa-cogs',           'Pengaturan'],
    ])->filter(fn ($a) => Route::has($a[0]))->values();

    $warnaQuick = ['ql-blue', 'ql-green', 'ql-purple', 'ql-teal'];

    $labelBagian = fn ($b) => Str::headline($b);
@endphp

{{-- .container = diberi margin-top setinggi header oleh Kaiadmin, .page-inner = padding isi --}}
<div class="container">
<div class="page-inner">

    {{-- ── WELCOME BANNER ── --}}
    <div class="welcome-banner">
        <div class="z1 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="text-white">
                <div style="font-size:.83rem;opacity:.8;margin-bottom:4px;">
                    {{ now()->translatedFormat('l, d F Y') }}
                </div>
                <h4 class="fw-bold mb-1">Selamat Datang, {{ $user->username }}! 👋</h4>
                <p class="mb-0" style="font-size:.88rem;opacity:.85;">
                    Dashboard Admin {{ $setting?->nama ?? 'Belajar Mandarin' }} &bull; Pantau hafalan, antrean review, dan bank soal dalam satu halaman.
                </p>
            </div>
            <div class="text-white text-end" style="font-size:.82rem;opacity:.8;">
                <div><i class="fas fa-clock me-1"></i> {{ now()->format('H:i T') }}</div>
                <div class="mt-1"><i class="fas fa-user-shield me-1"></i> Administrator</div>
            </div>
        </div>
    </div>

    {{-- ── ALERT: ADA YANG PERLU DITANGANI ── --}}
    @if ($stats['laporan_baru'] > 0 || $stats['perlu_dicek'] > 0)
    <div class="pending-alert d-flex align-items-center gap-3">
        <div style="width:42px;height:42px;background:#fde68a;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="fas fa-triangle-exclamation" style="color:#92400e;font-size:1.1rem;"></i>
        </div>
        <div>
            <div class="fw-bold" style="color:#92400e;font-size:.92rem;">
                @if ($stats['laporan_baru'] > 0)
                    {{ $stats['laporan_baru'] }} laporan soal belum ditangani.
                @endif
                @if ($stats['perlu_dicek'] > 0)
                    {{ $stats['perlu_dicek'] }} soal menunggu pemeriksaan.
                @endif
            </div>
            <div style="font-size:.78rem;color:#a16207;">Soal yang dilaporkan ambigu atau salah kunci sebaiknya diperiksa sebelum dipakai lagi.</div>
        </div>
    </div>
    @endif

    {{-- ── STAT CARDS ROW 1 – Belajar hari ini ── --}}
    <div class="row g-3 mb-3">
        @foreach ($ringkasan as $r)
        @php [$warna, $ikon, $nilai, $label, $sub] = $r; @endphp
        <div class="col-6 col-md-4 col-xl">
            <div class="card stat-card sc-{{ $warna }}">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon si-{{ $warna }}"><i class="fas {{ $ikon }}"></i></div>
                    <div style="min-width:0;">
                        <div class="stat-value">{{ $nilai }}</div>
                        <div class="stat-label">{{ $label }}</div>
                        <div class="stat-sub">{{ $sub }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── STAT CARDS ROW 2 & 3 – Data master ── --}}
    @foreach (array_chunk($master, 4) as $baris)
    <div class="row g-3 {{ $loop->last ? 'mb-4' : 'mb-3' }}">
        @foreach ($baris as $m)
        @php [$warna, $ikon, $nilai, $label, $sub] = $m; @endphp
        <div class="col-6 col-md-3">
            <div class="card stat-card sc-{{ $warna }}">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon si-{{ $warna }}"><i class="fas {{ $ikon }}"></i></div>
                    <div style="min-width:0;">
                        <div class="stat-value">{{ $nilai }}</div>
                        <div class="stat-label">{{ $label }}</div>
                        <div class="stat-sub">{{ $sub }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endforeach

    {{-- ── QUICK LINKS ── --}}
    @if ($akses->isNotEmpty())
    <div class="card dash-card mb-4">
        <div class="card-body p-4">
            <div class="sec-header mb-3">
                <div class="sec-title">
                    <span class="sec-title-dot" style="background:#1a73e8;"></span>
                    Akses Cepat
                </div>
            </div>
            <div class="row g-3">
                @foreach ($akses as $i => $a)
                <div class="col-6 col-md-3">
                    <a href="{{ route($a[0]) }}" class="quick-link {{ $warnaQuick[$i % count($warnaQuick)] }}">
                        <i class="fas {{ $a[1] }}"></i><span>{{ $a[2] }}</span>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- ── ROW: Grafik review + Target hari ini ── --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card dash-card h-100">
                <div class="card-header">
                    <div class="sec-title">
                        <span class="sec-title-dot" style="background:#1a73e8;"></span>
                        Aktivitas Review (30 Hari Terakhir)
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="chartReviewHarian" height="110"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card dash-card h-100">
                <div class="card-header">
                    <div class="sec-title">
                        <span class="sec-title-dot" style="background:#16a34a;"></span>
                        Target Hari Ini
                    </div>
                </div>
                <div class="card-body">
                    @foreach ($target as $t)
                    @php
                        $punyaTarget = (int) $t['target'] > 0;
                        $persen      = $punyaTarget ? min(100, round($t['nilai'] / $t['target'] * 100)) : 0;
                    @endphp
                    <div class="target-row">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="fw-semibold" style="font-size:.84rem;color:#1e293b;">
                                <i class="fas {{ $t['ikon'] }} me-2" style="color:{{ $t['warna'] }};width:16px;text-align:center;"></i>{{ $t['label'] }}
                            </div>
                            <div style="font-size:.8rem;color:#64748b;">
                                <strong style="color:#1e293b;">{{ $angka($t['nilai']) }}</strong>@if ($punyaTarget) / {{ $angka($t['target']) }}@endif
                                @if ($punyaTarget && $t['nilai'] >= $t['target'])
                                    <i class="fas fa-circle-check ms-1" style="color:#16a34a;"></i>
                                @endif
                            </div>
                        </div>
                        @if ($punyaTarget)
                        <div class="bar-slim"><div style="width:{{ $persen }}%;background:{{ $t['warna'] }};"></div></div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ── ROW: Distribusi ── --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="card dash-card h-100">
                <div class="card-header">
                    <div class="sec-title">
                        <span class="sec-title-dot" style="background:#ea580c;"></span>
                        Status Hafalan
                    </div>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <canvas id="chartStatus" height="180"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card dash-card h-100">
                <div class="card-header">
                    <div class="sec-title">
                        <span class="sec-title-dot" style="background:#7c3aed;"></span>
                        Kosakata per Level TOCFL
                    </div>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <canvas id="chartLevel" height="180"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card dash-card h-100">
                <div class="card-header">
                    <div class="sec-title">
                        <span class="sec-title-dot" style="background:#0d9488;"></span>
                        Bank Soal per Bagian
                    </div>
                </div>
                <div class="card-body">
                    @php $maks = max(1, (int) $bank_soal['bagian']->max()); @endphp
                    @foreach (\App\Models\Soal::BAGIAN as $bagian)
                    @php $jumlah = (int) ($bank_soal['bagian'][$bagian] ?? 0); @endphp
                    <div class="target-row">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="fw-semibold" style="font-size:.84rem;color:#1e293b;">{{ $labelBagian($bagian) }}</div>
                            <span class="badge" style="background:#f0fdfa;color:#0d9488;">{{ $angka($jumlah) }} soal</span>
                        </div>
                        <div class="bar-slim"><div style="width:{{ round($jumlah / $maks * 100) }}%;background:#0d9488;"></div></div>
                    </div>
                    @endforeach
                    <div class="d-flex gap-2 flex-wrap mt-3">
                        <span class="badge bdg-kuning">{{ $bank_soal['status'][\App\Models\Soal::STATUS_PERLU_DICEK] ?? 0 }} perlu dicek</span>
                        <span class="badge bdg-benar">{{ $bank_soal['status'][\App\Models\Soal::STATUS_DISETUJUI] ?? 0 }} disetujui</span>
                        <span class="badge bdg-salah">{{ $bank_soal['status'][\App\Models\Soal::STATUS_DITOLAK] ?? 0 }} ditolak</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── ROW: Review Terbaru + Hasil Ujian Terbaru ── --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card dash-card h-100">
                <div class="card-header">
                    <div class="sec-title">
                        <span class="sec-title-dot" style="background:#16a34a;"></span>
                        Aktivitas Review Terbaru
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table dash-table mb-0">
                            <thead><tr>
                                <th>Item</th>
                                <th>Sumber</th>
                                <th>Hasil</th>
                                <th>Waktu</th>
                            </tr></thead>
                            <tbody>
                            @forelse ($review_terbaru as $r)
                            @php $item = $r->item(); @endphp
                            <tr>
                                <td>
                                    <span class="hanzi">{{ $item?->hanzi ?? '-' }}</span>
                                    <div style="font-size:.71rem;color:#94a3b8;">{{ $item?->pinyin ?? '' }}</div>
                                </td>
                                <td>
                                    <span class="badge bdg-{{ $r->sumber }}">{{ ucfirst($r->sumber) }}</span>
                                    <div style="font-size:.71rem;color:#94a3b8;margin-top:3px;">{{ ucfirst($r->jenis_item) }}</div>
                                </td>
                                <td>
                                    <span class="badge {{ $r->benar ? 'bdg-benar' : 'bdg-salah' }}">{{ $r->benar ? 'Benar' : 'Salah' }}</span>
                                </td>
                                <td style="font-size:.76rem;color:#64748b;">{{ $r->direview_pada?->diffForHumans() }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4"><div class="empty-sm"><i class="fas fa-inbox mb-2" style="display:block;font-size:1.5rem;"></i>Belum ada aktivitas review</div></td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card dash-card h-100">
                <div class="card-header">
                    <div class="sec-title">
                        <span class="sec-title-dot" style="background:#1a73e8;"></span>
                        Hasil Latihan &amp; Ujian Terbaru
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table dash-table mb-0">
                            <thead><tr>
                                <th>Paket</th>
                                <th>Skor</th>
                                <th>Hasil</th>
                                <th>Selesai</th>
                            </tr></thead>
                            <tbody>
                            @forelse ($ujian_terbaru as $h)
                            <tr>
                                <td class="fw-semibold" style="color:#1e293b;">
                                    {{ $h->grupSoal?->nama ?? 'Paket #' . $h->grup_soal_id }}
                                    <div style="font-size:.71rem;color:#94a3b8;font-weight:400;">{{ ucfirst($h->grupSoal?->jenis ?? '-') }}</div>
                                </td>
                                <td>
                                    <strong style="color:#1e293b;">{{ (float) $h->skor }}</strong>
                                    <div style="font-size:.71rem;color:#94a3b8;">{{ $h->jumlah_benar }}/{{ $h->jumlah_soal }} benar</div>
                                </td>
                                <td>
                                    @if ($h->lulus === null)
                                        <span class="badge bdg-nonaktif">Latihan</span>
                                    @else
                                        <span class="badge {{ $h->lulus ? 'bdg-benar' : 'bdg-salah' }}">{{ $h->lulus ? 'Lulus' : 'Belum lulus' }}</span>
                                    @endif
                                </td>
                                <td style="font-size:.76rem;color:#64748b;">{{ $h->selesai_pada?->diffForHumans() }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4"><div class="empty-sm"><i class="fas fa-inbox mb-2" style="display:block;font-size:1.5rem;"></i>Belum ada latihan atau ujian yang selesai</div></td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── ROW: Item Bandel + Laporan Soal ── --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card dash-card h-100">
                <div class="card-header">
                    <div class="sec-title">
                        <span class="sec-title-dot" style="background:#dc2626;"></span>
                        Item Paling Sering Salah
                    </div>
                </div>
                <div class="card-body">
                    @forelse ($item_bandel as $i => $b)
                    <div class="rank-item">
                        <div class="rank-no {{ $i===0?'rank-1':($i===1?'rank-2':($i===2?'rank-3':'rank-n')) }}">{{ $i + 1 }}</div>
                        <div style="flex:1;min-width:0;">
                            <div class="d-flex align-items-baseline gap-2">
                                <span class="hanzi">{{ $b['hanzi'] ?? '-' }}</span>
                                <span style="font-size:.75rem;color:#94a3b8;">{{ $b['pinyin'] }}</span>
                            </div>
                            <div style="font-size:.78rem;color:#64748b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                {{ $b['jenis'] }}{{ $b['arti'] ? ': ' . $b['arti'] : '' }}
                            </div>
                        </div>
                        <span class="badge bdg-salah">{{ $b['jumlah_salah'] }}x salah</span>
                    </div>
                    @empty
                    <div class="empty-sm">Belum ada item yang sering salah</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card dash-card h-100">
                <div class="card-header">
                    <div class="sec-title">
                        <span class="sec-title-dot" style="background:#ea580c;"></span>
                        Laporan Soal Terbaru
                    </div>
                </div>
                <div class="card-body">
                    @forelse ($laporan_terbaru as $l)
                    <div class="vis-item">
                        <div class="vis-icon"><i class="fas fa-flag"></i></div>
                        <div style="flex:1;min-width:0;">
                            <div style="font-size:.82rem;font-weight:600;color:#1e293b;">{{ $labelBagian($l->jenis) }}</div>
                            <div style="font-size:.72rem;color:#94a3b8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                Soal #{{ $l->soal_id }}{{ $l->soal?->bagian ? ', ' . $labelBagian($l->soal->bagian) : '' }}{{ $l->catatan ? ': ' . Str::limit($l->catatan, 40) : '' }}
                            </div>
                        </div>
                        <div style="font-size:.72rem;color:#64748b;white-space:nowrap;">{{ $l->created_at?->diffForHumans() }}</div>
                    </div>
                    @empty
                    <div class="empty-sm">Tidak ada laporan baru</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>{{-- end .page-inner --}}
</div>{{-- end .container --}}
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
// ── Data dari Laravel ──────────────────────────────────────────────
const grafikReview     = @json($grafik);
const distribusiLevel  = @json($distribusi_level);
const distribusiStatus = @json($distribusi_status);

const fontOpt = { family: 'Plus Jakarta Sans', size: 11 };

// ── CHART 1: Review 30 hari (Bar + Line) ──────────────────────────
new Chart(document.getElementById('chartReviewHarian'), {
    type: 'bar',
    data: {
        labels: grafikReview.label,
        datasets: [
            { label: 'Total Review', data: grafikReview.total, backgroundColor: 'rgba(26,115,232,.15)', borderColor: '#1a73e8', borderWidth: 2, borderRadius: 6 },
            { label: 'Jawaban Benar', data: grafikReview.benar, type: 'line', borderColor: '#16a34a', backgroundColor: 'rgba(22,163,74,.08)', fill: true, tension: .4, pointBackgroundColor: '#16a34a', pointRadius: 3 }
        ]
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: { legend: { position: 'top', labels: { font: fontOpt, padding: 16 } } },
        scales: {
            y: { beginAtZero: true, ticks: { precision: 0, font: { size: 10 } }, grid: { color: '#f1f5f9' } },
            x: { ticks: { font: { size: 10 }, maxRotation: 0, autoSkip: true, maxTicksLimit: 15 }, grid: { color: '#f8fafc' } }
        }
    }
});

// ── CHART 2: Status hafalan (Doughnut) ────────────────────────────
new Chart(document.getElementById('chartStatus'), {
    type: 'doughnut',
    data: {
        labels: ['Ingat Sepenuhnya', 'Lupa & Ingat', 'Berikutnya'],
        datasets: [{
            data: [
                distribusiStatus.ingat_sepenuhnya || 0,
                distribusiStatus.lupa_dan_ingat   || 0,
                distribusiStatus.berikutnya       || 0
            ],
            backgroundColor: ['#16a34a', '#ca8a04', '#94a3b8'],
            hoverOffset: 6, borderWidth: 3, borderColor: '#fff'
        }]
    },
    options: { responsive: true, cutout: '65%', plugins: { legend: { position: 'bottom', labels: { font: fontOpt, padding: 12 } } } }
});

// ── CHART 3: Kosakata per level TOCFL (Doughnut) ──────────────────
new Chart(document.getElementById('chartLevel'), {
    type: 'doughnut',
    data: {
        labels: distribusiLevel.map(x => x.label),
        datasets: [{
            data: distribusiLevel.map(x => x.total),
            backgroundColor: ['#1a73e8', '#16a34a', '#7c3aed', '#ea580c', '#ca8a04', '#dc2626', '#94a3b8'],
            hoverOffset: 6, borderWidth: 3, borderColor: '#fff'
        }]
    },
    options: { responsive: true, cutout: '65%', plugins: { legend: { position: 'bottom', labels: { font: fontOpt, padding: 12 } } } }
});
</script>
@endsection
