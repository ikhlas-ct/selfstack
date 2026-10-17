@extends('layouts.user.user')

@section('title', 'Hafalan Saya')

@section('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    body, .card, .btn, h4, h5 { font-family: 'Plus Jakarta Sans', sans-serif; }

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

    .hanzi-text { font-family: 'Noto Sans TC', 'PingFang TC', 'Microsoft JhengHei', 'Plus Jakarta Sans', sans-serif; }

    /* ---------- pil status ---------- */
    .pil-status { display: flex; flex-wrap: wrap; gap: 12px; margin: 8px 0 18px; }
    .pil { border: 1px solid #e2e8f0; background: #fff; color: #334155; border-radius: 999px; padding: 10px 24px; font-size: .88rem; font-weight: 600; cursor: pointer; transition: border-color .15s, background .15s, color .15s; }
    .pil:hover { border-color: #93c5fd; }
    .pil.aktif { background: #eaf2ff; border-color: #1a73e8; color: #1a73e8; }

    /* ---------- bar filter ---------- */
    .bar-filter { background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 16px; padding: 18px 24px; }
    .bar-filter .form-control, .bar-filter .form-select { border-radius: 10px; font-size: .85rem; border-color: #e2e8f0; height: 44px; }
    .bar-filter .form-control:focus, .bar-filter .form-select:focus { border-color: #1a73e8; box-shadow: 0 0 0 3px rgba(26,115,232,.12); }
    .kolom-cari { position: relative; }
    .kolom-cari i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: .85rem; }
    .kolom-cari .form-control { padding-left: 40px; }
    .btn-reset { border: 1px solid #e2e8f0; background: #fff; color: #64748b; border-radius: 10px; font-size: .83rem; height: 44px; padding: 0 18px; }
    .btn-reset:hover { background: #f1f5f9; color: #334155; }

    .info-tampil { font-size: .85rem; color: #64748b; margin: 20px 0 14px; }

    /* ---------- grid kartu ---------- */
    #areaDaftar { transition: opacity .15s; }
    #areaDaftar.memuat { opacity: .45; pointer-events: none; }
    .grid-kata { display: grid; grid-template-columns: repeat(auto-fill, minmax(190px, 1fr)); gap: 18px; }

    .kartu-kata { --warna: #94a3b8; position: relative; background: #fff; border: 1px solid #e2e8f0; border-left: 4px solid var(--warna); border-radius: 14px; padding: 14px 12px 12px; text-align: center; cursor: pointer; transition: transform .15s, box-shadow .15s; }
    .kartu-kata:hover, .kartu-kata:focus-visible { transform: translateY(-2px); box-shadow: 0 6px 18px rgba(0,0,0,.09); outline: none; }
    .kartu-kata.st-ingat_sepenuhnya { --warna: #16a34a; }
    .kartu-kata.st-lupa_dan_ingat   { --warna: #d97706; }
    .kartu-kata.st-berikutnya       { --warna: #94a3b8; }

    .kartu-pinyin { color: #1a73e8; font-weight: 600; font-size: .92rem; min-height: 1.3em; }
    .kartu-hanzi  { font-size: 2.1rem; font-weight: 800; color: #1e293b; line-height: 1.25; margin: 2px 0; }
    .kartu-arti   { font-size: .88rem; color: #334155; }
    .kartu-kategori { font-size: .74rem; color: #94a3b8; margin-top: 2px; min-height: 1.1em; }
    .kartu-badges { display: flex; flex-wrap: wrap; justify-content: center; gap: 6px; margin-top: 10px; }
    .kartu-mata { position: absolute; top: 10px; right: 10px; width: 34px; height: 30px; border-radius: 8px; background: #f1f5f9; color: #64748b; display: none; align-items: center; justify-content: center; font-size: .8rem; }
    .kartu-kata:hover .kartu-mata, .kartu-kata:focus-visible .kartu-mata { display: flex; }

    .badge { font-size: .7rem; font-weight: 600; padding: 4px 10px; border-radius: 6px; letter-spacing: .2px; }
    .badge-level { background: #e8f0fe; color: #1a73e8; }
    .badge-st-ingat_sepenuhnya { background: #dcfce7; color: #15803d; }
    .badge-st-lupa_dan_ingat   { background: #fef3c7; color: #b45309; }
    .badge-st-berikutnya       { background: #f1f5f9; color: #64748b; }
    .badge-aktif { background: #dcfce7; color: #15803d; }
    .badge-warn { background: #fef3c7; color: #b45309; }
    .badge-danger-soft { background: #fee2e2; color: #b91c1c; }

    .empty-state { padding: 60px 20px; text-align: center; grid-column: 1 / -1; }
    .empty-state-icon { width: 72px; height: 72px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px; font-size: 1.6rem; color: #94a3b8; }

    /* ---------- popup ---------- */
    #modalKata .modal-dialog { max-width: 980px; }
    #modalKata .modal-content { border: none; border-radius: 20px; overflow: hidden; }
    #modalKata .modal-body { padding: 36px 38px 28px; }
    #modalKata .tutup { position: absolute; top: 18px; right: 22px; z-index: 5; width: 50px; height: 50px; border-radius: 50%; border: 1px solid #e2e8f0; background: #fff; color: #334155; font-size: 1rem; display: flex; align-items: center; justify-content: center; }
    #modalKata .tutup:hover { background: #f1f5f9; }
    #modalKata .modal-footer { border-top: 1px solid #f1f5f9; background: #f8fafc; justify-content: space-between; padding: 12px 24px; }
    .nav-kata { width: 48px; height: 44px; border: 1px solid #e2e8f0; background: #fff; border-radius: 10px; color: #334155; }
    .nav-kata:disabled { opacity: .4; cursor: default; }
    .link-detail { color: #1a73e8; font-weight: 600; font-size: .9rem; text-decoration: none; }
    .link-detail:hover { text-decoration: underline; }

    .pop-hanzi  { font-size: 4.2rem; font-weight: 800; line-height: 1.1; color: #1e293b; }
    .pop-pinyin { font-size: 1.6rem; font-weight: 600; color: #1a73e8; margin-top: 4px; }

    .info-grid { display: grid; grid-template-columns: 170px 1fr; row-gap: 12px; margin-top: 26px; font-size: .98rem; }
    .info-grid .lbl { color: #94a3b8; }
    .info-grid .val { color: #1e293b; }
    .info-grid .val.teks-panjang { white-space: pre-line; line-height: 1.6; }

    .pindah-label { font-weight: 700; font-size: .9rem; margin: 28px 0 10px; color: #1e293b; }
    .pindah-btn { border: 1px solid #e2e8f0; background: #fff; color: #1e293b; border-radius: 12px; padding: 12px 24px; font-weight: 600; font-size: .95rem; margin-right: 8px; margin-bottom: 8px; }
    .pindah-btn:hover { border-color: #93c5fd; }
    .pindah-btn.aktif { background: #64748b; border-color: #64748b; color: #fff; }
    .pindah-btn:disabled { opacity: .6; }

    .kotak-contoh { background: #eaf1fb; border-radius: 18px; padding: 22px 24px; margin-top: 34px; }
    .kotak-contoh .judul { font-weight: 700; font-size: .9rem; color: #1e293b; margin-bottom: 14px; }
    .contoh-item { padding: 16px 0; border-bottom: 1px solid #d6e2f3; }
    .contoh-item:first-of-type { padding-top: 6px; }
    .contoh-item:last-child { border-bottom: none; padding-bottom: 0; }
    .contoh-hanzi  { font-size: 1.5rem; font-weight: 700; color: #1e293b; line-height: 1.4; }
    .contoh-pinyin { color: #1a73e8; font-weight: 500; margin-top: 2px; }
    .contoh-arti   { color: #334155; margin-top: 2px; }
    .contoh-catatan { color: #94a3b8; font-style: italic; font-size: .85rem; margin-top: 4px; }
    .btn-dengar-kecil { margin-top: 8px; border: 1px solid #e2e8f0; background: #fff; color: #334155; border-radius: 10px; padding: 7px 16px; font-size: .88rem; }
    .btn-dengar-kecil:hover { background: #f1f5f9; }

    .aksi-hanzi { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 26px; }
    .aksi-hanzi .btn-aksi { border: 1px solid #e2e8f0; background: #fff; color: #334155; border-radius: 12px; padding: 13px 22px; font-weight: 600; font-size: .95rem; }
    .aksi-hanzi .btn-aksi:hover { background: #f1f5f9; }
    .aksi-hanzi .btn-aksi.utama { background: #1565c0; border-color: #1565c0; color: #fff; }
    .aksi-hanzi .btn-aksi.utama:hover { background: #0f4f9a; }

    .area-penulis { display: flex; flex-wrap: wrap; gap: 14px; margin-top: 18px; }
    .kotak-huruf { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; cursor: pointer; line-height: 0;
        background-image: linear-gradient(to right, transparent calc(50% - .5px), #e2e8f0 calc(50% - .5px), #e2e8f0 calc(50% + .5px), transparent calc(50% + .5px)),
                          linear-gradient(to bottom, transparent calc(50% - .5px), #e2e8f0 calc(50% - .5px), #e2e8f0 calc(50% + .5px), transparent calc(50% + .5px)); }
    .petunjuk-kotak { font-size: .82rem; color: #64748b; margin-top: 10px; }

    .sub-judul { font-weight: 700; font-size: .9rem; color: #1e293b; margin: 34px 0 12px; }
    .stat-mini { display: flex; gap: 12px; text-align: center; margin-bottom: 12px; }
    .stat-mini > div { flex: 1; background: #f8fafc; border-radius: 12px; padding: 12px 6px; }
    .stat-mini .angka { font-size: 1.4rem; font-weight: 800; color: #1e293b; line-height: 1.1; }
    .stat-mini .nama { font-size: .7rem; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: #94a3b8; margin-top: 2px; }
    .riwayat-item { padding: 10px 0; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; justify-content: space-between; gap: 10px; }
    .riwayat-item:last-child { border-bottom: none; }

    @media (max-width: 576px) {
        #modalKata .modal-body { padding: 28px 20px 20px; }
        .pop-hanzi { font-size: 3.2rem; }
        .info-grid { grid-template-columns: 110px 1fr; }
        .pil { padding: 8px 16px; }
    }
</style>
@endsection

@section('content')
    <div class="container">

        <div class="ph-card">
            <div class="ph-left">
                <div class="ph-icon"><i class="fas fa-chart-pie"></i></div>
                <div>
                    <h5 class="ph-title">Hafalan Saya</h5>
                    <ol class="ph-breadcrumb" aria-label="breadcrumb">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><span class="bc-active">Hafalan Saya</span></li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="page-inner">

            {{-- Pil status: jumlahnya selalu total keseluruhan, tidak ikut filter --}}
            <div class="pil-status" id="pilStatus">
                <button type="button" class="pil {{ $status === null ? 'aktif' : '' }}" data-status="">
                    Semua (<span data-hitung="semua">{{ $totalKosakata }}</span>)
                </button>
                <button type="button" class="pil {{ $status === 'ingat_sepenuhnya' ? 'aktif' : '' }}" data-status="ingat_sepenuhnya">
                    Ingat Sepenuhnya (<span data-hitung="ingat_sepenuhnya">{{ $ingat }}</span>)
                </button>
                <button type="button" class="pil {{ $status === 'lupa_dan_ingat' ? 'aktif' : '' }}" data-status="lupa_dan_ingat">
                    Lupa &amp; Ingat (<span data-hitung="lupa_dan_ingat">{{ $lupa }}</span>)
                </button>
                <button type="button" class="pil {{ $status === 'berikutnya' ? 'aktif' : '' }}" data-status="berikutnya">
                    Berikutnya (<span data-hitung="berikutnya">{{ $berikutnya }}</span>)
                </button>
            </div>

            <div class="bar-filter">
                <form method="GET" id="formCari" class="row g-3 align-items-center">
                    <input type="hidden" name="status" value="{{ $status }}">

                    <div class="col-lg-5">
                        <div class="kolom-cari">
                            <i class="fas fa-search"></i>
                            <input type="text" name="search" class="form-control" placeholder="Ketik hanzi / pinyin / arti..." value="{{ $cari }}" autocomplete="off">
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <select name="kategori" class="form-select">
                            <option value="">Semua Kategori</option>
                            @foreach ($kategoris as $kt)
                                <option value="{{ $kt->id }}" @selected($kategori === $kt->id)>{{ $kt->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-6">
                        <select name="level" class="form-select">
                            <option value="">Semua Level</option>
                            @foreach ($levels as $lv)
                                <option value="{{ $lv->id }}" @selected($level === $lv->id)>TOCFL {{ $lv->tingkat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-lg-2 d-grid">
                        <button type="button" class="btn-reset" id="btnReset"><i class="fas fa-redo me-1"></i> Reset</button>
                    </div>
                </form>
            </div>

            @fragment('daftar-kata')
            <div id="areaDaftar">
                <div class="info-tampil">
                    Menampilkan <strong>{{ $kosakatas->count() }}</strong> dari <strong>{{ $totalCocok }}</strong> kata
                    @if ($totalCocok > $kosakatas->count())
                        <span class="ms-1">· ketik di pencarian untuk menemukan kata lainnya</span>
                    @endif
                </div>

                <div class="grid-kata">
                    @forelse ($kosakatas as $k)
                        @php
                            $st = $k->progres->status ?? 'berikutnya';
                            $labelSt = \App\Models\ProgresHafalan::LABEL[$st] ?? $st;
                        @endphp
                        <div class="kartu-kata st-{{ $st }}" role="button" tabindex="0"
                             data-id="{{ $k->id }}" data-status="{{ $st }}"
                             data-url="{{ route('admin.hafalan.riwayat', $k->id) }}">
                            <span class="kartu-mata"><i class="fas fa-eye"></i></span>
                            <div class="kartu-pinyin">{{ $k->pinyin }}</div>
                            <div class="kartu-hanzi hanzi-text">{{ $k->hanzi }}</div>
                            <div class="kartu-arti">{{ $k->arti_pilihan }}</div>
                            <div class="kartu-kategori">{{ $k->kategori->nama ?? '' }}</div>
                            <div class="kartu-badges">
                                @if ($k->levelTocfl)
                                    <span class="badge badge-level">TOCFL {{ $k->levelTocfl->tingkat }}</span>
                                @endif
                                <span class="badge badge-st-{{ $st }} badge-status">{{ $labelSt }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <div class="empty-state-icon"><i class="fas fa-search"></i></div>
                            <div class="fw-semibold text-secondary mb-1">Tidak ada kata yang cocok</div>
                            <div class="text-muted" style="font-size:.8rem;">Coba ubah kata kunci, kategori, level, atau status</div>
                        </div>
                    @endforelse
                </div>
            </div>
            @endfragment

        </div>
    </div>

    {{-- ================= POPUP DETAIL KATA ================= --}}
    <div class="modal fade" id="modalKata" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <button type="button" class="tutup" data-bs-dismiss="modal" aria-label="Tutup"><i class="fas fa-times"></i></button>
                <div class="modal-body" id="isiModalKata"></div>
                <div class="modal-footer">
                    <button type="button" class="nav-kata" id="btnSebelumnya" aria-label="Kata sebelumnya"><i class="fas fa-chevron-left"></i></button>
                    <a href="#" class="link-detail" id="linkDetail" target="_blank" rel="noopener"><i class="fas fa-external-link-alt me-2"></i>Buka halaman detail</a>
                    <button type="button" class="nav-kata" id="btnBerikutnya" aria-label="Kata berikutnya"><i class="fas fa-chevron-right"></i></button>
                </div>
            </div>
        </div>
    </div>

    {{-- Animasi goresan & latihan menulis (butuh internet untuk memuat library + data goresan) --}}
    <script src="https://cdn.jsdelivr.net/npm/hanzi-writer@3/dist/hanzi-writer.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const CSRF  = @json(csrf_token());
            const LABEL = @json(\App\Models\ProgresHafalan::LABEL);

            const form         = document.getElementById('formCari');
            const inputCari    = form.querySelector('[name=search]');
            const inputStatus  = form.querySelector('[name=status]');
            const selKategori  = form.querySelector('[name=kategori]');
            const selLevel     = form.querySelector('[name=level]');
            const pils         = document.querySelectorAll('#pilStatus .pil');
            let timer = null, controller = null;

            const esc = (t) => String(t ?? '').replace(/[&<>"']/g, c => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
            }[c]));

            /* ============ pencarian & filter (AJAX) ============ */
            function urlDariForm() {
                const p = new URLSearchParams();
                new FormData(form).forEach((v, k) => { if (String(v).trim() !== '') p.set(k, String(v).trim()); });
                const q = p.toString();
                return window.location.pathname + (q ? '?' + q : '');
            }

            function sinkronPil() {
                pils.forEach(p => p.classList.toggle('aktif', p.dataset.status === inputStatus.value));
            }

            async function muat(url) {
                const area = document.getElementById('areaDaftar');
                area.classList.add('memuat');

                controller?.abort();
                controller = new AbortController();

                try {
                    const res = await fetch(url, {
                        signal: controller.signal,
                        cache: 'no-store',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    });
                    if (!res.ok) throw new Error();

                    const doc  = new DOMParser().parseFromString(await res.text(), 'text/html');
                    const baru = doc.getElementById('areaDaftar');
                    if (!baru) throw new Error();

                    area.replaceWith(baru);
                    history.replaceState(null, '', url);
                    sinkronPil();
                } catch (err) {
                    if (err.name === 'AbortError') return;
                    area.classList.remove('memuat');
                    window.location.href = url;
                }
            }

            const cari = () => muat(urlDariForm());

            inputCari.addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(cari, 350); });
            selKategori.addEventListener('change', cari);
            selLevel.addEventListener('change', cari);
            form.addEventListener('submit', (e) => { e.preventDefault(); clearTimeout(timer); cari(); });

            pils.forEach(p => p.addEventListener('click', () => {
                inputStatus.value = p.dataset.status;
                sinkronPil();
                cari();
            }));

            document.getElementById('btnReset').addEventListener('click', () => {
                inputCari.value = ''; selKategori.value = ''; selLevel.value = ''; inputStatus.value = '';
                sinkronPil();
                clearTimeout(timer);
                cari();
            });

            /* ============ suara (text-to-speech browser) ============ */
            function bicara(teks, kecepatan = 0.9) {
                if (!('speechSynthesis' in window)) { alert('Browser ini tidak mendukung suara.'); return; }
                speechSynthesis.cancel();
                const u = new SpeechSynthesisUtterance(teks);
                u.lang = 'zh-TW';
                u.rate = kecepatan;
                const suara = speechSynthesis.getVoices();
                const v = suara.find(s => /zh[-_]TW/i.test(s.lang)) || suara.find(s => /^zh/i.test(s.lang));
                if (v) u.voice = v;
                speechSynthesis.speak(u);
            }

            /* ============ popup detail ============ */
            const modalEl   = document.getElementById('modalKata');
            const modal     = bootstrap.Modal.getOrCreateInstance(modalEl);
            const isi       = document.getElementById('isiModalKata');
            const btnPrev   = document.getElementById('btnSebelumnya');
            const btnNext   = document.getElementById('btnBerikutnya');
            const linkDetail = document.getElementById('linkDetail');

            let daftarKartu = [], idx = -1, tokenMuat = 0, data = null, penulis = [], sesiAnimasi = 0;
            const cache = new Map();

            function bukaKartu(kartu) {
                daftarKartu = [...document.querySelectorAll('#areaDaftar .kartu-kata')];
                idx = daftarKartu.indexOf(kartu);
                if (idx < 0) return;
                tampil();
                modal.show();
            }

            document.addEventListener('click', (e) => {
                const kartu = e.target.closest('#areaDaftar .kartu-kata');
                if (kartu) bukaKartu(kartu);
            });
            document.addEventListener('keydown', (e) => {
                if ((e.key === 'Enter' || e.key === ' ') && e.target.classList?.contains('kartu-kata')) {
                    e.preventDefault();
                    bukaKartu(e.target);
                }
            });

            btnPrev.addEventListener('click', () => { if (idx > 0) { idx--; tampil(); } });
            btnNext.addEventListener('click', () => { if (idx < daftarKartu.length - 1) { idx++; tampil(); } });
            modalEl.addEventListener('keydown', (e) => {
                if (e.target.closest('input, textarea')) return;
                if (e.key === 'ArrowLeft')  btnPrev.click();
                if (e.key === 'ArrowRight') btnNext.click();
            });
            modalEl.addEventListener('hidden.bs.modal', () => {
                sesiAnimasi++;
                speechSynthesis?.cancel();
                penulis = [];
                isi.innerHTML = '';
            });

            async function ambil(url) {
                if (cache.has(url)) return cache.get(url);
                const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                if (!res.ok) throw new Error();
                const d = await res.json();
                cache.set(url, d);
                return d;
            }

            async function tampil() {
                const kartu = daftarKartu[idx];
                const token = ++tokenMuat;
                sesiAnimasi++;
                speechSynthesis?.cancel();

                btnPrev.disabled = idx <= 0;
                btnNext.disabled = idx >= daftarKartu.length - 1;
                isi.innerHTML = '<div class="text-center text-muted py-5"><i class="fas fa-spinner fa-spin me-2"></i>Memuat...</div>';
                isi.scrollTop = 0;

                try {
                    const d = await ambil(kartu.dataset.url);
                    if (token !== tokenMuat) return;
                    data = d;
                    render(d);
                } catch (err) {
                    if (token !== tokenMuat) return;
                    isi.innerHTML = '<div class="text-center text-danger py-5">Gagal memuat detail kata. Coba lagi.</div>';
                }
            }

            function baris(label, nilai, kelas = '') {
                if (nilai === null || nilai === undefined || String(nilai).trim() === '') return '';
                return `<div class="lbl">${esc(label)}</div><div class="val ${kelas}">${nilai}</div>`;
            }

            function render(d) {
                linkDetail.style.display = d.url_detail ? '' : 'none';
                if (d.url_detail) linkDetail.href = d.url_detail;

                const pindah = ['berikutnya', 'lupa_dan_ingat', 'ingat_sepenuhnya']
                    .map(k => `<button type="button" class="pindah-btn ${d.status.kunci === k ? 'aktif' : ''}" data-aksi="pindah" data-kunci="${esc(k)}">${esc(LABEL[k])}</button>`).join('');

                const contoh = d.contoh.length ? `
                    <div class="kotak-contoh">
                        <div class="judul">Contoh pemakaian (${d.contoh.length})</div>
                        ${d.contoh.map((c, i) => `
                            <div class="contoh-item">
                                <div class="contoh-hanzi hanzi-text">${esc(c.hanzi)}</div>
                                <div class="contoh-pinyin">${esc(c.pinyin)}</div>
                                <div class="contoh-arti">${esc(c.arti)}</div>
                                ${c.catatan ? `<div class="contoh-catatan">${esc(c.catatan)}</div>` : ''}
                                <button type="button" class="btn-dengar-kecil" data-aksi="dengar-contoh" data-i="${i}"><i class="fas fa-volume-up me-2"></i>Dengar</button>
                            </div>`).join('')}
                    </div>` : '';

                const jadwal = d.review_berikutnya
                    ? `${esc(d.review_berikutnya.tanggal)} ${d.review_berikutnya.lewat ? '<span class="badge badge-danger-soft ms-1">Jatuh tempo</span>' : ''}`
                    : '';

                const riwayat = d.riwayat.length
                    ? d.riwayat.map(r => `
                        <div class="riwayat-item">
                            <div>
                                <div class="fw-semibold" style="font-size:.88rem;">${esc(r.waktu)}</div>
                                <div class="text-muted" style="font-size:.76rem;">${esc(r.relatif)} · ${r.sumber === 'soal' ? 'dari soal' : 'flashcard'} · status saat itu: ${esc(r.status_saat_review)}</div>
                            </div>
                            <span class="badge ${r.benar ? 'badge-aktif' : 'badge-warn'}">${r.benar ? 'Ingat' : 'Lupa'}</span>
                        </div>`).join('')
                    : '<div class="text-muted py-3" style="font-size:.88rem;">Kata ini belum pernah direview.</div>';

                isi.innerHTML = `
                    <div class="pop-hanzi hanzi-text">${esc(d.hanzi)}</div>
                    <div class="pop-pinyin">${esc(d.pinyin)}</div>

                    <div class="info-grid">
                        ${baris('Arti', esc(d.arti))}
                        ${baris('Cara baca (Indo)', esc(d.baca_indonesia))}
                        ${baris('Inggris', esc(d.english))}
                        ${baris('Kategori', esc(d.kategori))}
                        ${baris('Level', esc(d.level))}
                        ${baris('Status', `<span class="badge badge-st-${esc(d.status.kunci)}" id="badgeStatusPopup">${esc(d.status.label)}</span>`)}
                        ${baris('Kegunaan', esc(d.kegunaan), 'teks-panjang')}
                    </div>

                    <div class="pindah-label">Pindahkan ke:</div>
                    <div id="tombolPindah">${pindah}</div>

                    ${contoh}

                    <div class="aksi-hanzi">
                        <button type="button" class="btn-aksi utama" data-aksi="dengar"><i class="fas fa-volume-up me-2"></i>Dengar</button>
                        <button type="button" class="btn-aksi" data-aksi="pelan"><i class="fas fa-volume-down me-2"></i>Pelan</button>
                        <button type="button" class="btn-aksi" data-aksi="animasi"><i class="fas fa-play me-2"></i>Animasi goresan</button>
                        <button type="button" class="btn-aksi" data-aksi="latihan"><i class="fas fa-pen me-2"></i>Latihan menulis</button>
                    </div>
                    <div class="area-penulis" id="areaPenulis"></div>
                    <div class="petunjuk-kotak">Klik kotak huruf untuk memutar animasi satu karakter.</div>

                    <div class="sub-judul">Riwayat review</div>
                    <div class="stat-mini">
                        <div><div class="angka">${d.total}</div><div class="nama">Total review</div></div>
                        <div><div class="angka">${d.akurasi !== null ? d.akurasi + '%' : '-'}</div><div class="nama">Akurasi</div></div>
                        <div><div class="angka">${d.beruntun}×</div><div class="nama">Ingat beruntun</div></div>
                    </div>
                    ${jadwal ? `<div class="mb-2" style="font-size:.88rem;"><span class="text-muted">Review berikutnya:</span> <strong>${jadwal}</strong></div>` : ''}
                    <div>${riwayat}</div>`;

                siapkanPenulis(d.hanzi);
            }

            /* ============ aksi di dalam popup (delegasi klik) ============ */
            isi.addEventListener('click', (e) => {
                const el = e.target.closest('[data-aksi]');
                if (!el || !data) return;

                switch (el.dataset.aksi) {
                    case 'dengar':        bicara(data.hanzi, 0.9); break;
                    case 'pelan':         bicara(data.hanzi, 0.5); break;
                    case 'dengar-contoh': bicara(data.contoh[Number(el.dataset.i)].hanzi, 0.9); break;
                    case 'animasi':       animasiSemua(); break;
                    case 'latihan':       latihanSemua(); break;
                    case 'pindah':        pindahStatus(el.dataset.kunci, el); break;
                }
            });

            /* ============ pindah status ============ */
            async function pindahStatus(kunci, tombol) {
                if (kunci === data.status.kunci) return;
                const semua = isi.querySelectorAll('.pindah-btn');
                semua.forEach(b => b.disabled = true);

                try {
                    const res = await fetch(data.url_status, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': CSRF,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ status: kunci }),
                    });
                    if (!res.ok) throw new Error();
                    const hasil = await res.json();

                    const lama = data.status.kunci;
                    data.status = { kunci: hasil.kunci, label: hasil.label };

                    // popup
                    semua.forEach(b => b.classList.toggle('aktif', b.dataset.kunci === hasil.kunci));
                    const bp = document.getElementById('badgeStatusPopup');
                    if (bp) { bp.className = 'badge badge-st-' + hasil.kunci; bp.textContent = hasil.label; }

                    // kartu di grid
                    const kartu = daftarKartu[idx];
                    if (kartu) {
                        kartu.className = 'kartu-kata st-' + hasil.kunci;
                        kartu.dataset.status = hasil.kunci;
                        const bk = kartu.querySelector('.badge-status');
                        if (bk) { bk.className = 'badge badge-st-' + hasil.kunci + ' badge-status'; bk.textContent = hasil.label; }
                    }

                    // jumlah di pil
                    const ubah = (k, n) => {
                        const s = document.querySelector(`[data-hitung="${k}"]`);
                        if (s) s.textContent = Math.max(Number(s.textContent) + n, 0);
                    };
                    ubah(lama, -1);
                    ubah(hasil.kunci, +1);
                } catch (err) {
                    alert('Gagal memindahkan status. Coba lagi.');
                } finally {
                    semua.forEach(b => b.disabled = false);
                }
            }

            /* ============ animasi goresan & latihan menulis ============ */
            function siapkanPenulis(hanzi) {
                const area = document.getElementById('areaPenulis');
                penulis = [];
                if (!area) return;

                const huruf = [...hanzi].filter(c => /\p{Script=Han}/u.test(c));
                if (!huruf.length) return;

                if (typeof HanziWriter === 'undefined') {
                    area.innerHTML = '<div class="text-muted" style="font-size:.85rem;">Library goresan belum termuat (perlu koneksi internet).</div>';
                    return;
                }

                const ukuran = huruf.length === 1 ? 200 : 120;
                huruf.forEach(c => {
                    const kotak = document.createElement('div');
                    kotak.className = 'kotak-huruf';
                    area.appendChild(kotak);

                    const w = HanziWriter.create(kotak, c, {
                        width: ukuran, height: ukuran, padding: 12,
                        showOutline: true,
                        strokeColor: '#1e293b',
                        radicalColor: '#1a73e8',
                        outlineColor: '#e2e8f0',
                        drawingColor: '#1a73e8',
                        strokeAnimationSpeed: 1,
                        delayBetweenStrokes: 200,
                        onLoadCharDataError: () => {
                            kotak.dataset.error = '1';
                            kotak.style.width = ukuran + 'px';
                            kotak.style.height = ukuran + 'px';
                            kotak.style.lineHeight = '1.3';
                            kotak.innerHTML = '<div class="text-muted p-3" style="font-size:.78rem;">Data goresan untuk ' + esc(c) + ' tidak tersedia.</div>';
                        },
                    });
                    penulis.push({ w, kotak });
                    kotak.addEventListener('click', () => {
                        sesiAnimasi++;
                        w.cancelQuiz?.();
                        w.showCharacter();
                        w.animateCharacter();
                    });
                });
            }

            function hentikanSemua() {
                sesiAnimasi++;
                penulis.forEach(({ w, kotak }) => {
                    if (kotak.dataset.error) return;
                    w.cancelQuiz?.();
                    w.showCharacter();
                });
            }

            async function animasiSemua() {
                hentikanSemua();
                const sesi = sesiAnimasi;
                for (const { w, kotak } of penulis) {
                    if (sesi !== sesiAnimasi) return;
                    if (kotak.dataset.error) continue;
                    await new Promise(res => w.animateCharacter({ onComplete: res }));
                }
            }

            async function latihanSemua() {
                hentikanSemua();
                const sesi = sesiAnimasi;
                for (const { w, kotak } of penulis) {
                    if (sesi !== sesiAnimasi) return;
                    if (kotak.dataset.error) continue;
                    await new Promise(res => w.quiz({ onComplete: res }));
                    if (sesi !== sesiAnimasi) return;
                    w.showCharacter();
                }
            }
        });
    </script>
@endsection
