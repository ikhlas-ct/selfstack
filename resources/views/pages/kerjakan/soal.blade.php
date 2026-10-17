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
    .bar-waktu { position: sticky; top: 8px; z-index: 5; background: #fff; border: 1px solid #e9ecef; border-radius: 14px; padding: 10px 16px 12px; margin-bottom: 1.25rem; box-shadow: 0 2px 10px rgba(0,0,0,.07); }
    .bar-info { display: flex; align-items: center; justify-content: space-between; gap: 12px; font-size: .84rem; font-weight: 700; color: var(--gk-ink); }
    .bar-info .timer { font-variant-numeric: tabular-nums; font-size: 1rem; color: var(--gk-blue); display: inline-flex; align-items: center; gap: 6px; }
    .bar-info .timer.mepet { color: #dc2626; }
    .bar-prog { height: 6px; border-radius: 999px; background: #e2e8f0; margin-top: 8px; overflow: hidden; }
    .bar-prog > div { height: 100%; background: var(--gk-blue); border-radius: 999px; }

    .soal-card-body { padding: 22px; }
    .soal-head { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 14px; }
    .soal-no { min-width: 26px; height: 26px; padding: 0 6px; border-radius: 13px; background: #e2e8f0; color: #475569; font-size: .74rem; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .soal-paragraf { font-family: var(--gk-hanzi); font-size: 1rem; line-height: 1.8; color: #475569; background: var(--gk-soft); border-radius: 10px; padding: 12px 14px; margin-bottom: 14px; }
    .soal-tanya { font-family: var(--gk-hanzi); font-size: 1.2rem; font-weight: 600; color: var(--gk-ink); margin-bottom: 18px; line-height: 1.6; }
    .soal-audio { margin-bottom: 16px; display: flex; align-items: center; gap: 10px; flex-wrap: wrap; font-size: .78rem; color: var(--gk-muted); }
    .soal-audio audio { width: 100%; height: 36px; }

    .opsi-list { display: grid; gap: 10px; }
    .opsi-pilih { position: relative; display: flex; gap: 12px; align-items: flex-start; margin: 0; padding: 12px 14px; background: #fff; border: 1.5px solid var(--gk-line); border-radius: 12px; cursor: pointer; font-family: var(--gk-hanzi); font-size: 1rem; line-height: 1.5; color: #334155; transition: border-color .15s, background .15s; }
    .opsi-pilih:hover { border-color: #93c5fd; }
    .opsi-pilih input { position: absolute; opacity: 0; pointer-events: none; }
    .opsi-pilih .huruf { width: 28px; height: 28px; border-radius: 50%; background: #f1f5f9; color: var(--gk-muted); font-family: 'Plus Jakarta Sans', sans-serif; font-weight: 700; font-size: .8rem; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .opsi-pilih input:checked ~ .huruf { background: var(--gk-blue); color: #fff; }
    .opsi-pilih:has(input:checked) { border-color: var(--gk-blue); background: #e8f0fe; }
    .opsi-pilih:has(input:focus-visible) { outline: 3px solid rgba(26,115,232,.35); outline-offset: 2px; }
    .opsi-pilih .tanda { margin-left: auto; flex-shrink: 0; margin-top: 4px; }
    .opsi-pilih.terkunci { cursor: default; }
    .opsi-pilih.terkunci:hover { border-color: var(--gk-line); }
    .opsi-pilih.benar, .opsi-pilih.benar:has(input:checked) { background: #dcfce7; border-color: #4ade80; color: #14532d; }
    .opsi-pilih.benar .huruf, .opsi-pilih.benar input:checked ~ .huruf { background: #16a34a; color: #fff; }
    .opsi-pilih.benar .tanda { color: #15803d; }
    .opsi-pilih.salah, .opsi-pilih.salah:has(input:checked) { background: #fee2e2; border-color: #f87171; color: #7f1d1d; }
    .opsi-pilih.salah .huruf, .opsi-pilih.salah input:checked ~ .huruf { background: #dc2626; color: #fff; }
    .opsi-pilih.salah .tanda { color: #b91c1c; }

    .umpan { border-radius: 12px; padding: 12px 14px; margin-top: 16px; font-size: .84rem; }
    .umpan.benar { background: #dcfce7; color: #14532d; }
    .umpan.salah { background: #fee2e2; color: #7f1d1d; }
    .umpan .jelas { margin-top: 6px; color: #334155; }
    .soal-aksi { display: flex; gap: 8px; flex-wrap: wrap; justify-content: space-between; align-items: center; margin-top: 20px; padding-top: 16px; border-top: 1px dashed #dbe3ec; }
    .soal-aksi .kanan { display: flex; gap: 8px; flex-wrap: wrap; margin-left: auto; }
    .btn.disabled-look { opacity: .45; pointer-events: none; }

    .nav-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(40px, 1fr)); gap: 6px; }
    .nav-no { height: 40px; border-radius: 10px; border: 1.5px solid var(--gk-line); background: #fff; color: #475569; font-size: .8rem; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; padding: 0; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; }
    .nav-no:hover { border-color: #93c5fd; color: var(--gk-blue); }
    .nav-no.terjawab { background: #e8f0fe; border-color: #93c5fd; color: var(--gk-blue); }
    .nav-no.benar { background: #dcfce7; border-color: #86efac; color: #15803d; }
    .nav-no.salah { background: #fee2e2; border-color: #fca5a5; color: #b91c1c; }
    .nav-no.sekarang { background: var(--gk-ink); border-color: var(--gk-ink); color: #fff; }
    .nav-no:focus-visible { outline: 3px solid rgba(26,115,232,.35); outline-offset: 2px; }
    .nav-legend { display: flex; flex-wrap: wrap; gap: 4px 12px; margin-top: 12px; font-size: .72rem; color: var(--gk-muted); }
    .nav-legend span::before { content: ''; display: inline-block; width: 10px; height: 10px; border-radius: 3px; margin-right: 5px; vertical-align: -1px; border: 1.5px solid var(--gk-line); background: #fff; }
    .nav-legend .l-jawab::before { background: #e8f0fe; border-color: #93c5fd; }
    .nav-legend .l-benar::before { background: #dcfce7; border-color: #86efac; }
    .nav-legend .l-salah::before { background: #fee2e2; border-color: #fca5a5; }

    /* ===== SUARA (TEXT-TO-SPEECH) ===== */
    .tts-bar { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; width: 100%; }
    .tts-bar[hidden] { display: none; }
    .tts-bar .form-select { flex: 1 1 220px; max-width: 380px; font-size: .78rem; padding: 5px 30px 5px 10px; }
    .tts-info { font-size: .74rem; color: var(--gk-faint); }

@media (prefers-reduced-motion: reduce) {
        .btn-primary, .grup-card, .form-control, .opsi-pilih { transition: none; }
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
    @endphp

    @php
        $sudahDijawab = $jawabanSaatIni?->jawaban_dipilih;
        $akhir        = $no === $total;
        $kataAkhiri   = $latihan ? 'latihan' : 'ujian';
    @endphp

    <div class="container">

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
                    <span>Soal {{ $no }} dari {{ $total }}</span>
                    @if ($sisaDetik !== null)
                        <span class="timer" id="timer" data-sisa="{{ $sisaDetik }}" role="timer" aria-label="Sisa waktu">
                            <i class="fas fa-clock"></i><span id="timer-teks">--:--</span>
                        </span>
                    @endif
                </div>
                <div class="bar-prog" aria-hidden="true"><div style="width: {{ round($no / $total * 100) }}%"></div></div>
            </div>

            {{-- Satu form untuk soal + navigasi: semua tombol ber-name="ke" menyimpan jawaban lalu pindah --}}
            <form method="POST" action="{{ route('admin.kerjakan.jawab', $hasil) }}" id="form-soal">
                @csrf
                <input type="hidden" name="soal_id" value="{{ $soal->id }}">
                <input type="hidden" name="no" value="{{ $no }}">
                <input type="hidden" name="waktu_ms" id="waktu_ms" value="0">

                <div class="row g-4">

                    {{-- ===== Kiri: soal ===== --}}
                    <div class="col-lg-8">
                        <div class="card form-card">
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
                                    @endphp
                                    <div class="soal-audio" data-audio-blok>
                                        @if ($srcAudio)
                                            <audio controls preload="metadata" src="{{ $srcAudio }}" data-audio-file></audio>
                                        @endif

                                        @if ($teksSuara)
                                            {{-- Tanpa file audio (atau file gagal dimuat) suara dibacakan browser lewat text-to-speech --}}
                                            <div class="tts-bar" data-tts-blok @if ($srcAudio) hidden @endif>
                                                <button type="button" class="btn btn-outline-secondary btn-sm" data-suara="{{ $teksSuara }}">
                                                    <i class="fas fa-volume-up me-1"></i> Putar suara
                                                </button>
                                                <select class="form-select form-select-sm" data-pilih-suara aria-label="Pilih suara">
                                                    <option value="">(Otomatis — pilih zh-TW kalau ada)</option>
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
                                        @php
                                            $dipilih = $sudahDijawab === $huruf;
                                            $kelas   = '';
                                            if ($terkunci) {
                                                if ($huruf === $soal->jawaban_benar) {
                                                    $kelas = 'benar';
                                                } elseif ($dipilih) {
                                                    $kelas = 'salah';
                                                }
                                            }
                                        @endphp
                                        <label class="opsi-pilih {{ $kelas }} {{ $terkunci ? 'terkunci' : '' }}">
                                            <input type="radio" name="jawaban" value="{{ $huruf }}"
                                                   @checked($dipilih) @disabled($terkunci) @required($latihan && ! $terkunci)>
                                            <span class="huruf">{{ $huruf }}</span>
                                            <span>{{ $teks }}</span>
                                            @if ($kelas === 'benar')
                                                <i class="fas fa-check-circle tanda" title="Jawaban benar"></i>
                                            @elseif ($kelas === 'salah')
                                                <i class="fas fa-times-circle tanda" title="Jawabanmu"></i>
                                            @endif
                                        </label>
                                    @endforeach
                                </div>

                                {{-- Umpan balik: hanya pada latihan, setelah jawaban diperiksa --}}
                                @if ($terkunci)
                                    @php $benar = $jawabanSaatIni->benar; @endphp
                                    <div class="umpan {{ $benar ? 'benar' : 'salah' }}" role="status">
                                        @if ($benar)
                                            <strong><i class="fas fa-check-circle me-1"></i>Benar.</strong>
                                        @else
                                            <strong><i class="fas fa-times-circle me-1"></i>Kurang tepat.</strong>
                                            Jawaban yang benar adalah {{ $soal->jawaban_benar }}.
                                        @endif
                                        @if ($soal->penjelasan)
                                            <div class="jelas">{!! nl2br(e(strip_tags($soal->penjelasan))) !!}</div>
                                        @endif
                                    </div>
                                @endif

                                {{-- ===== Tombol aksi ===== --}}
                                <div class="soal-aksi">
                                    @if ($latihan)
                                        {{-- Latihan: navigasi lewat link biasa, hanya "Periksa" yang menyimpan jawaban --}}
                                        @if ($no > 1)
                                            <a href="{{ route('admin.kerjakan.soal', [$hasil, 'no' => $no - 1]) }}" class="btn btn-outline-secondary">
                                                <i class="fas fa-arrow-left me-1"></i> Sebelumnya
                                            </a>
                                        @else
                                            <span></span>
                                        @endif

                                        <div class="kanan">
                                            @if (! $terkunci)
                                                <button type="submit" name="ke" value="{{ $no }}" class="btn btn-primary">
                                                    <i class="fas fa-check me-1"></i> Periksa jawaban
                                                </button>
                                                @if (! $akhir)
                                                    <a href="{{ route('admin.kerjakan.soal', [$hasil, 'no' => $no + 1]) }}" class="btn btn-outline-secondary">
                                                        Lewati <i class="fas fa-arrow-right ms-1"></i>
                                                    </a>
                                                @endif
                                            @elseif (! $akhir)
                                                <a href="{{ route('admin.kerjakan.soal', [$hasil, 'no' => $no + 1]) }}" class="btn btn-primary">
                                                    Berikutnya <i class="fas fa-arrow-right ms-1"></i>
                                                </a>
                                            @endif

                                            @if ($akhir)
                                                <button type="submit" name="ke" value="selesai" class="btn btn-success" data-akhiri data-belum="{{ $belumDijawab }}" formnovalidate>
                                                    <i class="fas fa-flag-checkered me-1"></i> Selesai &amp; lihat hasil
                                                </button>
                                            @endif
                                        </div>
                                    @else
                                        {{-- Ujian: setiap tombol menyimpan pilihan saat ini sebelum pindah --}}
                                        @if ($no > 1)
                                            <button type="submit" name="ke" value="{{ $no - 1 }}" class="btn btn-outline-secondary" formnovalidate>
                                                <i class="fas fa-arrow-left me-1"></i> Sebelumnya
                                            </button>
                                        @else
                                            <span></span>
                                        @endif

                                        <div class="kanan">
                                            @if (! $akhir)
                                                <button type="submit" name="ke" value="{{ $no + 1 }}" class="btn btn-primary" formnovalidate>
                                                    Simpan &amp; berikutnya <i class="fas fa-arrow-right ms-1"></i>
                                                </button>
                                            @else
                                                <button type="submit" name="ke" value="selesai" class="btn btn-success" data-akhiri data-belum="{{ $belumDijawab }}" formnovalidate>
                                                    <i class="fas fa-flag-checkered me-1"></i> Simpan &amp; selesai
                                                </button>
                                            @endif
                                        </div>
                                    @endif
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
                                        <span class="badge badge-pilih">{{ $total - $belumDijawab }}/{{ $total }}</span>
                                    </div>

                                    <div class="nav-grid">
                                        @foreach ($nav as $item)
                                            @php
                                                $kelasNav = $item['no'] === $no ? 'sekarang' : $item['status'];
                                                $labelNav = 'Soal ' . $item['no'] . ($item['status'] === 'kosong' ? ', belum dijawab' : ', sudah dijawab');
                                            @endphp
                                            @if ($latihan)
                                                <a href="{{ route('admin.kerjakan.soal', [$hasil, 'no' => $item['no']]) }}"
                                                   class="nav-no {{ $kelasNav }}" aria-label="{{ $labelNav }}"
                                                   @if ($item['no'] === $no) aria-current="true" @endif>{{ $item['no'] }}</a>
                                            @else
                                                <button type="submit" name="ke" value="{{ $item['no'] }}" formnovalidate
                                                        class="nav-no {{ $kelasNav }}" aria-label="{{ $labelNav }}"
                                                        @if ($item['no'] === $no) aria-current="true" @endif>{{ $item['no'] }}</button>
                                            @endif
                                        @endforeach
                                    </div>

                                    <div class="nav-legend">
                                        @if ($latihan)
                                            <span class="l-benar">Benar</span>
                                            <span class="l-salah">Salah</span>
                                        @else
                                            <span class="l-jawab">Sudah dijawab</span>
                                        @endif
                                        <span>Belum dijawab</span>
                                    </div>

                                    <hr class="my-3">

                                    <button type="submit" name="ke" value="selesai" class="btn btn-outline-danger w-100"
                                            data-akhiri data-belum="{{ $belumDijawab }}" formnovalidate>
                                        <i class="fas fa-flag-checkered me-1"></i> Akhiri {{ $kataAkhiri }}
                                    </button>
                                    <div class="help-text">
                                        @if ($latihan)
                                            Soal yang belum diperiksa dihitung dilewati.
                                        @else
                                            Pilihan di soal yang sedang tampil ikut tersimpan. Soal yang belum dijawab dihitung salah.
                                        @endif
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
        (function () {
            var bloks = document.querySelectorAll('[data-tts-blok]');
            var KUNCI = 'kerjakan_suara';

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

            if (!bloks.length) return;

            if (!('speechSynthesis' in window)) {
                bloks.forEach(function (b) {
                    b.innerHTML = '<span class="tts-info">Browser ini tidak mendukung suara bawaan.</span>';
                    b.hidden = false;
                });
                return;
            }

            var synth  = window.speechSynthesis;
            var simpan = '';
            try { simpan = localStorage.getItem(KUNCI) || ''; } catch (e) {}

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

            // Otomatis: zh-TW kalau ada, lalu zh apa pun, terakhir suara bawaan dengan lang zh-TW.
            function suaraTerpilih() {
                var suara = synth.getVoices();
                if (simpan) {
                    var dipilih = suara.find(function (v) { return v.name === simpan; });
                    if (dipilih) return dipilih;
                }
                return suara.find(function (v) { return /^zh[-_]TW$/i.test(v.lang); })
                    || suara.find(function (v) { return /^zh/i.test(v.lang); })
                    || null;
            }

            function ucapkan(teks) {
                synth.cancel();
                var u = new SpeechSynthesisUtterance(teks);
                var v = suaraTerpilih();
                if (v) { u.voice = v; u.lang = v.lang; } else { u.lang = 'zh-TW'; }
                u.rate = 0.85;
                synth.speak(u);
            }

            document.addEventListener('click', function (e) {
                var tombol = e.target.closest('[data-suara]');
                if (tombol) ucapkan(tombol.dataset.suara);
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
            isiPilihan();
        })();
    </script>

    <script>
        (function () {
            var form  = document.getElementById('form-soal');
            var waktu = document.getElementById('waktu_ms');
            var mulai = Date.now();

            function catatWaktu() {
                waktu.value = Math.min(Date.now() - mulai, 3600000);
            }

            form.addEventListener('submit', catatWaktu);

            // Konfirmasi sebelum mengakhiri. Pilihan yang baru dipilih (belum tersimpan) tidak dihitung "belum dijawab".
            form.querySelectorAll('[data-akhiri]').forEach(function (tombol) {
                tombol.addEventListener('click', function (e) {
                    var belum = parseInt(tombol.dataset.belum, 10) || 0;
                    var pilih = form.querySelector('input[name="jawaban"]:checked');
                    if (pilih && !pilih.defaultChecked && !pilih.disabled) belum = Math.max(0, belum - 1);

                    var pesan = belum > 0
                        ? 'Masih ada ' + belum + ' soal yang belum dijawab. Akhiri sekarang?'
                        : 'Akhiri dan lihat hasil?';

                    if (!confirm(pesan)) e.preventDefault();
                });
            });

            // Hitung mundur. Waktu habis -> kirim form dengan tujuan "selesai" (server tetap memverifikasi batas waktu).
            var timer = document.getElementById('timer');
            if (!timer) return;

            var teks  = document.getElementById('timer-teks');
            var batas = Date.now() + parseInt(timer.dataset.sisa, 10) * 1000;
            var kirim = false;

            function dua(n) { return n < 10 ? '0' + n : '' + n; }

            function tick() {
                var s = Math.max(0, Math.round((batas - Date.now()) / 1000));
                var jam = Math.floor(s / 3600), mnt = Math.floor((s % 3600) / 60), dtk = s % 60;
                teks.textContent = (jam > 0 ? jam + ':' + dua(mnt) : mnt) + ':' + dua(dtk);
                timer.classList.toggle('mepet', s <= 60);

                if (s === 0 && !kirim) {
                    kirim = true;
                    var ke = document.createElement('input');
                    ke.type = 'hidden'; ke.name = 'ke'; ke.value = 'selesai';
                    form.appendChild(ke);
                    catatWaktu();
                    form.submit();
                }
            }

            tick();
            setInterval(tick, 1000);
        })();
    </script>
@endsection
