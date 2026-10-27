@extends('layouts.user.user')

@section('title', 'Latihan Kosakata')

@section('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    body, .card, .btn, h4, h5, label, select { font-family: 'Plus Jakarta Sans', sans-serif; }

    .ph-card { background: #fff; border: 1px solid #e9ecef; border-radius: 14px; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; margin-bottom: 1.25rem; position: relative; overflow: hidden; box-shadow: 0 1px 6px rgba(0,0,0,.05); }
    .ph-card::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; border-radius: 14px 0 0 14px; background: #0369a1; }
    .ph-left { display: flex; align-items: center; gap: 12px; }
    .ph-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; background: #e0f2fe; color: #0369a1; }
    .ph-title { font-size: 1.05rem; font-weight: 700; color: #1e293b; letter-spacing: -.2px; line-height: 1.2; margin: 0; }
    .ph-breadcrumb { display: flex; align-items: center; gap: 4px; flex-wrap: wrap; margin-top: 4px; list-style: none; padding: 0; margin-bottom: 0; }
    .ph-breadcrumb li { display: flex; align-items: center; }
    .ph-breadcrumb li + li::before { content: '›'; color: #cbd5e1; font-size: .7rem; margin: 0 4px; }
    .ph-breadcrumb a { font-size: .75rem; color: #1a73e8; text-decoration: none; }
    .ph-breadcrumb .bc-active { font-size: .75rem; color: #94a3b8; }

    .form-card { border: none; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,.07); }
    .section-divider { background: #f8f9fa; border-left: 4px solid #1269db; padding: 8px 14px; border-radius: 0 6px 6px 0; font-weight: 600; font-size: .9rem; color: #1269db; margin-bottom: 1rem; }

    .review-hero { border: none; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,.07); background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%); position: relative; overflow: hidden; }
    .review-hero::after { content: ''; position: absolute; right: -30px; top: -30px; width: 130px; height: 130px; border-radius: 50%; background: #ea580c; opacity: .1; }
    .review-hero.kosong { background: linear-gradient(135deg, #e6f9f0 0%, #d1fae5 100%); }
    .review-hero.kosong::after { background: #16a34a; }
    .review-angka { font-size: 2.6rem; font-weight: 800; line-height: 1; color: #1e293b; }
    .review-ket { font-size: .85rem; color: #64748b; }

    .form-label { font-size: .78rem; font-weight: 600; color: #64748b; margin-bottom: 4px; }
    .form-select { border-radius: 10px; font-size: .85rem; border-color: #e2e8f0; }
    .jenis-wrap { display: flex; flex-wrap: wrap; gap: 8px; }
    .jenis-wrap .form-check { margin: 0; padding: 0; }
    .jenis-wrap .form-check-input { position: absolute; opacity: 0; pointer-events: none; }
    .jenis-wrap .form-check-label { display: inline-block; font-size: .8rem; font-weight: 600; color: #64748b; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 6px 12px; cursor: pointer; transition: all .15s; }
    .jenis-wrap .form-check-input:checked + .form-check-label { background: #e8f0fe; border-color: #1a73e8; color: #1a73e8; }
    .jenis-wrap .form-check-input:focus-visible + .form-check-label { outline: 2px solid #1a73e8; outline-offset: 2px; }
    .btn-primary { background: linear-gradient(135deg, #1a73e8, #1558b0); border: none; border-radius: 10px; font-weight: 600; font-size: .85rem; padding: 9px 20px; box-shadow: 0 2px 8px rgba(26,115,232,.35); }
    .btn-primary:hover { background: linear-gradient(135deg, #1558b0, #0f3e82); }
    .btn-orange { background: linear-gradient(135deg, #f97316, #ea580c); border: none; color: #fff; border-radius: 10px; font-weight: 600; font-size: .85rem; padding: 9px 20px; box-shadow: 0 2px 8px rgba(234,88,12,.35); }
    .empty-grup { border: none; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,.07); text-align: center; padding: 36px 24px; }
    .empty-grup .ikon { width: 64px; height: 64px; border-radius: 50%; background: #e0f2fe; color: #0369a1; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; font-size: 1.5rem; }
    .btn-orange:hover { color: #fff; background: linear-gradient(135deg, #ea580c, #c2410c); }
</style>
@endsection

@section('content')
    <div class="container">

        <div class="ph-card">
            <div class="ph-left">
                <div class="ph-icon"><i class="fas fa-layer-group"></i></div>
                <div>
                    <h5 class="ph-title">Latihan Kosakata</h5>
                    <ol class="ph-breadcrumb" aria-label="breadcrumb">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><a href="{{ route('admin.hafalan.index') }}">Progres Hafalan</a></li>
                        <li><span class="bc-active">Flashcard</span></li>
                    </ol>
                </div>
            </div>
            <a href="{{ route('admin.hafalan.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="page-inner">

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif
            @if (session('error') || $errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') ?? $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            @if ($grups->isEmpty())
                {{-- Belum punya grup kosakata: flashcard hanya memakai kata dari grup --}}
                <div class="card empty-grup">
                    <div class="ikon"><i class="fas fa-folder-open"></i></div>
                    <h5 class="fw-bold mb-1" style="color:#1e293b;">Belum ada grup kosakata</h5>
                    <p class="text-muted mb-3" style="font-size:.85rem;">
                        Flashcard hanya memakai kata yang ada di grup kosakata buatanmu.
                        Buat grup dulu, lalu masukkan kata-kata yang mau dihafal.
                    </p>
                    <div>
                        <a href="{{ route('admin.grup-kosakata.index') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-1"></i> Kelola grup kosakata
                        </a>
                    </div>
                </div>
            @else

            {{-- Review harian --}}
            <div class="card review-hero mb-4 {{ $jatuhTempo === 0 ? 'kosong' : '' }}">
                <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="review-angka">{{ $jatuhTempo }}</div>
                        <div>
                            <div class="fw-bold" style="color:#1e293b;">
                                @if ($jatuhTempo > 0)
                                    kata siap direview hari ini
                                @else
                                    <i class="fas fa-check-circle text-success me-1"></i> Semua review hari ini sudah beres
                                @endif
                            </div>
                            <div class="review-ket">
                                @if ($jatuhTempo > $batasReview)
                                    Satu sesi memuat maksimal {{ $batasReview }} kata, sisanya bisa dilanjutkan setelahnya.
                                @elseif ($jatuhTempo > 0)
                                    Kata di grup kosakatamu berstatus Lupa &amp; Ingat dan Ingat Sepenuhnya yang jadwalnya sudah tiba.
                                @else
                                    Kata baru akan muncul lagi sesuai jadwal. Mau lanjut, pakai latihan bebas di bawah.
                                @endif
                            </div>
                        </div>
                    </div>
                    @if ($jatuhTempo > 0)
                        <form method="GET" action="{{ route('admin.flashcard.mulai') }}">
                            <input type="hidden" name="mode" value="review">
                            <button type="submit" class="btn btn-orange">
                                <i class="fas fa-play me-1"></i> Mulai review
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Latihan bebas --}}
            <div class="card form-card">
                <div class="card-body p-4">
                    <div class="section-divider"><i class="fas fa-sliders-h me-2"></i>Latihan Bebas</div>

                    <form method="GET" action="{{ route('admin.flashcard.mulai') }}">
                        <input type="hidden" name="mode" value="bebas">

                        <div class="row g-3">
                            <div class="col-md-12 col-lg-6">
                                <label class="form-label" for="f-grup">Grup kosakata</label>
                                <select class="form-select" id="f-grup" name="grup_kosakata_id">
                                    <option value="">Semua grup kosakataku</option>
                                    @foreach ($grups as $grup)
                                        <option value="{{ $grup->id }}" {{ (string) request('grup_kosakata_id') === (string) $grup->id ? 'selected' : '' }}>
                                            {{ $grup->nama }} ({{ $grup->kosakatas_count }} kata)
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <label class="form-label" for="f-status">Status kata</label>
                                <select class="form-select" id="f-status" name="status">
                                    <option value="berikutnya" selected>{{ $statusList['berikutnya']['label'] }} (belum dipelajari)</option>
                                    <option value="lupa_dan_ingat">{{ $statusList['lupa_dan_ingat']['label'] }}</option>
                                    <option value="ingat_sepenuhnya">{{ $statusList['ingat_sepenuhnya']['label'] }}</option>
                                    <option value="">Semua status</option>
                                </select>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <label class="form-label" for="f-jumlah">Jumlah kartu</label>
                                <select class="form-select" id="f-jumlah" name="jumlah">
                                    @foreach ($pilihanJumlah as $n)
                                        <option value="{{ $n }}" {{ $n === 20 ? 'selected' : '' }}>{{ $n }} kata</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="f-kategori">Kategori</label>
                                <select class="form-select" id="f-kategori" name="kategori_id">
                                    <option value="">Semua kategori</option>
                                    @foreach ($kategoris as $kategori)
                                        <option value="{{ $kategori->id }}">{{ $kategori->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="f-level">Level TOCFL</label>
                                <select class="form-select" id="f-level" name="level_tocfl_id">
                                    <option value="">Semua level</option>
                                    @foreach ($levels as $level)
                                        <option value="{{ $level->id }}">{{ $level->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mt-4">
                            <div class="form-label">Jenis soal</div>
                            <div class="jenis-wrap">
                                @foreach ($jenisSoal as $kunci => $label)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="jenis[]" value="{{ $kunci }}" id="j-{{ $kunci }}" checked>
                                        <label class="form-check-label" for="j-{{ $kunci }}">{{ $label }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mt-4">
                            <div class="form-check">
                                <input type="hidden" name="acak" value="0">
                                <input class="form-check-input" type="checkbox" name="acak" value="1" id="f-acak" checked>
                                <label class="form-check-label" for="f-acak" style="font-size:.85rem;">Acak urutan soal</label>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-play me-1"></i> Mulai latihan
                            </button>
                        </div>
                    </form>

                    <div class="mt-4 pt-3" style="border-top:1px solid #f1f5f9; font-size:.78rem; color:#64748b; line-height:1.6;">
                        <i class="fas fa-info-circle me-1"></i>
                        Hanya kata yang sudah masuk grup kosakata yang muncul di flashcard.
                        Jawab tiap soal dari ingatanmu, lalu nilai sendiri: <b>Benar</b> atau <b>Salah</b>.
                        Benar <b>{{ $targetBeruntun }} kali berturut-turut</b> membuat kata menjadi <b>Ingat Sepenuhnya</b> (review tiap 7 hari).
                        Sekali <b>Salah</b>, kata kembali ke <b>Lupa &amp; Ingat</b> (review besok) dan hitungan mulai lagi dari 0.
                    </div>
                </div>
            </div>

            @endif

        </div>
    </div>
@endsection
