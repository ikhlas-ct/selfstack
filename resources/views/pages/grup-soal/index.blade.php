@extends('layouts.user.user')

@section('title', 'Paket Soal')

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

    /* ---------- pil ---------- */
    .pil-status { display: flex; flex-wrap: wrap; gap: 12px; margin: 8px 0 18px; }
    .pil { border: 1px solid #e2e8f0; background: #fff; color: #334155; border-radius: 999px; padding: 10px 24px; font-size: .88rem; font-weight: 600; text-decoration: none; transition: border-color .15s, background .15s, color .15s; }
    .pil:hover { border-color: #93c5fd; color: #334155; }
    .pil.aktif { background: #eaf2ff; border-color: #1a73e8; color: #1a73e8; }

    /* ---------- bar filter ---------- */
    .bar-filter { background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 16px; padding: 18px 24px; margin-bottom: 18px; }
    .bar-filter .form-control, .bar-filter .form-select { border-radius: 10px; font-size: .85rem; border-color: #e2e8f0; height: 44px; }
    .bar-filter .form-control:focus, .bar-filter .form-select:focus { border-color: #1a73e8; box-shadow: 0 0 0 3px rgba(26,115,232,.12); }
    .bar-filter .btn-utama, .bar-filter .btn-polos { height: 44px; }

    /* ---------- tabel & label ---------- */
    .tbl-wrap { overflow-x: auto; }
    .tbl { width: 100%; border-collapse: collapse; font-size: .88rem; }
    .tbl th { text-align: left; padding: 12px 16px; font-size: .72rem; letter-spacing: .04em; text-transform: uppercase; color: #94a3b8; font-weight: 700; border-bottom: 1px solid #e9ecef; white-space: nowrap; }
    .tbl td { padding: 14px 16px; border-bottom: 1px solid #f1f5f9; vertical-align: top; color: #334155; }
    .tbl tr:last-child td { border-bottom: 0; }
    .tbl tbody tr:hover { background: #fafbfd; }
    .judul-paket { font-weight: 700; color: #1e293b; text-decoration: none; }
    .judul-paket:hover { color: #1a73e8; }
    .sub { font-size: .76rem; color: #94a3b8; margin-top: 3px; }
    .kosong { text-align: center; padding: 48px 16px; color: #94a3b8; }
    .kosong i { font-size: 2rem; margin-bottom: 10px; display: block; color: #cbd5e1; }

    .tag { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 6px; font-size: .7rem; font-weight: 600; letter-spacing: .2px; background: #f1f5f9; color: #475569; white-space: nowrap; border: 0; }
    .tag-latihan { background: #e0f2fe; color: #0369a1; }
    .tag-ujian { background: #fce7f3; color: #be185d; }
    .tag-level { background: #e8f0fe; color: #1a73e8; }
    .tag-ok, .tag-disetujui { background: #dcfce7; color: #15803d; }
    .tag-otomatis_lolos { background: #ccfbf1; color: #0f766e; }
    .tag-perlu_dicek { background: #fef3c7; color: #b45309; }
    .tag-ditolak { background: #fee2e2; color: #b91c1c; }
    .tag-off { background: #f1f5f9; color: #64748b; }
    button.tag { cursor: pointer; }

    .btn-ikon { width: 34px; height: 34px; border-radius: 8px; border: 1px solid #e2e8f0; background: #fff; color: #64748b; display: inline-flex; align-items: center; justify-content: center; font-size: .8rem; text-decoration: none; cursor: pointer; }
    .btn-ikon:hover { background: #f1f5f9; color: #1e293b; }
    .btn-ikon.ok:hover { color: #15803d; border-color: #86efac; background: #f0fdf4; }
    .btn-ikon.bahaya:hover { color: #b91c1c; border-color: #fca5a5; background: #fef2f2; }
    .aksi-baris { display: flex; gap: 6px; justify-content: flex-end; }
    .aksi-baris form { margin: 0; }
</style>
@endsection

@section('content')
    @php
        $L = \App\Support\LabelSoal::class;
        $qBase = request()->except('jenis', 'page');
    @endphp

    <div class="container">
        <div class="ph-card">
            <div class="ph-left">
                <div class="ph-icon"><i class="fas fa-clipboard-list"></i></div>
                <div>
                    <h5 class="ph-title">Paket Soal</h5>
                    <ol class="ph-breadcrumb" aria-label="breadcrumb">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><span class="bc-active">Paket Soal</span></li>
                    </ol>
                </div>
            </div>
            <div class="ph-aksi">
                <a href="{{ route('admin.grup-soal.create') }}" class="btn-utama"><i class="fas fa-plus me-2"></i>Buat Paket</a>
            </div>
        </div>

        <div class="page-inner">
            @if (session('sukses'))
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="fas fa-circle-check me-2"></i>{{ session('sukses') }}
                </div>
            @endif


        <div class="pil-status">
            <a class="pil {{ $jenis === null ? 'aktif' : '' }}" href="{{ route('admin.grup-soal.index', $qBase) }}">
                Semua ({{ $ringkas['semua'] }})
            </a>
            <a class="pil {{ $jenis === 'latihan' ? 'aktif' : '' }}" href="{{ route('admin.grup-soal.index', $qBase + ['jenis' => 'latihan']) }}">
                Latihan ({{ $ringkas['latihan'] }})
            </a>
            <a class="pil {{ $jenis === 'ujian' ? 'aktif' : '' }}" href="{{ route('admin.grup-soal.index', $qBase + ['jenis' => 'ujian']) }}">
                Ujian ({{ $ringkas['ujian'] }})
            </a>
        </div>

        <form method="GET" action="{{ route('admin.grup-soal.index') }}" class="bar-filter">
            <input type="hidden" name="jenis" value="{{ $jenis }}">
            <div class="row g-2 align-items-center">
                <div class="col-lg-5">
                    <input type="search" name="search" value="{{ $cari }}" class="form-control" placeholder="Cari judul paket…">
                </div>
                <div class="col-lg-2 col-6">
                    <select name="level" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua level</option>
                        @foreach ($levels as $lv)
                            <option value="{{ $lv->id }}" @selected($level === $lv->id)>TOCFL {{ $lv->tingkat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-6">
                    <select name="aktif" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua status</option>
                        <option value="1" @selected($aktif === '1')>Aktif</option>
                        <option value="0" @selected($aktif === '0')>Nonaktif</option>
                    </select>
                </div>
                <div class="col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn-utama"><i class="fas fa-magnifying-glass me-2"></i>Cari</button>
                    <a href="{{ route('admin.grup-soal.index') }}" class="btn-polos"><i class="fas fa-rotate-right me-2"></i>Reset</a>
                </div>
            </div>
        </form>

        <div class="panel">
            <div class="tbl-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Paket</th>
                            <th>Jenis</th>
                            <th>Level</th>
                            <th>Soal</th>
                            <th>Pengaturan</th>
                            <th>Percobaan</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($paket as $p)
                            @php
                                $atur = array_filter([
                                    $p->durasi_menit ? $p->durasi_menit . ' menit' : null,
                                    ($p->jenis === 'ujian' && $p->nilai_lulus !== null) ? 'lulus ≥ ' . $p->nilai_lulus : null,
                                    $p->maks_percobaan ? 'maks ' . $p->maks_percobaan . '×' : null,
                                ]);
                                $pesanHapus = 'Hapus paket ini? ' . $p->soals_count . ' soal beserta semua riwayat percobaan dan jawabannya ikut terhapus permanen. Untuk menyembunyikan saja, nonaktifkan paketnya.';
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ route('admin.grup-soal.show', $p) }}" class="judul-paket hanzi-text">{{ $p->judul }}</a>
                                    @if ($p->grupKosakata)
                                        <div class="sub"><i class="fas fa-layer-group me-1"></i>{{ $p->grupKosakata->nama }}</div>
                                    @endif
                                    @if ($p->sumber !== 'manual')
                                        <div class="sub">Sumber: {{ $L::SUMBER[$p->sumber] ?? $p->sumber }}</div>
                                    @endif
                                </td>
                                <td><span class="tag tag-{{ $p->jenis }}">{{ $L::JENIS[$p->jenis] ?? $p->jenis }}</span></td>
                                <td>
                                    @if ($p->levelTocfl)
                                        <span class="tag tag-level">TOCFL {{ $p->levelTocfl->tingkat }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $p->soals_count }}</strong>
                                    @if ($p->perlu_dicek_count > 0)
                                        <div><span class="tag tag-perlu_dicek">{{ $p->perlu_dicek_count }} perlu dicek</span></div>
                                    @endif
                                </td>
                                <td class="sub" style="margin:0">{{ $atur ? implode(' · ', $atur) : 'Tanpa batas' }}</td>
                                <td>{{ $p->percobaan_count }}×</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.grup-soal.aktif', $p) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="tag {{ $p->aktif ? 'tag-ok' : 'tag-off' }}"
                                                title="Klik untuk {{ $p->aktif ? 'menonaktifkan' : 'mengaktifkan' }}">
                                            {{ $p->aktif ? 'Aktif' : 'Nonaktif' }}
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <div class="aksi-baris">
                                        <a href="{{ route('admin.grup-soal.show', $p) }}" class="btn-ikon" title="Lihat soal"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('admin.grup-soal.edit', $p) }}" class="btn-ikon" title="Ubah paket"><i class="fas fa-pen"></i></a>
                                        <form method="POST" action="{{ route('admin.grup-soal.destroy', $p) }}"
                                              onsubmit='return confirm(@json($pesanHapus))'>
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-ikon bahaya" title="Hapus paket"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="kosong">
                                        <i class="fas fa-clipboard-question"></i>
                                        Belum ada paket soal yang cocok.
                                        <div class="mt-3">
                                            <a href="{{ route('admin.grup-soal.create') }}" class="btn-utama"><i class="fas fa-plus me-2"></i>Buat Paket</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($paket->hasPages())
            <div class="d-flex justify-content-center">
                {{ $paket->links('pagination::bootstrap-5') }}
            </div>
        @endif
        </div>
    </div>
@endsection
