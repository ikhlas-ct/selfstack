@extends('layouts.user.user')

@section('title', 'Data Kosakata')

@section('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    body, .card, .table, .btn, h4, h5 { font-family: 'Plus Jakarta Sans', sans-serif; }

    /* ===== PAGE HEADER ===== */
    .ph-card { background: #fff; border: 1px solid #e9ecef; border-radius: 14px; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; margin-bottom: 1.25rem; position: relative; overflow: hidden; box-shadow: 0 1px 6px rgba(0,0,0,.05); }
    .ph-card::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; border-radius: 14px 0 0 14px; }
    .ph-card.index-page::before { background: #1a73e8; }
    .ph-left { display: flex; align-items: center; gap: 12px; }
    .ph-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; }
    .ph-icon.index { background: #e8f0fe; color: #1a73e8; }
    .ph-title { font-size: 1.05rem; font-weight: 700; color: #1e293b; letter-spacing: -.2px; line-height: 1.2; margin: 0; }
    .ph-breadcrumb { display: flex; align-items: center; gap: 4px; flex-wrap: wrap; margin-top: 4px; list-style: none; padding: 0; margin-bottom: 0; }
    .ph-breadcrumb li { display: flex; align-items: center; }
    .ph-breadcrumb li + li::before { content: '›'; color: #cbd5e1; font-size: .7rem; margin: 0 4px; }
    .ph-breadcrumb a { font-size: .75rem; color: #1a73e8; text-decoration: none; }
    .ph-breadcrumb a:hover { text-decoration: underline; }
    .ph-breadcrumb .bc-active { font-size: .75rem; color: #94a3b8; }

    /* ===== STAT CARDS ===== */
    .stat-card { border: none; border-radius: 16px; padding: 20px; position: relative; overflow: hidden; transition: transform .2s ease, box-shadow .2s ease; box-shadow: 0 2px 12px rgba(0,0,0,.07); }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.12); }
    .stat-card::after { content: ''; position: absolute; right: -18px; top: -18px; width: 80px; height: 80px; border-radius: 50%; opacity: .12; }
    .stat-card.blue   { background: linear-gradient(135deg, #e8f0fe 0%, #dbeafe 100%); } .stat-card.blue::after   { background: #1a73e8; }
    .stat-card.green  { background: linear-gradient(135deg, #e6f9f0 0%, #d1fae5 100%); } .stat-card.green::after  { background: #16a34a; }
    .stat-card.orange { background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%); } .stat-card.orange::after { background: #ea580c; }
    .stat-card.purple { background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%); } .stat-card.purple::after { background: #7c3aed; }
    .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0; color: #fff; }
    .stat-icon.blue { background: #1a73e8; } .stat-icon.green { background: #16a34a; }
    .stat-icon.orange { background: #ea580c; } .stat-icon.purple { background: #7c3aed; }
    .stat-value { font-size: 1.85rem; font-weight: 800; line-height: 1; color: #1e293b; }
    .stat-label { font-size: .78rem; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; color: #64748b; margin-top: 3px; }

    /* ===== CARD & FILTER ===== */
    .filter-card { border: none; border-radius: 16px; box-shadow: 0 2px 12px rgba(0,0,0,.07); overflow: hidden; }
    .filter-card .card-header { background: #fff; border-bottom: 1px solid #f1f5f9; padding: 18px 24px; }
    .filter-card .card-header h5 { font-size: .95rem; font-weight: 700; color: #1e293b; }
    .filter-section { background: #fafbfc; border-bottom: 1px solid #f1f5f9; padding: 16px 24px; }

    .form-control, .form-select { border-radius: 10px; border: 1.5px solid #e2e8f0; font-size: .83rem; padding: 7px 12px; color: #334155; background-color: #f8fafc; transition: border-color .2s, box-shadow .2s; }
    .form-control:focus, .form-select:focus { border-color: #1a73e8; background: #fff; box-shadow: 0 0 0 3px rgba(26,115,232,.12); }
    .form-control::placeholder { color: #94a3b8; }
    .input-group .input-group-text { background: #f8fafc; border: 1.5px solid #e2e8f0; border-right: none; border-radius: 10px 0 0 10px; color: #94a3b8; font-size: .8rem; }
    .input-group .form-control { border-left: none; border-radius: 0 10px 10px 0; }

    /* ===== TABLE ===== */
    .table thead th { background: #f8fafc; color: #64748b; font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .6px; padding: 12px 16px; border-bottom: 2px solid #e2e8f0; border-top: none; white-space: nowrap; }
    .table tbody td { padding: 13px 16px; vertical-align: middle; font-size: .85rem; color: #334155; border-bottom: 1px solid #f1f5f9; }
    .table tbody tr:last-child td { border-bottom: none; }
    .table-hover tbody tr:hover td { background: #f8fafc; }
    .hanzi-text { font-size: 1.25rem; font-weight: 600; color: #1e293b; font-family: 'Noto Sans TC', 'PingFang TC', 'Microsoft JhengHei', 'Plus Jakarta Sans', sans-serif; }

    /* ===== BADGES ===== */
    .badge { font-size: .7rem; font-weight: 600; padding: 4px 9px; border-radius: 6px; letter-spacing: .2px; }
    .badge-aktif    { background: #dcfce7; color: #15803d; }
    .badge-nonaktif { background: #f1f5f9; color: #64748b; }
    .badge-level    { background: #e8f0fe; color: #1a73e8; }
    .badge-jenis    { background: #f5f3ff; color: #7c3aed; }

    /* ===== ACTION BUTTONS ===== */
    .btn-action { width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; font-size: .75rem; padding: 0; border: none; transition: all .15s ease; }
    .btn-detail { background: #e0f2fe; color: #0369a1; } .btn-detail:hover { background: #0369a1; color: #fff; }
    .btn-edit   { background: #fef9c3; color: #a16207; } .btn-edit:hover   { background: #ca8a04; color: #fff; }
    .btn-hapus  { background: #fee2e2; color: #dc2626; } .btn-hapus:hover  { background: #dc2626; color: #fff; }

    .btn-primary { background: linear-gradient(135deg, #1a73e8, #1558b0); border: none; border-radius: 10px; font-weight: 600; font-size: .83rem; padding: 8px 18px; box-shadow: 0 2px 8px rgba(26,115,232,.35); transition: all .2s ease; }
    .btn-primary:hover { background: linear-gradient(135deg, #1558b0, #0f3e82); box-shadow: 0 4px 14px rgba(26,115,232,.45); transform: translateY(-1px); }
    .btn-outline-secondary { border-radius: 10px; font-size: .83rem; border-color: #e2e8f0; color: #64748b; padding: 7px 12px; }
    .btn-outline-secondary:hover { background: #f1f5f9; border-color: #cbd5e1; color: #334155; }
    .btn-excel { border-radius: 10px; font-size: .83rem; font-weight: 600; border: 1.5px solid #16a34a; color: #16a34a; background: #fff; padding: 7px 14px; }
    .btn-excel:hover { background: #16a34a; color: #fff; }

    /* ===== EMPTY STATE ===== */
    .empty-state { padding: 60px 20px; }
    .empty-state-icon { width: 72px; height: 72px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 1.6rem; color: #94a3b8; }
</style>
@endsection

@section('content')
    <div class="container">

        {{-- Header – di LUAR page-inner, sama seperti index lain --}}
        <div class="ph-card index-page">
            <div class="ph-left">
                <div class="ph-icon index"><i class="fas fa-language"></i></div>
                <div>
                    <h5 class="ph-title">Daftar Kosakata</h5>
                    <ol class="ph-breadcrumb" aria-label="breadcrumb">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><span class="bc-active">Kosakata</span></li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="page-inner">

            {{-- Alert (X + hilang otomatis 3 detik) --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible alert-flash fade show mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible alert-flash fade show mb-4" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif

            {{-- ===== STAT CARDS ===== --}}
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card stat-card blue">
                        <div class="d-flex align-items-center gap-3">
                            <div class="stat-icon blue"><i class="fas fa-language"></i></div>
                            <div>
                                <div class="stat-value">{{ $stats['total'] }}</div>
                                <div class="stat-label">Total Kosakata</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card stat-card green">
                        <div class="d-flex align-items-center gap-3">
                            <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
                            <div>
                                <div class="stat-value">{{ $stats['dengan_contoh'] }}</div>
                                <div class="stat-label">Ada Contoh Kalimat</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card stat-card orange">
                        <div class="d-flex align-items-center gap-3">
                            <div class="stat-icon orange"><i class="fas fa-inbox"></i></div>
                            <div>
                                <div class="stat-value">{{ $stats['tanpa_contoh'] }}</div>
                                <div class="stat-label">Belum Ada Contoh</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card stat-card purple">
                        <div class="d-flex align-items-center gap-3">
                            <div class="stat-icon purple"><i class="fas fa-question-circle"></i></div>
                            <div>
                                <div class="stat-value">{{ $stats['tanpa_arti'] }}</div>
                                <div class="stat-label">Belum Ada Arti</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== FILTER & TABLE CARD ===== --}}
            <div class="card filter-card shadow-sm">

                <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <h5 class="mb-0">
                        <i class="fas fa-list text-primary me-2 opacity-75"></i>Daftar Kosakata
                    </h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.kosakata.import') }}" class="btn btn-excel btn-sm">
                            <i class="fas fa-file-excel me-1"></i> Import Excel
                        </a>
                        <a href="{{ route('admin.kosakata.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i> Tambah Kosakata
                        </a>
                    </div>
                </div>

                {{-- Filter --}}
                <div class="filter-section">
                    <form id="form-filter" method="GET" action="{{ route('admin.kosakata.index') }}">
                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" name="search" id="input-search" class="form-control" autocomplete="off"
                                        placeholder="Cari hanzi / pinyin / arti..." value="{{ request('search') }}">
                                </div>
                            </div>

                            <div class="col-6 col-md-2">
                                <select name="kategori_id" class="form-select">
                                    <option value="">Semua Kategori</option>
                                    <option value="kosong" {{ request('kategori_id') === 'kosong' ? 'selected' : '' }}>Tanpa kategori</option>
                                    @foreach ($kategoris as $kategori)
                                        <option value="{{ $kategori->id }}" {{ (string) request('kategori_id') === (string) $kategori->id ? 'selected' : '' }}>
                                            {{ $kategori->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6 col-md-2">
                                <select name="level_tocfl_id" class="form-select">
                                    <option value="">Semua Level</option>
                                    <option value="kosong" {{ request('level_tocfl_id') === 'kosong' ? 'selected' : '' }}>Tanpa level</option>
                                    @foreach ($levels as $level)
                                        <option value="{{ $level->id }}" {{ (string) request('level_tocfl_id') === (string) $level->id ? 'selected' : '' }}>
                                            {{ $level->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6 col-md-2">
                                <select name="jenis_kata" class="form-select">
                                    <option value="">Semua Jenis</option>
                                    <option value="kosong" {{ request('jenis_kata') === 'kosong' ? 'selected' : '' }}>Tanpa jenis kata</option>
                                    @foreach ($jenisKata as $jenis)
                                        <option value="{{ $jenis }}" {{ request('jenis_kata') === $jenis ? 'selected' : '' }}>
                                            {{ \Illuminate\Support\Str::headline($jenis) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6 col-md-2">
                                <select name="lengkap" class="form-select">
                                    <option value="">Semua Kelengkapan</option>
                                    <option value="ada_contoh" {{ request('lengkap') === 'ada_contoh' ? 'selected' : '' }}>Ada contoh kalimat</option>
                                    <option value="tanpa_contoh" {{ request('lengkap') === 'tanpa_contoh' ? 'selected' : '' }}>Belum ada contoh</option>
                                    <option value="tanpa_arti" {{ request('lengkap') === 'tanpa_arti' ? 'selected' : '' }}>Belum ada arti Indonesia</option>
                                    <option value="tanpa_pecahan" {{ request('lengkap') === 'tanpa_pecahan' ? 'selected' : '' }}>Belum ada pecahan karakter</option>
                                </select>
                            </div>

                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-search me-1"></i> Filter
                                </button>
                                <a href="{{ route('admin.kosakata.index') }}" class="btn btn-outline-secondary btn-sm ms-1"
                                    title="Reset filter">
                                    <i class="fas fa-redo-alt"></i>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Tabel + pagination (di-refresh lewat AJAX) --}}
                <div id="tabel-wrap">
                    @fragment('tabel')
                {{-- Table --}}
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table-hover mb-0 table">
                            <thead>
                                <tr>
                                    <th width="40">#</th>
                                    <th>Hanzi</th>
                                    <th>Pinyin</th>
                                    <th>Arti</th>
                                    <th>Jenis</th>
                                    <th>Kategori</th>
                                    <th>Level</th>
                                    <th>Contoh</th>
                                    <th width="110">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kosakatas as $i => $kosakata)
                                    <tr>
                                        <td class="text-muted">{{ $kosakatas->firstItem() + $i }}</td>

                                        <td><span class="hanzi-text">{{ $kosakata->hanzi }}</span></td>

                                        <td>
                                            {{ $kosakata->pinyin }}
                                            @if ($kosakata->zhuyin)
                                                <div class="text-muted" style="font-size:.75rem;">{{ $kosakata->zhuyin }}</div>
                                            @endif
                                            @if ($kosakata->baca_indonesia)
                                                <div class="text-muted" style="font-size:.75rem;">{{ $kosakata->baca_indonesia }}</div>
                                            @endif
                                        </td>

                                        <td style="max-width:240px;">
                                            @if ($kosakata->arti_indonesia)
                                                {{ $kosakata->arti_indonesia }}
                                            @else
                                                <span class="text-muted fst-italic">Belum ada arti Indonesia</span>
                                            @endif
                                            @if ($kosakata->english)
                                                <div class="text-muted" style="font-size:.75rem;">{{ $kosakata->english }}</div>
                                            @endif
                                            @if ($kosakata->kegunaan)
                                                <div class="text-muted fst-italic" style="font-size:.75rem;" title="{{ $kosakata->kegunaan }}">{{ \Illuminate\Support\Str::limit($kosakata->kegunaan, 70) }}</div>
                                            @endif
                                        </td>

                                        <td>
                                            @if ($kosakata->jenis_kata)
                                                <span class="badge badge-jenis">{{ \Illuminate\Support\Str::headline($kosakata->jenis_kata) }}</span>
                                                @if ($kosakata->sub_jenis)
                                                    <div class="text-muted" style="font-size:.72rem;">{{ \Illuminate\Support\Str::headline($kosakata->sub_jenis) }}</div>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>

                                        <td class="text-muted">{{ $kosakata->kategori->nama ?? '-' }}</td>

                                        <td>
                                            @if ($kosakata->levelTocfl)
                                                <span class="badge badge-level">{{ $kosakata->levelTocfl->nama }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>

                                        <td>
                                            <span class="badge {{ $kosakata->contoh_kalimats_count > 0 ? 'badge-aktif' : 'badge-nonaktif' }}">
                                                {{ $kosakata->contoh_kalimats_count }} kalimat
                                            </span>
                                        </td>

                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('admin.kosakata.show', $kosakata) }}"
                                                    class="btn btn-action btn-detail" title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.kosakata.edit', $kosakata) }}"
                                                    class="btn btn-action btn-edit" title="Edit">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                                <button type="button" class="btn btn-action btn-hapus"
                                                    data-id="{{ $kosakata->id }}" data-hanzi="{{ $kosakata->hanzi }}"
                                                    data-jumlah="{{ $kosakata->contoh_kalimats_count }}"
                                                    data-soal="{{ $kosakata->soals_count }}" title="Hapus">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                                <form id="form-hapus-{{ $kosakata->id }}"
                                                    action="{{ route('admin.kosakata.destroy', $kosakata) }}" method="POST"
                                                    class="d-none">
                                                    @csrf @method('DELETE')
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="p-0 text-center">
                                            <div class="empty-state">
                                                <div class="empty-state-icon"><i class="fas fa-language"></i></div>
                                                <div class="fw-semibold text-secondary mb-1">Belum ada data kosakata</div>
                                                <div class="text-muted" style="font-size:.8rem;">
                                                    Coba ubah filter, tambahkan kosakata baru, atau import dari Excel
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Pagination --}}
                @if ($kosakatas->hasPages())
                    <div class="card-footer d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <small class="text-muted">
                            Menampilkan
                            <strong>{{ $kosakatas->firstItem() }}</strong>–<strong>{{ $kosakatas->lastItem() }}</strong>
                            dari <strong>{{ $kosakatas->total() }}</strong> data
                        </small>
                        {{ $kosakatas->links() }}
                    </div>
                @endif
                    @endfragment
                </div>

            </div>{{-- end .card --}}

        </div>{{-- end .page-inner --}}
    </div>{{-- end .container --}}
@endsection

@section('scripts')
    <script>
        (function () {
            const form = document.getElementById('form-filter');
            const wrap = document.getElementById('tabel-wrap');
            const search = document.getElementById('input-search');
            const baseUrl = form.getAttribute('action');
            let timer = null;
            let controller = null;

            // Ambil tabel baru dari server (hanya potongan tabel, bukan seluruh halaman).
            function muat(url, push = true) {
                if (controller) controller.abort();          // batalkan request lama yang belum selesai
                controller = new AbortController();
                wrap.style.opacity = '.5';

                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    signal: controller.signal,
                })
                    .then(r => { if (!r.ok) throw new Error(r.status); return r.text(); })
                    .then(html => {
                        wrap.innerHTML = html;
                        wrap.style.opacity = '1';
                        if (push) history.pushState(null, '', url);
                    })
                    .catch(e => {
                        if (e.name !== 'AbortError') wrap.style.opacity = '1';
                    });
            }

            function urlFilter(halaman) {
                const params = new URLSearchParams(new FormData(form));
                for (const [k, v] of [...params]) if (v === '') params.delete(k);
                if (halaman) params.set('page', halaman);
                const q = params.toString();
                return q ? baseUrl + '?' + q : baseUrl;
            }

            // Ketik -> tunggu 300ms setelah berhenti mengetik, baru cari.
            search.addEventListener('input', () => {
                clearTimeout(timer);
                timer = setTimeout(() => muat(urlFilter()), 300);
            });

            // Ganti dropdown -> langsung filter.
            form.querySelectorAll('select').forEach(el =>
                el.addEventListener('change', () => muat(urlFilter())));

            // Tombol Filter / Enter tidak reload halaman.
            form.addEventListener('submit', e => { e.preventDefault(); clearTimeout(timer); muat(urlFilter()); });

            // Tombol reset.
            form.querySelector('a[title="Reset filter"]').addEventListener('click', e => {
                e.preventDefault();
                form.reset();
                form.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
                search.value = '';
                muat(baseUrl);
            });

            // Pagination (tabel diganti lewat AJAX, jadi pakai event delegation).
            wrap.addEventListener('click', e => {
                const a = e.target.closest('.pagination a');
                if (!a) return;
                e.preventDefault();
                muat(a.href);
                window.scrollTo({ top: form.offsetTop - 80, behavior: 'smooth' });
            });

            // Tombol back/forward browser.
            window.addEventListener('popstate', () => muat(location.href, false));

            // Hapus (delegation, karena baris tabel diganti-ganti).
            wrap.addEventListener('click', e => {
                const btn = e.target.closest('.btn-hapus[data-id]');
                if (!btn) return;

                const id = btn.dataset.id;
                const hanzi = btn.dataset.hanzi;
                const jumlah = parseInt(btn.dataset.jumlah || 0);
                const soal = parseInt(btn.dataset.soal || 0);

                let teks = `Kosakata "${hanzi}" akan dihapus permanen.`;
                if (jumlah > 0) teks += ` ${jumlah} contoh kalimatnya ikut terhapus.`;
                teks += ' Pecahan karakter, relasi, keanggotaan grup, dan progres hafalan kata ini juga ikut terhapus.';
                if (soal > 0) teks += ` ${soal} soal yang memakai kata ini tidak ikut terhapus, tapi diberi status ditolak (disembunyikan dari latihan dan ujian).`;
                teks += ' Riwayat review tetap tersimpan.';

                swal({
                    title: 'Hapus Kosakata?',
                    text: teks,
                    icon: 'warning',
                    buttons: { cancel: 'Batal', confirm: { text: 'Ya, Hapus!', className: 'btn-danger' } },
                    dangerMode: true,
                }).then(ok => { if (ok) document.getElementById('form-hapus-' + id).submit(); });
            });
        })();
    </script>
@endsection
