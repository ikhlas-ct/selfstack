@extends('layouts.user.user')

@section('title', $grup->judul)

@section('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    body, .card, .table, .btn, .form-control, .form-select, h4, h5, h6 { font-family: 'Plus Jakarta Sans', sans-serif; }

    :root {
        --gk-blue: #1a73e8;
        --gk-blue-dark: #1558b0;
        --gk-ink: #1e293b;
        --gk-muted: #64748b;
        --gk-faint: #94a3b8;
        --gk-line: #e2e8f0;
        --gk-soft: #f8fafc;
        --gk-hanzi: 'Noto Sans TC', 'PingFang TC', 'Microsoft JhengHei', 'Plus Jakarta Sans', sans-serif;
    }

    /* ===== PAGE HEADER ===== */
    .ph-card { background: #fff; border: 1px solid #e9ecef; border-radius: 14px; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; margin-bottom: 1.25rem; position: relative; overflow: hidden; box-shadow: 0 1px 6px rgba(0,0,0,.05); }
    .ph-card::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; border-radius: 14px 0 0 14px; }
    .ph-card.index-page::before, .ph-card.show-page::before { background: #0369a1; }
    .ph-card.hasil-page::before { background: #16a34a; }
    .ph-left { display: flex; align-items: center; gap: 12px; min-width: 0; }
    .ph-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
    .ph-icon.index, .ph-icon.show { background: #e0f2fe; color: #0369a1; }
    .ph-icon.hasil { background: #dcfce7; color: #16a34a; }
    .ph-title { font-size: 1.05rem; font-weight: 700; color: var(--gk-ink); letter-spacing: -.2px; line-height: 1.2; margin: 0; }
    .ph-breadcrumb { display: flex; align-items: center; gap: 4px; flex-wrap: wrap; margin-top: 4px; list-style: none; padding: 0; margin-bottom: 0; }
    .ph-breadcrumb li { display: flex; align-items: center; }
    .ph-breadcrumb li + li::before { content: '›'; color: #cbd5e1; font-size: .7rem; margin: 0 4px; }
    .ph-breadcrumb a { font-size: .75rem; color: var(--gk-blue); text-decoration: none; }
    .ph-breadcrumb a:hover { text-decoration: underline; }
    .ph-breadcrumb .bc-active { font-size: .75rem; color: var(--gk-faint); }
    .ph-actions { display: flex; gap: 8px; flex-wrap: wrap; }

    /* ===== CARD ===== */
    .form-card { border: none; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,.07); }
    .section-divider { background: #f8f9fa; border-left: 4px solid #1269db; padding: 8px 14px; border-radius: 0 6px 6px 0; font-weight: 600; font-size: .9rem; color: #1269db; margin-bottom: 1rem; display: flex; align-items: center; justify-content: space-between; gap: 8px; }
    .info-label { font-size: .72rem; font-weight: 600; color: var(--gk-faint); letter-spacing: .02em; }
    .help-text { font-size: .78rem; color: var(--gk-muted); margin-top: 8px; }
    label.lbl { font-size: .85rem; font-weight: 600; color: #334155; margin-bottom: 4px; }
    .sticky-side { position: sticky; top: 16px; }
    @media (max-width: 991.98px) { .sticky-side { position: static; } }

    /* ===== ALERT ===== */
    .alert { border: none; border-radius: 12px; font-size: .85rem; font-weight: 500; padding: 12px 16px; }
    .alert-success { background: #dcfce7; color: #15803d; }
    .alert-danger { background: #fee2e2; color: #b91c1c; }
    .alert-warning { background: #fef3c7; color: #92400e; }

    /* ===== FORM ===== */
    .form-control, .form-select { border-radius: 10px; border: 1.5px solid var(--gk-line); font-size: .85rem; color: #334155; background-color: var(--gk-soft); transition: border-color .2s, box-shadow .2s; }
    .form-control:focus, .form-select:focus { border-color: var(--gk-blue); background: #fff; box-shadow: 0 0 0 3px rgba(26,115,232,.12); }
    .form-control::placeholder { color: var(--gk-faint); }
    .invalid-feedback { font-size: .76rem; }
    .input-group > .form-control:not(:last-child) { border-top-right-radius: 0; border-bottom-right-radius: 0; }
    .input-group > .btn:not(:first-child) { border-top-left-radius: 0; border-bottom-left-radius: 0; }

    /* ===== BADGES ===== */
    .badge { font-size: .7rem; font-weight: 600; padding: 4px 9px; border-radius: 6px; letter-spacing: .2px; }
    .badge-level { background: #e8f0fe; color: var(--gk-blue); }
    .badge-jumlah { background: #dcfce7; color: #15803d; }
    .badge-pilih { background: #e0f2fe; color: #0369a1; }
    .badge-latihan { background: #fef3c7; color: #92400e; }
    .badge-ujian { background: #fee2e2; color: #b91c1c; }
    .badge-draf { background: #f1f5f9; color: var(--gk-muted); }
    .badge-listening { background: #ede9fe; color: #6d28d9; }
    .badge-reading { background: #e0f2fe; color: #0369a1; }
    .badge-tata { background: #fce7f3; color: #be185d; }
    .badge-kosa { background: #dcfce7; color: #15803d; }
    .badge-karakter { background: #ffedd5; color: #c2410c; }
    .badge-benar { background: #dcfce7; color: #15803d; }
    .badge-salah { background: #fee2e2; color: #b91c1c; }
    .badge-lewat { background: #f1f5f9; color: var(--gk-muted); }

    /* ===== BUTTONS ===== */
    .btn-primary { background: linear-gradient(135deg, var(--gk-blue), var(--gk-blue-dark)); border: none; border-radius: 10px; font-weight: 600; font-size: .83rem; padding: 8px 18px; box-shadow: 0 2px 8px rgba(26,115,232,.35); transition: all .2s ease; }
    .btn-primary:hover { background: linear-gradient(135deg, var(--gk-blue-dark), #0f3e82); box-shadow: 0 4px 14px rgba(26,115,232,.45); transform: translateY(-1px); }
    .btn-success { border-radius: 10px; font-weight: 600; font-size: .83rem; padding: 8px 18px; }
    .btn-outline-secondary { border-radius: 10px; font-size: .83rem; border-color: var(--gk-line); color: var(--gk-muted); padding: 7px 12px; }
    .btn-outline-secondary:hover { background: #f1f5f9; border-color: #cbd5e1; color: #334155; }
    .btn-outline-danger { border-radius: 10px; font-size: .8rem; border-color: #fecaca; color: #dc2626; }
    .btn-outline-danger:hover { background: #fee2e2; border-color: #fca5a5; color: #b91c1c; }
    .btn:focus-visible { outline: 3px solid rgba(26,115,232,.35); outline-offset: 2px; }

    /* ===== HALAMAN SOAL ===== */
    .bar-waktu { position: sticky; top: 8px; z-index: 5; background: rgba(255,255,255,.96); backdrop-filter: blur(6px); border: 1px solid #e9ecef; border-radius: 14px; padding: 10px 16px 12px; margin-bottom: 1.25rem; box-shadow: 0 2px 10px rgba(0,0,0,.07); }
    .bar-info { display: flex; align-items: center; gap: 6px 16px; flex-wrap: wrap; font-size: .86rem; font-weight: 700; color: var(--gk-ink); }
    .bar-info .posisi { margin-right: auto; }
    .bar-info .posisi small { font-weight: 600; color: var(--gk-muted); font-size: .78rem; margin-left: 6px; }
    .bar-info .timer { font-variant-numeric: tabular-nums; font-size: .95rem; color: var(--gk-blue); background: #e8f0fe; border-radius: 999px; padding: 4px 12px; display: inline-flex; align-items: center; gap: 6px; }
    .bar-info .timer.mepet { color: #dc2626; background: #fee2e2; }
    .bar-prog { height: 6px; border-radius: 999px; background: #e2e8f0; margin-top: 9px; overflow: hidden; }
    .bar-prog > div { height: 100%; background: var(--gk-blue); border-radius: 999px; transition: width .3s ease; }

    .soal-card-body { padding: 24px; }
    .soal-head { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 16px; }
    .soal-no { min-width: 30px; height: 30px; padding: 0 8px; border-radius: 15px; background: var(--gk-ink); color: #fff; font-size: .8rem; font-weight: 800; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .soal-petunjuk { margin-left: auto; font-size: .74rem; color: var(--gk-faint); }
    .soal-paragraf { font-family: var(--gk-hanzi); color: #475569; background: var(--gk-soft); border: 1px solid #eef2f6; border-radius: 12px; padding: 14px 16px; margin-bottom: 16px; }
    .soal-tanya { font-family: var(--gk-hanzi); font-weight: 600; color: var(--gk-ink); margin-bottom: 20px; }
    .soal-audio { margin-bottom: 18px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; font-size: .78rem; color: var(--gk-muted); background: #f5f3ff; border: 1px solid #ede9fe; border-radius: 12px; padding: 10px 12px; }
    .soal-audio audio { width: 100%; height: 36px; }

    /* Pilihan jawaban: netral, tanpa penanda benar/salah. Yang terpilih saja yang disorot. */
    .opsi-list { display: grid; gap: 12px; }
    .opsi-pilih { position: relative; display: flex; gap: 16px; align-items: center; margin: 0; padding: 12px 18px 12px 14px; background: #fff; border: 2px solid var(--gk-line); border-radius: 14px; cursor: pointer; font-family: var(--gk-hanzi); line-height: 1.4; color: #334155; transition: border-color .15s, background .15s, box-shadow .15s; }
    .opsi-pilih:hover { border-color: #93c5fd; background: #f8fbff; }
    .opsi-pilih input { position: absolute; opacity: 0; pointer-events: none; }
    .opsi-pilih .huruf { width: 38px; height: 38px; border-radius: 10px; background: #f1f5f9; color: var(--gk-muted); font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 800; font-size: .95rem; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; transition: background .15s, color .15s; }
    .opsi-pilih .tanda { margin-left: auto; flex-shrink: 0; font-size: 1.15rem; color: var(--gk-blue); opacity: 0; transform: scale(.6); transition: opacity .15s, transform .15s; }
    .opsi-pilih input:checked ~ .huruf { background: var(--gk-blue); color: #fff; }
    .opsi-pilih input:checked ~ .tanda { opacity: 1; transform: none; }
    .opsi-pilih:has(input:checked) { border-color: var(--gk-blue); background: #eff6ff; box-shadow: 0 0 0 3px rgba(26,115,232,.12); }
    .opsi-pilih:has(input:focus-visible) { outline: 3px solid rgba(26,115,232,.35); outline-offset: 2px; }

    .soal-aksi { display: grid; grid-template-columns: 1fr auto 1fr; align-items: center; gap: 10px; margin-top: 22px; padding-top: 18px; border-top: 1px dashed #dbe3ec; }
    .soal-aksi > :first-child { justify-self: start; }
    .soal-aksi .kanan { display: flex; gap: 8px; justify-self: end; }
    @media (max-width: 575.98px) {
        .soal-aksi { grid-template-columns: 1fr 1fr; }
        .soal-aksi .btn-ragu { grid-column: 1 / -1; grid-row: 1; justify-self: center; }
    }
    .soal-aksi .btn { padding: 10px 20px; }
    .tips-kbd { font-size: .72rem; color: var(--gk-faint); margin-top: 12px; }
    .tips-kbd kbd { font-family: 'Plus Jakarta Sans', sans-serif; font-size: .68rem; font-weight: 700; color: var(--gk-muted); background: #f1f5f9; border: 1px solid var(--gk-line); border-bottom-width: 2px; border-radius: 5px; padding: 0 5px; }

    /* Kotak nomor: hanya dua status (sudah / belum dijawab) + posisi sekarang */
    .nav-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(42px, 1fr)); gap: 8px; }
    .nav-no { position: relative; height: 42px; border-radius: 10px; border: 1.5px solid var(--gk-line); background: #fff; color: #64748b; font-size: .82rem; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; padding: 0; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; transition: border-color .15s, background .15s; }
    .nav-no:hover { border-color: #93c5fd; color: var(--gk-blue); }
    .nav-no.terjawab { background: #e8f0fe; border-color: #93c5fd; color: var(--gk-blue); }
    .nav-no.sekarang { background: var(--gk-ink); border-color: var(--gk-ink); color: #fff; }
    .nav-no.sekarang.terjawab::after { content: ''; position: absolute; top: 4px; right: 4px; width: 7px; height: 7px; border-radius: 50%; background: #60a5fa; }
    .nav-no:focus-visible { outline: 3px solid rgba(26,115,232,.35); outline-offset: 2px; }
    .nav-legend { display: flex; flex-wrap: wrap; gap: 4px 14px; margin-top: 14px; font-size: .72rem; color: var(--gk-muted); }
    .nav-legend span::before { content: ''; display: inline-block; width: 10px; height: 10px; border-radius: 3px; margin-right: 5px; vertical-align: -1px; border: 1.5px solid var(--gk-line); background: #fff; }
    .nav-legend .l-jawab::before { background: #e8f0fe; border-color: #93c5fd; }
    .nav-legend .l-sekarang::before { background: var(--gk-ink); border-color: var(--gk-ink); }
    .nav-ringkas { font-size: .78rem; color: var(--gk-muted); margin-bottom: 12px; }
    .nav-ringkas strong { color: var(--gk-ink); }

    /* ===== SUARA (TEXT-TO-SPEECH) ===== */
    .tts-bar { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; width: 100%; }
    .tts-bar[hidden] { display: none; }
    .tts-bar .form-select { flex: 1 1 220px; max-width: 380px; font-size: .78rem; padding: 5px 30px 5px 10px; }
    .tts-info { font-size: .74rem; color: var(--gk-faint); }

    /* Huruf Mandarin dibuat besar. !important karena CSS tema (label/.card) menimpa ukuran font. */
    #area-soal .soal-tanya { font-size: clamp(2rem, 4.5vw, 2.8rem) !important; line-height: 1.5; }
    #area-soal .soal-paragraf { font-size: clamp(1.6rem, 3.6vw, 2.2rem) !important; line-height: 1.9; }
    #area-soal .opsi-pilih { font-size: clamp(2rem, 4.5vw, 2.8rem) !important; }
    #area-soal .opsi-pilih .teks-opsi { font-size: clamp(2rem, 4.5vw, 2.8rem) !important; line-height: 1.4; font-family: var(--gk-hanzi); }

    #area-soal { transition: opacity .15s; }
    #area-soal.memuat { opacity: .55; pointer-events: none; }

/* ===== RAGU-RAGU (kuning) ===== */
    .btn-ragu { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 9px 18px; border-radius: 999px; border: 1.5px solid #fcd34d; background: #fffbeb; color: #92400e; font-size: .83rem; font-weight: 700; font-family: inherit; line-height: 1.4; cursor: pointer; transition: background .15s, border-color .15s, box-shadow .15s; }
    .btn-ragu:hover { background: #fef3c7; border-color: #fbbf24; }
    .btn-ragu[aria-pressed="true"] { background: #f59e0b; border-color: #f59e0b; color: #fff; box-shadow: 0 2px 8px rgba(245,158,11,.4); }
    .btn-ragu:focus-visible { outline: 3px solid rgba(245,158,11,.45); outline-offset: 2px; }
    .form-card.ragu-aktif { box-shadow: 0 0 0 2px #fcd34d, 0 2px 12px rgba(0,0,0,.07); }
    .form-card.ragu-aktif .soal-no { background: #f59e0b; }
    .nav-no.ragu { background: #fef3c7; border-color: #fcd34d; color: #92400e; }
    .nav-no.ragu:hover { border-color: #f59e0b; color: #92400e; }
    .nav-no.ragu.terjawab::after { content: ''; position: absolute; top: 4px; right: 4px; width: 7px; height: 7px; border-radius: 50%; background: var(--gk-blue); }
    .nav-no.sekarang.ragu { background: var(--gk-ink); border-color: #f59e0b; color: #fff; box-shadow: 0 0 0 2px #fcd34d; }
    .nav-legend .l-ragu::before { background: #fef3c7; border-color: #fcd34d; }
    .teks-ragu, .ragu-hitung { color: #b45309; }

@media (prefers-reduced-motion: reduce) {
        #area-soal, .bar-prog > div, .opsi-pilih, .opsi-pilih .huruf, .opsi-pilih .tanda, .nav-no { transition: none; }
        .btn-primary { transition: none; }
        .btn-primary:hover { transform: none; }
    }
</style>
@endsection

@section('content')
    @php
        // Nama, ikon, dan warna badge untuk tiap bagian soal.
        $infoBagian = function (string $bagian): array {
            $peta = [
                'listening'   => ['Listening', 'fa-headphones', 'badge-listening'],
                'reading'     => ['Reading', 'fa-book-open', 'badge-reading'],
                'tata_bahasa' => ['Tata bahasa', 'fa-language', 'badge-tata'],
                'kosakata'    => ['Kosakata', 'fa-font', 'badge-kosa'],
                'karakter'    => ['Karakter', 'fa-pen', 'badge-karakter'],
            ];
            $p = $peta[$bagian] ?? [ucfirst(str_replace('_', ' ', $bagian)), 'fa-tag', 'badge-draf'];

            return ['nama' => $p[0], 'ikon' => $p[1], 'kelas' => $p[2]];
        };

        $sudahDijawab = $jawabanSaatIni?->jawaban_dipilih;
        $akhir        = $no === $total;
        $kataAkhiri   = $latihan ? 'latihan' : 'ujian';
        $terjawab     = $total - $belumDijawab;
    @endphp

    {{-- #area-soal: seluruh isi di dalamnya diganti lewat AJAX saat pindah soal (tanpa refresh halaman) --}}
    <div class="container" id="area-soal">

        <div class="ph-card show-page">
            <div class="ph-left">
                <div class="ph-icon show"><i class="fas fa-pencil-alt"></i></div>
                <div style="min-width:0;">
                    <h5 class="ph-title">{{ $grup->judul }}</h5>
                    <ol class="ph-breadcrumb" aria-label="breadcrumb">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><a href="{{ route('admin.kerjakan.index') }}">Kerjakan Soal</a></li>
                        <li><a href="{{ route('admin.kerjakan.detail', $grup) }}">{{ \Illuminate\Support\Str::limit($grup->judul, 24) }}</a></li>
                        <li><span class="bc-active">Soal {{ $no }}</span></li>
                    </ol>
                </div>
            </div>
            <div class="ph-actions">
                <span class="badge {{ $latihan ? 'badge-latihan' : 'badge-ujian' }} align-self-center">{{ $latihan ? 'Latihan' : 'Ujian' }}</span>
            </div>
        </div>

        <div class="page-inner">

            @if (session('success'))
                <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</div>
            @endif
            @if (session('error') || $errors->any())
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>{{ session('error') ?? $errors->first() }}</div>
            @endif

            {{-- Bar progres + timer (menempel di atas saat scroll) --}}
            <div class="bar-waktu">
                <div class="bar-info">
                    <span class="posisi">Soal {{ $no }} dari {{ $total }}<small>{{ $terjawab }} terjawab @if ($jumlahRagu > 0)<span class="ragu-hitung">, {{ $jumlahRagu }} ragu-ragu</span>@endif</small></span>
                    @if ($sisaDetik !== null)
                        <span class="timer" id="timer" data-sisa="{{ $sisaDetik }}" role="timer" aria-label="Sisa waktu">
                            <i class="fas fa-clock"></i><span id="timer-teks">--:--</span>
                        </span>
                    @endif
                </div>
                <div class="bar-prog" aria-hidden="true"><div style="width: {{ round($terjawab / $total * 100) }}%"></div></div>
            </div>

            {{-- Satu form untuk soal + navigasi: semua tombol ber-name="ke" menyimpan jawaban lalu pindah --}}
            <form method="POST" action="{{ route('admin.kerjakan.jawab', $hasil) }}" id="form-soal">
                @csrf
                <input type="hidden" name="soal_id" value="{{ $soal->id }}">
                <input type="hidden" name="no" value="{{ $no }}">
                <input type="hidden" name="waktu_ms" id="waktu_ms" value="0">
                <input type="hidden" name="ragu" id="ragu" value="{{ $ragu ? 1 : 0 }}" data-awal="{{ $ragu ? 1 : 0 }}">

                <div class="row g-4">

                    {{-- ===== Kiri: soal ===== --}}
                    <div class="col-lg-8">
                        <div class="card form-card {{ $ragu ? 'ragu-aktif' : '' }}" id="kartu-soal">
                            <div class="card-body soal-card-body">

                                <div class="soal-head">
                                    <span class="soal-no">{{ $no }}</span>
                                    @php $ib = $infoBagian($soal->bagian); @endphp
                                    <span class="badge {{ $ib['kelas'] }}"><i class="fas {{ $ib['ikon'] }} me-1"></i>{{ $ib['nama'] }}</span>
                                </div>

                                @if ($soal->bagian === 'listening')
                                    @php
                                        // Kolom soals.audio: URL/path file audio -> <audio>; teks biasa -> dibacakan browser;
                                        // kosong -> hanzi kosakata soal yang dibacakan browser.
                                        $audioMentah = trim((string) $soal->audio);
                                        $adaFile     = $audioMentah !== '' && preg_match('#^(https?://|/)|\.(mp3|wav|ogg|m4a|aac)(\?.*)?$#i', $audioMentah);
                                        $srcAudio    = $adaFile
                                            ? (\Illuminate\Support\Str::startsWith($audioMentah, ['http://', 'https://', '/']) ? $audioMentah : asset('storage/' . ltrim($audioMentah, '/')))
                                            : null;
                                        $teksSuara   = ($audioMentah !== '' && ! $adaFile) ? $audioMentah : ($soal->kosakata->hanzi ?? null);

                                        // Soal Dengar -> Hanzi tanpa file audio dan tanpa tautan kosakata:
                                        // yang dibacakan adalah hanzi pada jawaban yang benar.
                                        if (! $teksSuara && $soal->tipe === 'dengar_ke_hanzi') {
                                            $teksSuara = $soal->pilihan[$soal->jawaban_benar] ?? null;
                                        }

                                        // Suara otomatis keluar saat soal dibuka (kecuali ada file audio).
                                        $autoPutar = $teksSuara && ! $srcAudio;
                                    @endphp
                                    <div class="soal-audio" data-audio-blok>
                                        @if ($srcAudio)
                                            <audio controls preload="metadata" src="{{ $srcAudio }}" data-audio-file></audio>
                                        @endif

                                        @if ($teksSuara)
                                            {{-- Tanpa file audio (atau file gagal dimuat) suara dibacakan browser lewat text-to-speech --}}
                                            <div class="tts-bar" data-tts-blok @if ($srcAudio) hidden @endif>
                                                <button type="button" class="btn btn-outline-secondary btn-sm" data-suara="{{ $teksSuara }}" @if ($autoPutar) data-suara-auto @endif>
                                                    <i class="fas fa-volume-up me-1"></i> Putar suara
                                                </button>
                                                <select class="form-select form-select-sm" data-pilih-suara aria-label="Pilih suara">
                                                    <option value="">(Otomatis — Google 普通话 zh-CN kalau ada)</option>
                                                </select>
                                                <span class="tts-info" data-tts-info></span>
                                            </div>
                                        @elseif (! $srcAudio)
                                            <span><i class="fas fa-volume-mute me-1"></i>Audio belum tersedia untuk soal ini.</span>
                                        @endif
                                    </div>
                                @endif

                                @if ($soal->paragraf)
                                    <div class="soal-paragraf">{!! nl2br(e(strip_tags($soal->paragraf))) !!}</div>
                                @endif

                                <div class="soal-tanya">{!! nl2br(e($soal->pertanyaan)) !!}</div>

                                <div class="opsi-list" role="radiogroup" aria-label="Pilihan jawaban">
                                    @foreach ($soal->pilihan as $huruf => $teks)
                                        <label class="opsi-pilih">
                                            <input type="radio" name="jawaban" value="{{ $huruf }}" @checked($sudahDijawab === $huruf)>
                                            <span class="huruf">{{ $huruf }}</span>
                                            <span class="teks-opsi">{{ $teks }}</span>
                                            <i class="fas fa-check-circle tanda" aria-hidden="true"></i>
                                        </label>
                                    @endforeach
                                </div>

                                {{-- ===== Tombol aksi: setiap tombol menyimpan pilihan saat ini sebelum pindah ===== --}}
                                <div class="soal-aksi">
                                    @if ($no > 1)
                                        <button type="submit" name="ke" value="{{ $no - 1 }}" class="btn btn-outline-secondary" data-prev formnovalidate>
                                            <i class="fas fa-arrow-left me-1"></i> Sebelumnya
                                        </button>
                                    @else
                                        <span></span>
                                    @endif

                                    <button type="button" class="btn-ragu" data-ragu-toggle aria-pressed="{{ $ragu ? 'true' : 'false' }}" title="Tandai soal ini untuk dicek lagi (tombol R)">
                                        <i class="fas fa-flag"></i><span data-ragu-teks>{{ $ragu ? 'Ditandai ragu-ragu' : 'Tandai ragu-ragu' }}</span>
                                    </button>

                                    <div class="kanan">
                                        @if (! $akhir)
                                            <button type="submit" name="ke" value="{{ $no + 1 }}" class="btn btn-primary" data-next formnovalidate>
                                                Simpan &amp; berikutnya <i class="fas fa-arrow-right ms-1"></i>
                                            </button>
                                        @else
                                            <button type="submit" name="ke" value="selesai" class="btn btn-success" data-akhiri data-belum="{{ $belumDijawab }}" data-ragu="{{ $jumlahRagu }}" formnovalidate>
                                                <i class="fas fa-flag-checkered me-1"></i> Simpan &amp; selesai
                                            </button>
                                        @endif
                                    </div>
                                </div>

                                <div class="tips-kbd d-none d-lg-block">
                                    <kbd>A</kbd>–<kbd>D</kbd> pilih jawaban &nbsp;·&nbsp; <kbd>Enter</kbd> simpan &amp; berikutnya &nbsp;·&nbsp; <kbd>←</kbd> <kbd>→</kbd> pindah soal &nbsp;·&nbsp; <kbd>R</kbd> ragu-ragu
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- ===== Kanan: navigasi nomor ===== --}}
                    <div class="col-lg-4">
                        <div class="sticky-side" style="top: 90px;">
                            <div class="card form-card">
                                <div class="card-body">
                                    <div class="section-divider">
                                        <span><i class="fas fa-th me-2"></i>Daftar soal</span>
                                        <span class="badge badge-pilih">{{ $terjawab }}/{{ $total }}</span>
                                    </div>

                                    <div class="nav-grid">
                                        @foreach ($nav as $item)
                                            @php
                                                $kelasNav = $item['status'] . ($item['ragu'] ? ' ragu' : '') . ($item['no'] === $no ? ' sekarang' : '');
                                                $labelNav = 'Soal ' . $item['no'] . ($item['status'] === 'kosong' ? ', belum dijawab' : ', sudah dijawab') . ($item['ragu'] ? ', ragu-ragu' : '');
                                            @endphp
                                            <button type="submit" name="ke" value="{{ $item['no'] }}" formnovalidate
                                                    class="nav-no {{ $kelasNav }}" aria-label="{{ $labelNav }}"
                                                    @if ($item['no'] === $no) aria-current="true" @endif>{{ $item['no'] }}</button>
                                        @endforeach
                                    </div>

                                    <div class="nav-legend">
                                        <span class="l-sekarang">Sekarang</span>
                                        <span class="l-jawab">Sudah dijawab</span>
                                        <span class="l-ragu">Ragu-ragu</span>
                                        <span>Belum dijawab</span>
                                    </div>

                                    <hr class="my-3">

                                    <div class="nav-ringkas">
                                        @if ($belumDijawab > 0)
                                            Masih <strong>{{ $belumDijawab }}</strong> soal belum dijawab.
                                        @else
                                            Semua soal sudah dijawab.
                                        @endif
                                        @if ($jumlahRagu > 0)
                                            <br><span class="teks-ragu"><i class="fas fa-flag me-1"></i><strong>{{ $jumlahRagu }}</strong> soal ditandai ragu-ragu.</span>
                                        @endif
                                    </div>

                                    <button type="submit" name="ke" value="selesai" class="btn btn-outline-danger w-100"
                                            data-akhiri data-belum="{{ $belumDijawab }}" data-ragu="{{ $jumlahRagu }}" formnovalidate>
                                        <i class="fas fa-flag-checkered me-1"></i> Akhiri {{ $kataAkhiri }}
                                    </button>
                                    <div class="help-text">
                                        Jawaban boleh diubah kapan saja sebelum kamu mengakhiri. Benar atau salahnya baru terlihat di halaman hasil.
                                        Soal yang belum dijawab dihitung salah.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </form>

        </div>
    </div>

    <script>
        // Text-to-speech untuk soal listening. Dipakai kalau soal tidak punya file audio (atau file gagal dimuat).
        // window.siapkanSuara() dipanggil saat halaman dibuka dan setiap kali soal diganti lewat AJAX.
        (function () {
            var KUNCI = 'kerjakan_suara';
            var didukung = 'speechSynthesis' in window;
            var synth  = didukung ? window.speechSynthesis : null;
            var simpan = '';
            try { simpan = localStorage.getItem(KUNCI) || ''; } catch (e) {}

            // File audio gagal dimuat -> tampilkan pemutar suara browser sebagai gantinya.
            document.addEventListener('error', function (e) {
                var audio = e.target;
                if (!audio.matches || !audio.matches('[data-audio-file]')) return;
                var wrap = audio.closest('[data-audio-blok]');
                var tts  = wrap && wrap.querySelector('[data-tts-blok]');
                if (!tts) return;
                audio.hidden = true;
                tts.hidden = false;
                var info = tts.querySelector('[data-tts-info]');
                if (info) info.textContent = 'File audio tidak bisa dimuat, dibacakan oleh browser.';
            }, true);

            // Suara Mandarin di urutan atas, sisanya menyusul.
            function daftarSuara() {
                var semua = synth.getVoices().slice();
                var zh = semua.filter(function (v) { return /^zh/i.test(v.lang); });
                var lain = semua.filter(function (v) { return !/^zh/i.test(v.lang); });
                return zh.concat(lain);
            }

            function isiPilihan() {
                var suara = daftarSuara();
                document.querySelectorAll('[data-pilih-suara]').forEach(function (sel) {
                    while (sel.options.length > 1) sel.remove(1);
                    suara.forEach(function (v) {
                        var o = document.createElement('option');
                        o.value = v.name;
                        o.textContent = v.name + ' (' + v.lang + ')';
                        sel.appendChild(o);
                    });
                    sel.value = simpan;
                    if (sel.value !== simpan) sel.value = '';
                });
            }

            // Otomatis: pilihan yang tersimpan -> Google 普通话 (zh-CN) -> zh-CN lain -> zh-TW -> zh apa pun.
            function suaraTerpilih() {
                var suara = synth.getVoices();
                if (simpan) {
                    var dipilih = suara.find(function (v) { return v.name === simpan; });
                    if (dipilih) return dipilih;
                }
                return suara.find(function (v) { return /^zh[-_]CN$/i.test(v.lang) && /google/i.test(v.name); })
                    || suara.find(function (v) { return /^zh[-_]CN$/i.test(v.lang); })
                    || suara.find(function (v) { return /^zh[-_]TW$/i.test(v.lang); })
                    || suara.find(function (v) { return /^zh/i.test(v.lang); })
                    || null;
            }

            function ucapkan(teks, tombol) {
                synth.cancel();
                var u = new SpeechSynthesisUtterance(teks);
                var v = suaraTerpilih();
                if (v) { u.voice = v; u.lang = v.lang; } else { u.lang = 'zh-TW'; }
                u.rate = 0.85;

                // Browser memblokir suara otomatis -> sorot tombol supaya orang tahu harus menekannya.
                u.onerror = function (ev) {
                    if (ev.error !== 'not-allowed' || !tombol) return;
                    tombol.classList.remove('btn-outline-secondary');
                    tombol.classList.add('btn-primary');
                    var blok = tombol.closest('[data-tts-blok]');
                    var info = blok && blok.querySelector('[data-tts-info]');
                    if (info) info.textContent = 'Klik "Putar suara" untuk mendengarkan.';
                };
                synth.speak(u);
            }

            if (didukung) {
                document.addEventListener('click', function (e) {
                    var tombol = e.target.closest('[data-suara]');
                    if (tombol) ucapkan(tombol.dataset.suara, tombol);
                });

                document.addEventListener('change', function (e) {
                    var sel = e.target.closest('[data-pilih-suara]');
                    if (!sel) return;
                    simpan = sel.value;
                    try { localStorage.setItem(KUNCI, simpan); } catch (err) {}
                    document.querySelectorAll('[data-pilih-suara]').forEach(function (s) { s.value = simpan; });
                    // Langsung diperdengarkan supaya suaranya bisa dinilai.
                    var tombol = sel.closest('[data-tts-blok]').querySelector('[data-suara]');
                    if (tombol) ucapkan(tombol.dataset.suara);
                });

                synth.addEventListener('voiceschanged', isiPilihan);
            }

            window.siapkanSuara = function () {
                var bloks = document.querySelectorAll('[data-tts-blok]');

                if (!didukung) {
                    bloks.forEach(function (b) {
                        b.innerHTML = '<span class="tts-info">Browser ini tidak mendukung suara bawaan.</span>';
                        b.hidden = false;
                    });
                    return;
                }

                if (!bloks.length) {
                    synth.cancel(); // pindah ke soal tanpa suara: hentikan suara soal sebelumnya
                    return;
                }

                isiPilihan();

                // Putar otomatis saat soal dibuka. Tunggu daftar suara siap (maks. 0,8 detik).
                var auto = document.querySelector('[data-suara-auto]');
                if (!auto) { synth.cancel(); return; }

                var sudahAuto = false;
                function putarOtomatis() {
                    if (sudahAuto) return;
                    sudahAuto = true;
                    synth.removeEventListener('voiceschanged', putarOtomatis);
                    ucapkan(auto.dataset.suara, auto);
                }

                if (synth.getVoices().length) {
                    putarOtomatis();
                } else {
                    synth.addEventListener('voiceschanged', putarOtomatis);
                    setTimeout(putarOtomatis, 800);
                }
            };
        })();
    </script>

    <script>
        // Pindah soal & kirim jawaban lewat AJAX: yang diganti hanya isi #area-soal, halaman tidak di-refresh.
        (function () {
            var mulai = Date.now();   // untuk mencatat lama menjawab per soal
            var timerId = null;
            var sibuk = false;

            function area() { return document.getElementById('area-soal'); }
            function formSoal() { return document.getElementById('form-soal'); }

            function tampilError(pesan) {
                var inner = area().querySelector('.page-inner');
                if (!inner) return;
                var lama = inner.querySelector('.alert-ajax');
                if (lama) lama.remove();
                var el = document.createElement('div');
                el.className = 'alert alert-danger alert-ajax';
                el.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>';
                el.appendChild(document.createTextNode(pesan));
                inner.insertBefore(el, inner.firstChild);
            }

            function tukar(html, url, dorong) {
                var doc  = new DOMParser().parseFromString(html, 'text/html');
                var baru = doc.getElementById('area-soal');

                if (!baru) {
                    // Server mengalihkan ke halaman lain (mis. hasil): tampilkan apa adanya.
                    history.replaceState(null, '', url);
                    document.open(); document.write(html); document.close();
                    return;
                }

                // Posisi scroll dipertahankan: tidak lompat ke atas saat soal diganti.
                var scrollY = window.scrollY;
                area().innerHTML = baru.innerHTML;
                if (doc.title) document.title = doc.title;
                if (dorong) history.pushState(null, '', url);
                window.scrollTo(0, scrollY);
                siapkan();
            }

            // Ambil halaman soal lalu tukar isinya.
            function muat(url, dorong) {
                return fetch(url, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function (r) {
                        if (!r.ok) throw new Error('Gagal memuat soal (' + r.status + ').');
                        return r.text().then(function (html) { tukar(html, r.url, dorong); });
                    });
            }

            function lakukan(janji) {
                if (sibuk) return;
                sibuk = true;
                area().classList.add('memuat');

                janji().catch(function (err) {
                    tampilError(err && err.message ? err.message : 'Terjadi kesalahan. Coba lagi.');
                }).then(function () {
                    sibuk = false;
                    area().classList.remove('memuat');
                });
            }

            // Simpan jawaban (POST JSON) lalu pindah / selesai.
            function kirim(fd) {
                fd.set('waktu_ms', Math.min(Date.now() - mulai, 3600000));

                lakukan(function () {
                    return fetch(formSoal().action, {
                        method: 'POST',
                        body: fd,
                        credentials: 'same-origin',
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    }).then(function (r) {
                        return r.json().then(function (d) { return { ok: r.ok, d: d }; }, function () {
                            throw new Error('Sesi bermasalah. Muat ulang halaman lalu coba lagi.');
                        });
                    }).then(function (x) {
                        if (!x.ok) {
                            var pesan = x.d.message || 'Gagal menyimpan jawaban.';
                            if (x.d.errors) pesan = Object.values(x.d.errors)[0][0];
                            throw new Error(pesan);
                        }
                        if (x.d.halaman === 'hasil') {
                            window.location.href = x.d.redirect;
                            return;
                        }
                        return muat(x.d.redirect, true);
                    });
                });
            }

            // ---- Event (delegasi, dipasang sekali) ----

            document.addEventListener('click', function (e) {
                // Konfirmasi sebelum mengakhiri. Pilihan yang baru dipilih (belum tersimpan) tidak dihitung "belum dijawab".
                var akhiri = e.target.closest('[data-akhiri]');
                if (!akhiri) return;

                var form  = formSoal();
                var belum = parseInt(akhiri.dataset.belum, 10) || 0;
                var pilih = form.querySelector('input[name="jawaban"]:checked');
                if (pilih && !pilih.defaultChecked) belum = Math.max(0, belum - 1);

                // Tanda ragu-ragu pada soal yang sedang tampil belum tersimpan: hitung selisihnya.
                var ragu = parseInt(akhiri.dataset.ragu, 10) || 0;
                var inputRagu = document.getElementById('ragu');
                if (inputRagu && inputRagu.value !== inputRagu.dataset.awal) ragu += inputRagu.value === '1' ? 1 : -1;
                ragu = Math.max(0, ragu);

                var catatan = [];
                if (belum > 0) catatan.push(belum + ' soal belum dijawab');
                if (ragu > 0) catatan.push(ragu + ' soal masih ditandai ragu-ragu');

                var pesan = catatan.length
                    ? 'Masih ada ' + catatan.join(' dan ') + '. Akhiri sekarang?'
                    : 'Akhiri dan lihat hasil?';

                if (!confirm(pesan)) e.preventDefault();
            });

            // Tandai ragu-ragu: hanya mengubah tampilan dan field tersembunyi; tersimpan bersama jawaban saat pindah soal.
            function toggleRagu() {
                var input  = document.getElementById('ragu');
                var tombol = document.querySelector('[data-ragu-toggle]');
                if (!input || !tombol) return;

                var aktif = input.value !== '1';
                input.value = aktif ? '1' : '0';
                tombol.setAttribute('aria-pressed', aktif ? 'true' : 'false');
                tombol.querySelector('[data-ragu-teks]').textContent = aktif ? 'Ditandai ragu-ragu' : 'Tandai ragu-ragu';

                var kartu = document.getElementById('kartu-soal');
                if (kartu) kartu.classList.toggle('ragu-aktif', aktif);

                var sekarang = document.querySelector('.nav-no[aria-current]');
                if (sekarang) sekarang.classList.toggle('ragu', aktif);
            }

            document.addEventListener('click', function (e) {
                if (e.target.closest('[data-ragu-toggle]')) toggleRagu();
            });

            document.addEventListener('submit', function (e) {
                var form = e.target;
                if (form.id !== 'form-soal') return;
                e.preventDefault();

                var fd = new FormData(form, e.submitter || undefined);
                if (!fd.get('ke')) return;
                kirim(fd);
            });

            // Pintasan keyboard: A-D memilih, Enter simpan & berikutnya, panah kiri/kanan pindah soal.
            document.addEventListener('keydown', function (e) {
                if (e.ctrlKey || e.metaKey || e.altKey || e.isComposing) return;

                var form = formSoal();
                if (!form || sibuk) return;

                var t = e.target;
                var radio = t.matches && t.matches('input[type="radio"]');
                if (t.matches && t.matches('select, textarea, button, a, input:not([type="radio"])')) return;

                var k = e.key;

                if (/^[a-d]$/i.test(k)) {
                    var r = form.querySelector('input[name="jawaban"][value="' + k.toUpperCase() + '"]');
                    if (r) { r.checked = true; r.dispatchEvent(new Event('change', { bubbles: true })); }
                    return;
                }

                if (/^r$/i.test(k)) { toggleRagu(); return; }

                var tombol = null;
                if (k === 'Enter') tombol = form.querySelector('[data-next]');
                if (k === 'ArrowRight') tombol = form.querySelector('[data-next]');
                if (k === 'ArrowLeft') tombol = form.querySelector('[data-prev]');

                // Enter di radio akan men-submit tombol pertama (bisa "Sebelumnya"): selalu dialihkan.
                if (k === 'Enter' || (radio && (k === 'ArrowLeft' || k === 'ArrowRight'))) e.preventDefault();
                if (tombol) tombol.click();
            });

            // Tombol back/forward browser.
            window.addEventListener('popstate', function () {
                lakukan(function () { return muat(location.href, false); });
            });

            // ---- Inisialisasi per soal (dipanggil saat load dan setelah tiap pergantian soal) ----

            function siapkan() {
                mulai = Date.now();
                mulaiTimer();
                if (window.siapkanSuara) window.siapkanSuara();
            }

            // Hitung mundur. Waktu habis -> kirim dengan tujuan "selesai" (server tetap memverifikasi batas waktu).
            function mulaiTimer() {
                if (timerId) { clearInterval(timerId); timerId = null; }

                var timer = document.getElementById('timer');
                if (!timer) return;

                var teks  = document.getElementById('timer-teks');
                var batas = Date.now() + parseInt(timer.dataset.sisa, 10) * 1000;
                var habis = false;

                function dua(n) { return n < 10 ? '0' + n : '' + n; }

                function tick() {
                    var s = Math.max(0, Math.round((batas - Date.now()) / 1000));
                    var jam = Math.floor(s / 3600), mnt = Math.floor((s % 3600) / 60), dtk = s % 60;
                    teks.textContent = (jam > 0 ? jam + ':' + dua(mnt) : mnt) + ':' + dua(dtk);
                    timer.classList.toggle('mepet', s <= 60);

                    if (s === 0 && !habis) {
                        habis = true;
                        clearInterval(timerId);
                        var fd = new FormData(formSoal());
                        fd.set('ke', 'selesai');
                        kirim(fd);
                    }
                }

                tick();
                timerId = setInterval(tick, 1000);
            }

            siapkan();
        })();
    </script>
@endsection
