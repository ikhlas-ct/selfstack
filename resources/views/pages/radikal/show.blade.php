@extends('layouts.user.user')

@section('title', 'Detail Radikal')

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

        /* ===== CARD, FILTER, FORM ===== */
        .filter-card { border: none; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,.07); overflow: hidden; }
        .filter-card .card-header { background: #fff; border-bottom: 1px solid #f1f5f9; padding: 18px 24px; }
        .filter-card .card-header h5 { font-size: .95rem; font-weight: 700; color: #1e293b; }
        .form-card { border: none; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,.07); }
        .section-divider { background: #f8f9fa; border-left: 4px solid #1269db; padding: 8px 14px; border-radius: 0 6px 6px 0; font-weight: 600; font-size: .9rem; color: #1269db; margin-bottom: 1rem; }
        .form-control, .form-select { border-radius: 10px; border: 1.5px solid #e2e8f0; font-size: .83rem; padding: 7px 12px; color: #334155; background-color: #f8fafc; transition: border-color .2s, box-shadow .2s; }
        .form-control:focus, .form-select:focus { border-color: #1a73e8; background: #fff; box-shadow: 0 0 0 3px rgba(26,115,232,.12); }

        /* ===== TABLE ===== */
        .table thead th { background: #f8fafc; color: #64748b; font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; padding: 12px 16px; border-bottom: 2px solid #e2e8f0; border-top: none; white-space: nowrap; }
        .table tbody td { padding: 13px 16px; vertical-align: middle; font-size: .85rem; color: #334155; border-bottom: 1px solid #f1f5f9; }
        .table tbody tr:last-child td { border-bottom: none; }
        .table-hover tbody tr:hover td { background: #f8fafc; }
        .hanzi-text { font-size: 1.25rem; font-weight: 600; color: #1e293b; font-family: 'Noto Sans TC', 'PingFang TC', 'Microsoft JhengHei', 'Plus Jakarta Sans', sans-serif; }
        .hanzi-besar { font-size: 3rem; font-weight: 600; color: #1e293b; line-height: 1.2; font-family: 'Noto Sans TC', 'PingFang TC', 'Microsoft JhengHei', 'Plus Jakarta Sans', sans-serif; }

        /* ===== BADGES ===== */
        .badge { font-size: .7rem; font-weight: 600; padding: 4px 9px; border-radius: 6px; letter-spacing: .2px; }
        .badge-aktif { background: #dcfce7; color: #15803d; }
        .badge-nonaktif { background: #f1f5f9; color: #64748b; }
        .badge-level { background: #e8f0fe; color: #1a73e8; }
        .badge-warn { background: #fff7ed; color: #c2410c; }
        .chip-varian { display: inline-block; font-size: 1.05rem; line-height: 1; padding: 3px 7px; margin: 0 4px 2px 0; border-radius: 7px; background: #f5f3ff; color: #7c3aed; font-family: 'Noto Sans TC', 'PingFang TC', 'Microsoft JhengHei', sans-serif; }

        /* ===== ACTION BUTTONS ===== */
        .btn-primary { background: linear-gradient(135deg, #1a73e8, #1558b0); border: none; border-radius: 10px; font-weight: 600; font-size: .83rem; padding: 8px 18px; box-shadow: 0 2px 8px rgba(26,115,232,.35); transition: all .2s ease; }
        .btn-primary:hover { background: linear-gradient(135deg, #1558b0, #0f3e82); box-shadow: 0 4px 14px rgba(26,115,232,.45); transform: translateY(-1px); }
        .btn-outline-secondary { border-radius: 10px; font-size: .83rem; border-color: #e2e8f0; color: #64748b; padding: 7px 12px; }
        .btn-outline-secondary:hover { background: #f1f5f9; border-color: #cbd5e1; color: #334155; }

        /* ===== INFO ITEM ===== */
        .info-item { padding: 12px 0; border-bottom: 1px solid #f1f5f9; }
        .info-item:last-child { border-bottom: none; padding-bottom: 0; }
        .info-label { font-size: .72rem; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: #94a3b8; }
        .info-value { font-size: .88rem; font-weight: 500; color: #1e293b; margin-top: 2px; word-break: break-word; }

        /* ===== GORESAN & SUARA ===== */
        .goresan-area { display: flex; flex-wrap: wrap; gap: 14px; }
        .goresan-item { text-align: center; }
        .goresan-cap { font-size: .72rem; font-weight: 600; color: #94a3b8; margin-top: 4px; text-transform: uppercase; letter-spacing: .4px; }
        .goresan-kotak { width: 160px; height: 160px; border: 1.5px solid #e2e8f0; border-radius: 14px; background: #fff; position: relative; cursor: pointer; transition: border-color .2s, box-shadow .2s; }
        .goresan-kotak:hover { border-color: #1a73e8; box-shadow: 0 2px 10px rgba(26,115,232,.15); }
        .goresan-kotak::before, .goresan-kotak::after { content: ''; position: absolute; background: repeating-linear-gradient(90deg, #e2e8f0 0 4px, transparent 4px 8px); pointer-events: none; }
        .goresan-kotak::before { left: 0; right: 0; top: 50%; height: 1px; }
        .goresan-kotak::after { top: 0; bottom: 0; left: 50%; width: 1px; background: repeating-linear-gradient(0deg, #e2e8f0 0 4px, transparent 4px 8px); }
        .goresan-kotak svg { position: relative; z-index: 1; }
        .goresan-info { font-size: .78rem; color: #64748b; margin-top: 10px; }
        .goresan-info.galat { color: #b45309; }

        /* ===== EMPTY STATE ===== */
        .empty-state { padding: 60px 20px; }
        .empty-state-icon { width: 72px; height: 72px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 1.6rem; color: #94a3b8; }

        /* ===== SHOW ===== */
        .scroll-karakter { max-height: 480px; overflow-y: auto; }
    </style>
@endsection

@section('content')
    @php
        $progres = $radikal->progres;
        $labelStatus = $progres ? \Illuminate\Support\Str::headline($progres->status) : null;
        $warnaStatus = match ($progres?->status) {
            'ingat_sepenuhnya' => 'badge-aktif',
            'lupa_dan_ingat'   => 'badge-warn',
            default            => 'badge-level',
        };
    @endphp

    <div class="container">

        <div class="ph-card show-page">
            <div class="ph-left">
                <div class="ph-icon show"><i class="fas fa-cubes"></i></div>
                <div>
                    <h5 class="ph-title">{{ $radikal->hanzi }} &nbsp;<span class="text-muted fw-normal">{{ $radikal->pinyin }}</span></h5>
                    <ol class="ph-breadcrumb" aria-label="breadcrumb">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><a href="{{ route('admin.radikal.index') }}">Radikal</a></li>
                        <li><span class="bc-active">Detail</span></li>
                    </ol>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.radikal.edit', $radikal) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-pencil-alt me-1"></i> Edit
                </a>
                <a href="{{ route('admin.radikal.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        <div class="page-inner">

            {{-- Goresan & suara --}}
            <div class="card form-card mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                        <div class="section-divider mb-0"><i class="fas fa-pen-nib me-2"></i>Urutan Goresan &amp; Suara</div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-primary btn-sm" id="btn-animasi">
                                <i class="fas fa-play me-1"></i> Animasi
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="btn-latihan">
                                <i class="fas fa-pencil-alt me-1"></i> Latihan menulis
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm btn-speak-utama" data-laju="0.9">
                                <i class="fas fa-volume-up me-1"></i> Dengar
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm btn-speak-utama" data-laju="0.55">
                                <i class="fas fa-volume-down me-1"></i> Pelan
                            </button>
                            <select id="pilih-suara" class="form-select form-select-sm d-none" style="width:auto;max-width:270px;border-radius:10px;font-size:.8rem;" title="Pilih suara"></select>
                        </div>
                    </div>

                    <div id="area-goresan" class="goresan-area"></div>
                    <div id="info-goresan" class="goresan-info">Klik kotak huruf untuk memutar animasi satu karakter.</div>
                    <div id="info-suara" class="goresan-info galat d-none"></div>
                </div>
            </div>

            <div class="row g-4">

                {{-- Info radikal --}}
                <div class="col-lg-4">
                    <div class="card form-card mb-4">
                        <div class="card-body">
                            <div class="section-divider"><i class="fas fa-info-circle me-2"></i>Informasi Radikal</div>

                            <div class="info-item">
                                <div class="hanzi-besar">{{ $radikal->hanzi }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Nomor Kangxi</div>
                                <div class="info-value">{{ $radikal->nomor_kangxi ?? '-' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Varian</div>
                                <div class="info-value">
                                    @forelse ($radikal->varian ?? [] as $v)
                                        <span class="chip-varian">{{ $v }}</span>
                                    @empty
                                        -
                                    @endforelse
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Pinyin</div>
                                <div class="info-value">{{ $radikal->pinyin }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Arti Indonesia</div>
                                <div class="info-value">{{ $radikal->arti_indonesia }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Jumlah Goresan</div>
                                <div class="info-value">{{ $radikal->jumlah_goresan ?? '-' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Keterangan</div>
                                <div class="info-value" style="white-space:pre-line;">{{ $radikal->keterangan ?: '-' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Urutan Belajar</div>
                                <div class="info-value">{{ $radikal->urutan }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Soal Terkait</div>
                                <div class="info-value">{{ $radikal->soals_count }} soal</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Dibuat</div>
                                <div class="info-value">{{ $radikal->created_at?->translatedFormat('d F Y, H:i') ?? '-' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Terakhir Diubah</div>
                                <div class="info-value">{{ $radikal->updated_at?->translatedFormat('d F Y, H:i') ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="card form-card mb-4">
                        <div class="card-body">
                            <div class="section-divider"><i class="fas fa-brain me-2"></i>Progres Hafalan</div>
                            @if ($progres)
                                <div class="info-item">
                                    <div class="info-label">Status</div>
                                    <div class="info-value"><span class="badge {{ $warnaStatus }}">{{ $labelStatus }}</span></div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Diulang / Salah</div>
                                    <div class="info-value">{{ $progres->jumlah_ulang }}x diulang &middot; {{ $progres->jumlah_salah }}x salah</div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Review Berikutnya</div>
                                    <div class="info-value">
                                        {{ $progres->review_berikutnya ? \Illuminate\Support\Carbon::parse($progres->review_berikutnya)->translatedFormat('d F Y, H:i') : '-' }}
                                        @if ($progres->ditangguhkan)
                                            <span class="badge badge-nonaktif ms-1">Ditangguhkan</span>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <span class="text-muted" style="font-size:.83rem;">Radikal ini belum pernah dipelajari.</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Karakter yang memakai radikal ini --}}
                <div class="col-lg-8">
                    <div class="card filter-card shadow-sm">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="mb-0">
                                <i class="fas fa-language text-primary me-2 opacity-75"></i>Karakter dengan Radikal Ini
                                <span class="badge {{ $radikal->karakters->count() ? 'badge-aktif' : 'badge-nonaktif' }} ms-1">
                                    {{ $radikal->karakters->count() }}
                                </span>
                            </h5>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive scroll-karakter">
                                <table class="table-hover mb-0 table">
                                    <thead>
                                        <tr>
                                            <th width="40">#</th>
                                            <th>Karakter</th>
                                            <th>Pinyin</th>
                                            <th>Arti</th>
                                            <th>Bentuk</th>
                                            <th>Goresan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($radikal->karakters as $i => $k)
                                            <tr>
                                                <td class="text-muted">{{ $i + 1 }}</td>
                                                <td>
                                                    {{-- Link aktif otomatis setelah CRUD Karakter (route admin.karakter.show) ada --}}
                                                    @if (\Illuminate\Support\Facades\Route::has('admin.karakter.show'))
                                                        <a href="{{ route('admin.karakter.show', $k->id) }}" class="text-decoration-none"><span class="hanzi-text">{{ $k->hanzi }}</span></a>
                                                    @else
                                                        <span class="hanzi-text">{{ $k->hanzi }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $k->pinyin }}</td>
                                                <td>{{ $k->arti_indonesia }}</td>
                                                <td>
                                                    @if ($k->bentuk_radikal)
                                                        <span class="chip-varian">{{ $k->bentuk_radikal }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td class="text-muted">{{ $k->jumlah_goresan ?? '-' }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="p-0 text-center">
                                                    <div class="empty-state">
                                                        <div class="empty-state-icon"><i class="fas fa-language"></i></div>
                                                        <div class="fw-semibold text-secondary mb-1">Belum ada karakter dengan radikal ini</div>
                                                        <div class="text-muted" style="font-size:.8rem;">
                                                            Tautan dibuat dari data karakter (kolom radikal)
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>{{-- end .page-inner --}}
    </div>{{-- end .container --}}
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/hanzi-writer@3.7.3/dist/hanzi-writer.min.js"></script>
    <script>
        (function () {
            const UTAMA  = @json($radikal->hanzi);
            const VARIAN = @json(array_values($radikal->varian ?? []));

            /* =============== GORESAN (radikal utama + tiap varian) =============== */
            const area = document.getElementById('area-goresan');
            const infoGoresan = document.getElementById('info-goresan');
            const daftar = [];
            let latihanAktif = false;
            const adaHan = c => /\p{Script=Han}/u.test(c);
            const sudah = new Set();

            function tambahKotak(char, label) {
                if (!adaHan(char) || sudah.has(char)) return;
                sudah.add(char);

                const item = document.createElement('div');
                item.className = 'goresan-item';
                const kotak = document.createElement('div');
                kotak.className = 'goresan-kotak';
                const cap = document.createElement('div');
                cap.className = 'goresan-cap';
                cap.textContent = label;
                item.appendChild(kotak);
                item.appendChild(cap);
                area.appendChild(item);

                const data = { gagal: false };
                data.writer = HanziWriter.create(kotak, char, {
                    width: 160,
                    height: 160,
                    padding: 14,
                    showOutline: true,
                    strokeColor: '#1e293b',
                    outlineColor: '#e2e8f0',
                    radicalColor: '#1a73e8',
                    drawingColor: '#1a73e8',
                    strokeAnimationSpeed: 1,
                    delayBetweenStrokes: 250,
                    onLoadCharDataError: () => {
                        data.gagal = true;
                        kotak.innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 text-muted small px-2 text-center">Data goresan "' + char + '" tidak tersedia</div>';
                    },
                });

                kotak.addEventListener('click', () => {
                    if (data.gagal || latihanAktif) return;
                    data.writer.animateCharacter();
                });

                daftar.push(data);
            }

            Array.from(UTAMA).forEach(c => tambahKotak(c, 'Radikal'));
            VARIAN.forEach(v => Array.from(v).forEach(c => tambahKotak(c, 'Varian')));

            if (daftar.length === 0) {
                infoGoresan.textContent = 'Tidak ada karakter Han yang bisa ditampilkan goresannya.';
            }

            let sedangAnimasi = false;
            async function animasiSemua() {
                if (sedangAnimasi) return;
                sedangAnimasi = true;
                stopLatihan();
                for (const item of daftar) {
                    if (item.gagal) continue;
                    await new Promise(selesai => item.writer.animateCharacter({ onComplete: selesai }));
                }
                sedangAnimasi = false;
            }

            const btnLatihan = document.getElementById('btn-latihan');

            function stopLatihan() {
                if (!latihanAktif) return;
                latihanAktif = false;
                daftar.forEach(i => { if (!i.gagal) { i.writer.cancelQuiz(); i.writer.showCharacter(); } });
                btnLatihan.innerHTML = '<i class="fas fa-pencil-alt me-1"></i> Latihan menulis';
                infoGoresan.textContent = 'Klik kotak huruf untuk memutar animasi satu karakter.';
            }

            function mulaiLatihan() {
                if (sedangAnimasi) return;
                latihanAktif = true;
                daftar.forEach(i => { if (!i.gagal) i.writer.quiz({ showHintAfterMisses: 2 }); });
                btnLatihan.innerHTML = '<i class="fas fa-times me-1"></i> Selesai latihan';
                infoGoresan.textContent = 'Tulis tiap goresan dengan mouse atau jari, sesuai urutan. Garis biru = benar.';
            }

            document.getElementById('btn-animasi').addEventListener('click', animasiSemua);
            btnLatihan.addEventListener('click', () => latihanAktif ? stopLatihan() : mulaiLatihan());

            /* =============== SUARA =============== */
            const info = document.getElementById('info-suara');
            const pilihan = document.getElementById('pilih-suara');
            const KUNCI_SUARA = 'suara_mandarin';
            const bisaSuara = 'speechSynthesis' in window;
            let suara = null;
            let daftarMandarin = [];

            function suaraDefault(list) {
                return list.find(v => /^zh[-_]TW$/i.test(v.lang) && /google/i.test(v.name))
                    || list.find(v => /^zh[-_](TW|HK)/i.test(v.lang))
                    || list[0]
                    || null;
            }

            function isiPilihan() {
                daftarMandarin = window.speechSynthesis.getVoices().filter(v => /^zh/i.test(v.lang));

                pilihan.innerHTML = '';
                if (daftarMandarin.length === 0) {
                    pilihan.classList.add('d-none');
                    suara = null;
                    return;
                }

                daftarMandarin.forEach(v => {
                    const opsi = document.createElement('option');
                    opsi.value = v.name;
                    opsi.textContent = v.name;
                    pilihan.appendChild(opsi);
                });

                let tersimpan = null;
                try { tersimpan = localStorage.getItem(KUNCI_SUARA); } catch (e) {}

                suara = daftarMandarin.find(v => v.name === tersimpan) || suaraDefault(daftarMandarin);
                pilihan.value = suara.name;
                pilihan.classList.remove('d-none');
            }

            if (bisaSuara) {
                isiPilihan();
                window.speechSynthesis.onvoiceschanged = isiPilihan;
            }

            pilihan.addEventListener('change', () => {
                suara = daftarMandarin.find(v => v.name === pilihan.value) || suara;
                try { localStorage.setItem(KUNCI_SUARA, pilihan.value); } catch (e) {}
                ucapkan(UTAMA, 0.9);
            });

            function ucapkan(teks, laju) {
                info.classList.add('d-none');

                if (!bisaSuara) {
                    info.textContent = 'Browser ini belum mendukung suara. Coba Chrome atau Edge terbaru.';
                    info.classList.remove('d-none');
                    return;
                }
                if (!suara) {
                    info.textContent = 'Suara Mandarin belum tersedia di perangkat ini. Di Windows: Settings > Time & language > Language & region, tambahkan bahasa Chinese, lalu restart browser. Chrome yang sedang online biasanya sudah punya suara Google bahasa Mandarin.';
                    info.classList.remove('d-none');
                    return;
                }

                window.speechSynthesis.cancel();
                const u = new SpeechSynthesisUtterance(teks);
                u.voice = suara;
                u.lang = suara.lang;
                u.rate = laju;
                window.speechSynthesis.speak(u);
            }

            document.querySelectorAll('.btn-speak-utama').forEach(btn => {
                btn.addEventListener('click', () => ucapkan(UTAMA, parseFloat(btn.dataset.laju)));
            });
        })();
    </script>
@endsection
