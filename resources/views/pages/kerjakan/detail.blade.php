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

    /* ===== RINGKASAN ANGKA ===== */
    .stat-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px; margin-bottom: 1.25rem; }
    .stat-box { background: #fff; border-radius: 14px; padding: 14px 16px; box-shadow: 0 1px 6px rgba(0,0,0,.06); border: 1px solid #eef2f6; }
    .stat-num { font-size: 1.55rem; font-weight: 800; color: var(--gk-ink); line-height: 1.1; letter-spacing: -.5px; }
    .stat-cap { font-size: .76rem; color: var(--gk-muted); margin-top: 2px; }

    /* ===== HANZI & EMPTY ===== */
    .hanzi-chip { display: inline-block; font-family: var(--gk-hanzi); font-size: 1rem; font-weight: 600; color: var(--gk-ink); background: #f1f5f9; border-radius: 8px; padding: 2px 9px; }
    .empty-state { padding: 36px 20px; text-align: center; }
    .empty-state-icon { width: 68px; height: 68px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; font-size: 1.5rem; color: var(--gk-faint); }
    .empty-state-title { font-weight: 600; color: var(--gk-muted); margin-bottom: 4px; }
    .empty-state-text { font-size: .82rem; color: var(--gk-faint); }

    /* ===== DETAIL PAKET ===== */
    .bagian-wrap { display: flex; flex-wrap: wrap; gap: 6px; }
    .riwayat-row { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 10px 0; border-bottom: 1px solid #f1f5f9; flex-wrap: wrap; }
    .riwayat-row:last-child { border-bottom: none; padding-bottom: 0; }
    .riwayat-row .jd { font-size: .86rem; font-weight: 600; color: var(--gk-ink); }
    .riwayat-row .mt { font-size: .74rem; color: var(--gk-faint); margin-top: 2px; }
    .riwayat-skor { font-size: 1.05rem; font-weight: 800; color: var(--gk-ink); }
    .aturan-list { list-style: none; margin: 0; padding: 0; font-size: .82rem; color: #475569; }
    .aturan-list li { display: flex; gap: 10px; padding: 6px 0; }
    .aturan-list i { color: var(--gk-faint); width: 16px; text-align: center; margin-top: 3px; flex-shrink: 0; }

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

    @php $ujian = $grup->jenis === 'ujian'; @endphp

    <div class="container">

        <div class="ph-card show-page">
            <div class="ph-left">
                <div class="ph-icon show"><i class="fas fa-clipboard-list"></i></div>
                <div style="min-width:0;">
                    <h5 class="ph-title">{{ $grup->judul }}</h5>
                    <ol class="ph-breadcrumb" aria-label="breadcrumb">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><a href="{{ route('admin.kerjakan.index') }}">Kerjakan Soal</a></li>
                        <li><span class="bc-active">{{ \Illuminate\Support\Str::limit($grup->judul, 30) }}</span></li>
                    </ol>
                </div>
            </div>
            <div class="ph-actions">
                <a href="{{ route('admin.kerjakan.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </div>

        <div class="page-inner">

            @if (session('success'))
                <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}</div>
            @endif
            @if (session('peringatan'))
                <div class="alert alert-warning"><i class="fas fa-exclamation-triangle me-2"></i>{{ session('peringatan') }}</div>
            @endif
            @if (session('error') || $errors->any())
                <div class="alert alert-danger"><i class="fas fa-exclamation-circle me-2"></i>{{ session('error') ?? $errors->first() }}</div>
            @endif

            <div class="stat-row">
                <div class="stat-box">
                    <div class="stat-num">{{ $jumlahSoal }}</div>
                    <div class="stat-cap">Soal</div>
                </div>
                <div class="stat-box">
                    <div class="stat-num">{{ $grup->durasi_menit ?: '∞' }}</div>
                    <div class="stat-cap">{{ $grup->durasi_menit ? 'Menit' : 'Tanpa batas waktu' }}</div>
                </div>
                <div class="stat-box">
                    <div class="stat-num">{{ $grup->nilai_lulus ?? '-' }}</div>
                    <div class="stat-cap">Nilai lulus</div>
                </div>
                <div class="stat-box">
                    <div class="stat-num">{{ $selesaiCount }}{{ $grup->maks_percobaan ? '/' . $grup->maks_percobaan : '' }}</div>
                    <div class="stat-cap">Percobaan selesai</div>
                </div>
            </div>

            <div class="row g-4">

                {{-- ===== Kiri: tentang paket + riwayat ===== --}}
                <div class="col-lg-8">
                    <div class="card form-card">
                        <div class="card-body">
                            <div class="section-divider"><span><i class="fas fa-info-circle me-2"></i>Tentang paket</span></div>

                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge {{ $ujian ? 'badge-ujian' : 'badge-latihan' }}">{{ $ujian ? 'Ujian' : 'Latihan' }}</span>
                                @if ($grup->levelTocfl)
                                    <span class="badge badge-level">{{ $grup->levelTocfl->nama }}</span>
                                @endif
                            </div>

                            @if ($grup->deskripsi)
                                <p style="font-size:.88rem; color:#475569;">{{ $grup->deskripsi }}</p>
                            @endif

                            @if ($perBagian->isNotEmpty())
                                <div class="info-label mb-2">Isi paket</div>
                                <div class="bagian-wrap mb-3">
                                    @foreach ($perBagian as $bagian => $jumlah)
                                        @php $ib = $infoBagian($bagian); @endphp
<span class="badge {{ $ib['kelas'] }}"><i class="fas {{ $ib['ikon'] }} me-1"></i>{{ $ib['nama'] }}<strong class="ms-1">{{ $jumlah }}</strong></span>
                                    @endforeach
                                </div>
                            @endif

                            <div class="info-label mb-1">Cara kerjanya</div>
                            <ul class="aturan-list">
                                @if ($ujian)
                                    <li><i class="fas fa-eye-slash"></i><span>Benar atau salah baru terlihat setelah ujian selesai.</span></li>
                                    <li><i class="fas fa-exchange-alt"></i><span>Jawaban boleh diubah dan soal boleh dikerjakan tidak berurutan sebelum ujian diakhiri.</span></li>
                                @else
                                    <li><i class="fas fa-check-double"></i><span>Jawaban langsung diperiksa lengkap dengan penjelasannya. Jawaban yang sudah diperiksa tidak bisa diubah.</span></li>
                                @endif
                                @if ($grup->durasi_menit)
                                    <li><i class="fas fa-clock"></i><span>Waktu {{ $grup->durasi_menit }} menit sejak dimulai. Saat habis, percobaan otomatis diakhiri dan jawaban yang sudah tersimpan dinilai.</span></li>
                                @endif
                                <li><i class="fas fa-forward"></i><span>Soal yang dilewati dihitung salah.</span></li>
                            </ul>
                        </div>
                    </div>

                    <div class="card form-card mt-4">
                        <div class="card-body">
                            <div class="section-divider">
                                <span><i class="fas fa-history me-2"></i>Riwayat percobaan</span>
                                <span class="badge badge-pilih">{{ $selesaiCount }}</span>
                            </div>

                            @forelse ($riwayat as $r)
                                <div class="riwayat-row">
                                    <div style="min-width:0;">
                                        <div class="jd">
                                            Percobaan {{ $selesaiCount - $loop->index }}
                                            @if ($r->lulus === true)
                                                <span class="badge badge-benar ms-1">Lulus</span>
                                            @elseif ($r->lulus === false)
                                                <span class="badge badge-salah ms-1">Belum lulus</span>
                                            @endif
                                        </div>
                                        <div class="mt">
                                            {{ $r->selesai_pada?->format('d/m/Y H:i') }},
                                            {{ $r->jumlah_benar }} dari {{ $r->jumlah_soal }} benar
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="riwayat-skor">{{ (float) $r->skor }}</span>
                                        <a href="{{ route('admin.kerjakan.hasil', $r) }}" class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-eye me-1"></i> Pembahasan
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state" style="padding:24px 20px;">
                                    <div class="empty-state-title">Belum pernah dikerjakan</div>
                                    <div class="empty-state-text">Skor dan pembahasan tiap percobaan akan muncul di sini.</div>
                                </div>
                            @endforelse

                            @if ($selesaiCount > $riwayat->count())
                                <div class="help-text">Menampilkan {{ $riwayat->count() }} percobaan terakhir.</div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- ===== Kanan: mulai / lanjutkan ===== --}}
                <div class="col-lg-4">
                    <div class="sticky-side">
                        <div class="card form-card">
                            <div class="card-body">
                                @if ($berjalan)
                                    <div class="section-divider"><span><i class="fas fa-spinner me-2"></i>Percobaan berjalan</span></div>

                                    <p style="font-size:.84rem; color:#475569;" class="mb-3">
                                        Dimulai {{ ($berjalan->mulai_pada ?? $berjalan->created_at)?->format('d/m/Y H:i') }}.
                                        Sudah dijawab <strong>{{ $dijawabBerjalan }}</strong> dari {{ $berjalan->jumlah_soal }} soal.
                                    </p>

                                    <a href="{{ route('admin.kerjakan.soal', [$berjalan, 'no' => $nomorLanjut]) }}" class="btn btn-success w-100 mb-2">
                                        <i class="fas fa-play me-1"></i> Lanjutkan ke soal {{ $nomorLanjut }}
                                    </a>

                                    <form method="POST" action="{{ route('admin.kerjakan.selesai', $berjalan) }}"
                                          onsubmit="return confirm('Akhiri percobaan ini sekarang? Soal yang belum dijawab dihitung salah.')">
                                        @csrf
                                        <button class="btn btn-outline-danger w-100"><i class="fas fa-stop me-1"></i> Akhiri percobaan</button>
                                    </form>
                                @elseif (! $boleh)
                                    <div class="section-divider"><span><i class="fas fa-lock me-2"></i>Percobaan habis</span></div>
                                    <p style="font-size:.84rem; color:#475569;" class="mb-0">
                                        Paket ini hanya boleh dikerjakan {{ $grup->maks_percobaan }} kali dan semuanya sudah terpakai.
                                        Lihat pembahasan percobaan sebelumnya di sebelah kiri.
                                    </p>
                                @elseif ($jumlahSoal === 0)
                                    <div class="section-divider"><span><i class="fas fa-ban me-2"></i>Belum ada soal</span></div>
                                    <p style="font-size:.84rem; color:#475569;" class="mb-0">Paket ini belum punya soal yang bisa dikerjakan.</p>
                                @else
                                    <div class="section-divider"><span><i class="fas fa-play me-2"></i>{{ $ujian ? 'Mulai ujian' : 'Mulai latihan' }}</span></div>

                                    <form method="POST" action="{{ route('admin.kerjakan.mulai', $grup) }}">
                                        @csrf
                                        <button class="btn btn-primary w-100">
                                            <i class="fas fa-play me-1"></i> {{ $ujian ? 'Mulai ujian' : 'Mulai latihan' }}
                                        </button>
                                    </form>

                                    <div class="help-text">
                                        @if ($grup->durasi_menit)
                                            Waktu langsung berjalan begitu dimulai.
                                        @else
                                            Tidak ada batas waktu. Kamu bisa berhenti dan melanjutkannya nanti.
                                        @endif
                                        @if ($sisaPercobaan !== null)
                                            Sisa {{ $sisaPercobaan }} percobaan.
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
