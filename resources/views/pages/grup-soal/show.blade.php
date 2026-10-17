@extends('layouts.user.user')

@section('title', $grupSoal->judul)

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

    /* ---------- ringkasan paket ---------- */
    .meta-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 16px; margin-top: 16px; }
    .meta-item .m-label { font-size: .72rem; text-transform: uppercase; letter-spacing: .04em; color: #94a3b8; font-weight: 700; }
    .meta-item .m-nilai { font-size: 1.05rem; font-weight: 700; color: #1e293b; margin-top: 2px; }
</style>
@endsection

@section('content')
    @php
        $L = \App\Support\LabelSoal::class;
        $qBase = request()->except('page');
    @endphp

    <div class="container">
        <div class="ph-card">
            <div class="ph-left">
                <div class="ph-icon"><i class="fas fa-clipboard-list"></i></div>
                <div>
                    <h5 class="ph-title">{{ \Illuminate\Support\Str::limit($grupSoal->judul, 60) }}</h5>
                    <ol class="ph-breadcrumb" aria-label="breadcrumb">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><a href="{{ route('admin.grup-soal.index') }}">Paket Soal</a></li>
                        <li><span class="bc-active">{{ \Illuminate\Support\Str::limit($grupSoal->judul, 40) }}</span></li>
                    </ol>
                </div>
            </div>
            <div class="ph-aksi">
                <a href="{{ route('admin.grup-soal.edit', $grupSoal) }}" class="btn-polos"><i class="fas fa-pen me-2"></i>Ubah Paket</a>
                <a href="{{ route('admin.grup-soal.soal.create', $grupSoal) }}" class="btn-utama"><i class="fas fa-plus me-2"></i>Tambah Soal</a>
            </div>
        </div>

        <div class="page-inner">
            @if (session('sukses'))
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="fas fa-circle-check me-2"></i>{{ session('sukses') }}
                </div>
            @endif


        {{-- ---------- ringkasan paket ---------- --}}
        <div class="panel panel-pad">
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <span class="tag tag-{{ $grupSoal->jenis }}">{{ $L::JENIS[$grupSoal->jenis] ?? $grupSoal->jenis }}</span>
                @if ($grupSoal->levelTocfl)
                    <span class="tag tag-level">TOCFL {{ $grupSoal->levelTocfl->tingkat }}</span>
                @endif
                <span class="tag {{ $grupSoal->aktif ? 'tag-ok' : 'tag-off' }}">{{ $grupSoal->aktif ? 'Aktif' : 'Nonaktif' }}</span>
                <span class="tag">{{ $L::SUMBER[$grupSoal->sumber] ?? $grupSoal->sumber }}</span>
            </div>

            @if ($grupSoal->deskripsi)
                <p class="mt-3 mb-0 hanzi-text" style="color:#475569; white-space:pre-line">{{ $grupSoal->deskripsi }}</p>
            @endif

            <div class="meta-grid">
                <div class="meta-item">
                    <div class="m-label">Jumlah soal</div>
                    <div class="m-nilai">{{ $totalSoal }}</div>
                </div>
                <div class="meta-item">
                    <div class="m-label">Perlu dicek</div>
                    <div class="m-nilai">{{ $perStatus['perlu_dicek'] ?? 0 }}</div>
                </div>
                <div class="meta-item">
                    <div class="m-label">Durasi</div>
                    <div class="m-nilai">{{ $grupSoal->durasi_menit ? $grupSoal->durasi_menit . ' menit' : 'Bebas' }}</div>
                </div>
                @if ($grupSoal->jenis === 'ujian')
                    <div class="meta-item">
                        <div class="m-label">Nilai lulus</div>
                        <div class="m-nilai">{{ $grupSoal->nilai_lulus !== null ? $grupSoal->nilai_lulus : '—' }}</div>
                    </div>
                @endif
                <div class="meta-item">
                    <div class="m-label">Maks. percobaan</div>
                    <div class="m-nilai">{{ $grupSoal->maks_percobaan ?? 'Tanpa batas' }}</div>
                </div>
                <div class="meta-item">
                    <div class="m-label">Percobaan selesai</div>
                    <div class="m-nilai">{{ $percobaan }}×</div>
                </div>
                <div class="meta-item">
                    <div class="m-label">Rata-rata skor</div>
                    <div class="m-nilai">{{ $rataSkor !== null ? $rataSkor : '—' }}</div>
                </div>
                @if ($grupSoal->grupKosakata)
                    <div class="meta-item">
                        <div class="m-label">Grup kosakata</div>
                        <div class="m-nilai hanzi-text">{{ $grupSoal->grupKosakata->nama }}</div>
                    </div>
                @endif
            </div>

            @if ($perBagian->isNotEmpty())
                <div class="d-flex flex-wrap gap-2 mt-3">
                    @foreach ($perBagian as $b => $jml)
                        <span class="tag">{{ $L::BAGIAN[$b] ?? $b }}: {{ $jml }}</span>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ---------- filter soal ---------- --}}
        <form method="GET" action="{{ route('admin.grup-soal.show', $grupSoal) }}" class="bar-filter">
            <div class="row g-2 align-items-center">
                <div class="col-lg-5">
                    <input type="search" name="search" value="{{ $cari }}" class="form-control" placeholder="Cari isi pertanyaan…">
                </div>
                <div class="col-lg-2 col-6">
                    <select name="bagian" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua bagian</option>
                        @foreach ($L::BAGIAN as $k => $label)
                            <option value="{{ $k }}" @selected($bagian === $k)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 col-6">
                    <select name="status" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua status</option>
                        @foreach ($L::STATUS as $k => $label)
                            <option value="{{ $k }}" @selected($status === $k)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-3 d-flex gap-2">
                    <button type="submit" class="btn-utama"><i class="fas fa-magnifying-glass me-2"></i>Cari</button>
                    <a href="{{ route('admin.grup-soal.show', $grupSoal) }}" class="btn-polos"><i class="fas fa-rotate-right me-2"></i>Reset</a>
                </div>
            </div>
        </form>

        {{-- ---------- daftar soal ---------- --}}
        <div class="panel">
            <div class="tbl-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Bagian</th>
                            <th>Pertanyaan</th>
                            <th>Kunci</th>
                            <th>Tingkat</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($soals as $s)
                            <tr>
                                <td class="text-muted">{{ $s->urutan }}</td>
                                <td>
                                    <span class="tag">{{ $L::BAGIAN[$s->bagian] ?? $s->bagian }}</span>
                                    @if ($s->tipe)
                                        <div class="sub">{{ $L::TIPE[$s->tipe] ?? $s->tipe }}</div>
                                    @endif
                                </td>
                                <td class="hanzi-text" style="min-width:260px">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($s->pertanyaan), 90) }}
                                    @if ($s->audio)<i class="fas fa-volume-high ms-1 text-muted" title="Ada audio"></i>@endif
                                    @if ($s->paragraf || $s->paragraf_id)<i class="fas fa-file-lines ms-1 text-muted" title="Ada bacaan"></i>@endif
                                </td>
                                <td class="hanzi-text">
                                    <strong>{{ $s->jawaban_benar }}.</strong>
                                    {{ \Illuminate\Support\Str::limit((string) $s->teks_jawaban_benar, 28) }}
                                </td>
                                <td>{{ $s->tingkat }}</td>
                                <td><span class="tag tag-{{ $s->status_validasi }}">{{ $L::STATUS[$s->status_validasi] ?? $s->status_validasi }}</span></td>
                                <td>
                                    <div class="aksi-baris">
                                        @if ($s->status_validasi !== 'disetujui')
                                            <form method="POST" action="{{ route('admin.soal.status', $s) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status_validasi" value="disetujui">
                                                <button type="submit" class="btn-ikon ok" title="Setujui"><i class="fas fa-check"></i></button>
                                            </form>
                                        @endif
                                        @if ($s->status_validasi !== 'ditolak')
                                            <form method="POST" action="{{ route('admin.soal.status', $s) }}">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status_validasi" value="ditolak">
                                                <button type="submit" class="btn-ikon bahaya" title="Tolak (disembunyikan dari ujian)"><i class="fas fa-ban"></i></button>
                                            </form>
                                        @endif
                                        <a href="{{ route('admin.soal.edit', $s) }}" class="btn-ikon" title="Ubah soal"><i class="fas fa-pen"></i></a>
                                        <form method="POST" action="{{ route('admin.soal.destroy', $s) }}"
                                              onsubmit="return confirm('Hapus soal ini? Jawaban pada riwayat ujian untuk soal ini ikut terhapus. Untuk menyembunyikan saja, pakai tombol Tolak.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-ikon bahaya" title="Hapus soal"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="kosong">
                                        <i class="fas fa-circle-question"></i>
                                        @if ($cari !== '' || $bagian || $status)
                                            Tidak ada soal yang cocok dengan filter.
                                        @else
                                            Paket ini belum punya soal.
                                            <div class="mt-3">
                                                <a href="{{ route('admin.grup-soal.soal.create', $grupSoal) }}" class="btn-utama"><i class="fas fa-plus me-2"></i>Tambah Soal</a>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($soals->hasPages())
            <div class="d-flex justify-content-center">
                {{ $soals->links('pagination::bootstrap-5') }}
            </div>
        @endif
        </div>
    </div>
@endsection
