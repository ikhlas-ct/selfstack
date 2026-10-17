@extends('layouts.user.user')

@section('title', 'Hasil ' . $grup->judul)

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

    /* ===== HASIL ===== */
    .skor-card { display: flex; align-items: center; gap: 24px; flex-wrap: wrap; }
    .skor-ring { --p: 0; --warna: var(--gk-blue); width: 132px; height: 132px; border-radius: 50%; background: conic-gradient(var(--warna) calc(var(--p) * 1%), #e2e8f0 0); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .skor-ring.lulus { --warna: #16a34a; }
    .skor-ring.gagal { --warna: #dc2626; }
    .skor-ring > div { width: 100px; height: 100px; border-radius: 50%; background: #fff; display: flex; flex-direction: column; align-items: center; justify-content: center; }
    .skor-ring .angka { font-size: 1.9rem; font-weight: 800; color: var(--gk-ink); line-height: 1; letter-spacing: -1px; }
    .skor-ring .cap { font-size: .68rem; color: var(--gk-faint); margin-top: 3px; }
    .skor-info { min-width: 0; flex: 1 1 240px; }
    .skor-judul { font-size: 1.1rem; font-weight: 800; color: var(--gk-ink); margin: 0 0 4px; }
    .skor-sub { font-size: .82rem; color: var(--gk-muted); }
    .bagian-bar { margin-top: 12px; }
    .bagian-bar .baris { display: flex; align-items: center; gap: 10px; font-size: .78rem; color: #475569; margin-bottom: 6px; }
    .bagian-bar .nama { width: 96px; flex-shrink: 0; font-weight: 600; }
    .bagian-bar .trek { flex: 1; height: 8px; background: #e2e8f0; border-radius: 999px; overflow: hidden; }
    .bagian-bar .isi { height: 100%; background: #16a34a; border-radius: 999px; }
    .bagian-bar .nilai { width: 44px; text-align: right; font-variant-numeric: tabular-nums; color: var(--gk-muted); }

    .tab-filter { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 14px; }
    .tab-filter a { padding: 6px 14px; border: 1.5px solid var(--gk-line); border-radius: 999px; font-size: .78rem; font-weight: 600; color: var(--gk-muted); text-decoration: none; background: #fff; }
    .tab-filter a:hover { border-color: #93c5fd; }
    .tab-filter a.aktif { background: #e8f0fe; border-color: var(--gk-blue); color: var(--gk-blue); }

    .review-item { background: #fff; border: 1px solid #e8edf3; border-radius: 14px; padding: 16px; margin-bottom: 12px; scroll-margin-top: 80px; }
    .review-item .opsi-pilih { padding: 8px 12px; font-size: .92rem; }
    .review-item .opsi-list { gap: 6px; }
    .review-item .soal-tanya { font-size: 1.05rem; margin-bottom: 12px; }
    .kamu-tag { font-family: 'Plus Jakarta Sans', sans-serif; font-size: .68rem; font-weight: 700; padding: 2px 7px; border-radius: 6px; background: rgba(0,0,0,.07); margin-left: auto; flex-shrink: 0; align-self: center; }
    .jelas-box { margin-top: 10px; font-size: .8rem; color: #475569; background: var(--gk-soft); border-radius: 10px; padding: 10px 12px; }

    details.lapor { margin-top: 12px; border-top: 1px dashed #dbe3ec; padding-top: 8px; }
    details.lapor > summary { cursor: pointer; font-size: .78rem; font-weight: 600; color: var(--gk-muted); list-style: none; display: inline-flex; align-items: center; gap: 6px; }
    details.lapor > summary::-webkit-details-marker { display: none; }
    details.lapor > summary:hover { color: var(--gk-blue); }
    details.lapor > summary:focus-visible { outline: 3px solid rgba(26,115,232,.35); outline-offset: 2px; border-radius: 4px; }
    .lapor-form { display: grid; gap: 10px; margin-top: 10px; max-width: 520px; }

    /* ===== SUARA (TEXT-TO-SPEECH) ===== */
    .tts-bar { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; width: 100%; }
    .tts-bar[hidden] { display: none; }
    .tts-bar .form-select { flex: 1 1 220px; max-width: 380px; font-size: .78rem; padding: 5px 30px 5px 10px; }
    .tts-info { font-size: .74rem; color: var(--gk-faint); }

/* ===== POLES HALAMAN HASIL ===== */
    .skor-card { gap: 28px; }
    .skor-ring { width: 150px; height: 150px; }
    .skor-ring > div { width: 118px; height: 118px; }
    .skor-ring .angka { font-size: 2.3rem; }
    .skor-judul { font-size: 1.35rem; letter-spacing: -.3px; }
    .stat-row .stat-box:nth-child(1) .stat-num { color: #15803d; }
    .stat-row .stat-box:nth-child(2) .stat-num { color: #dc2626; }
    .stat-row .stat-box:nth-child(3) .stat-num { color: var(--gk-muted); }

    .peta-jawab { display: grid; grid-template-columns: repeat(auto-fill, minmax(38px, 1fr)); gap: 6px; }
    .peta-no { height: 38px; border-radius: 10px; border: 1.5px solid var(--gk-line); background: #fff; font-size: .78rem; font-weight: 700; color: var(--gk-muted); display: inline-flex; align-items: center; justify-content: center; text-decoration: none; }
    .peta-no:hover { filter: brightness(.96); }
    .peta-no.benar { background: #dcfce7; border-color: #86efac; color: #15803d; }
    .peta-no.salah { background: #fee2e2; border-color: #fca5a5; color: #b91c1c; }
    .peta-no.lewat { background: #f1f5f9; color: var(--gk-muted); }
    .peta-no:focus-visible { outline: 3px solid rgba(26,115,232,.35); outline-offset: 2px; }
    .peta-legend { display: flex; gap: 14px; margin: 10px 0 18px; font-size: .72rem; color: var(--gk-muted); }
    .peta-legend span::before { content: ''; display: inline-block; width: 10px; height: 10px; border-radius: 3px; margin-right: 5px; vertical-align: -1px; border: 1.5px solid var(--gk-line); background: #f1f5f9; }
    .peta-legend .l-benar::before { background: #dcfce7; border-color: #86efac; }
    .peta-legend .l-salah::before { background: #fee2e2; border-color: #fca5a5; }

    .review-item { border-left: 5px solid var(--gk-line); }
    .review-item.r-benar { border-left-color: #22c55e; }
    .review-item.r-salah { border-left-color: #ef4444; }
    .review-item.r-lewat { border-left-color: #cbd5e1; }
    .review-item .soal-tanya { font-size: 1.5rem !important; }
    .review-item .soal-paragraf { font-size: 1.2rem !important; }
    .review-item .opsi-pilih { font-size: 1.3rem !important; padding: 9px 14px; align-items: center; }
    .review-item .opsi-pilih .tanda { margin-left: 0; margin-top: 0; }
    .tag-grup { margin-left: auto; display: inline-flex; gap: 6px; align-items: center; flex-shrink: 0; }
    .tag-grup .kamu-tag { margin-left: 0; }
    .kunci-tag { font-family: 'Plus Jakarta Sans', sans-serif; font-size: .68rem; font-weight: 700; padding: 2px 7px; border-radius: 6px; background: #16a34a; color: #fff; }
    .catatan-lewat { font-size: .78rem; color: var(--gk-muted); background: #f1f5f9; border-radius: 10px; padding: 8px 12px; margin-bottom: 10px; }
    .jelas-box { border-left: 3px solid #fbbf24; background: #fffbeb; color: #78350f; }

/* ===== RAGU-RAGU (kuning) ===== */
    .badge-ragu { background: #fef3c7; color: #92400e; }
    .stat-row .stat-box:nth-child(4) .stat-num { color: #b45309; }
    .peta-no { position: relative; }
    .peta-no.ragu { box-shadow: 0 0 0 2px #fbbf24; }
    .peta-no.ragu::after { content: ''; position: absolute; top: 3px; right: 3px; width: 7px; height: 7px; border-radius: 50%; background: #f59e0b; }
    .peta-legend .l-ragu::before { background: #fef3c7; border-color: #fbbf24; }

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
        $ujian = $grup->jenis === 'ujian';
        $skor  = (float) $hasil->skor;

        $kelasRing = $hasil->lulus === true ? 'lulus' : ($hasil->lulus === false ? 'gagal' : '');

        if ($hasil->lulus === true) {
            $judulSkor = 'Lulus';
        } elseif ($hasil->lulus === false) {
            $judulSkor = 'Belum lulus';
        } else {
            $judulSkor = $skor >= 80 ? 'Hasil yang bagus' : ($skor >= 50 ? 'Terus berlatih' : 'Perlu diulang');
        }
    @endphp

    <div class="container">

        <div class="ph-card hasil-page">
            <div class="ph-left">
                <div class="ph-icon hasil"><i class="fas fa-flag-checkered"></i></div>
                <div style="min-width:0;">
                    <h5 class="ph-title">Hasil {{ $ujian ? 'ujian' : 'latihan' }}</h5>
                    <ol class="ph-breadcrumb" aria-label="breadcrumb">
                        <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li><a href="{{ route('admin.kerjakan.index') }}">Kerjakan Soal</a></li>
                        <li><a href="{{ route('admin.kerjakan.detail', $grup) }}">{{ \Illuminate\Support\Str::limit($grup->judul, 24) }}</a></li>
                        <li><span class="bc-active">Hasil</span></li>
                    </ol>
                </div>
            </div>
            <div class="ph-actions">
                @if ($boleh)
                    <form method="POST" action="{{ route('admin.kerjakan.mulai', $grup) }}">
                        @csrf
                        <button class="btn btn-primary btn-sm"><i class="fas fa-redo me-1"></i> Coba lagi</button>
                    </form>
                @endif
                <a href="{{ route('admin.kerjakan.detail', $grup) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Ke paket
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

            {{-- ===== Skor ===== --}}
            <div class="card form-card mb-4">
                <div class="card-body">
                    <div class="skor-card">
                        <div class="skor-ring {{ $kelasRing }}" style="--p: {{ min(100, $skor) }}" role="img" aria-label="Skor {{ $skor }} dari 100">
                            <div>
                                <span class="angka">{{ $skor }}</span>
                                <span class="cap">dari 100</span>
                            </div>
                        </div>

                        <div class="skor-info">
                            <h6 class="skor-judul">{{ $judulSkor }}</h6>
                            <div class="skor-sub">
                                {{ $grup->judul }}, percobaan ke-{{ $percobaanKe }}
                                @if ($grup->nilai_lulus !== null)
                                    <br>Nilai lulus {{ $grup->nilai_lulus }}
                                @endif
                                @if ($skorTerbaik !== null)
                                    <br>Skor terbaik {{ (float) $skorTerbaik }}
                                @endif
                            </div>

                            @if ($perBagian->count() > 1)
                                <div class="bagian-bar">
                                    @foreach ($perBagian as $bagian => $p)
                                        <div class="baris">
                                            <span class="nama">@php $ib = $infoBagian($bagian); @endphp
                                                <span class="badge {{ $ib['kelas'] }}"><i class="fas {{ $ib['ikon'] }} me-1"></i>{{ $ib['nama'] }}</span></span>
                                            <span class="trek" aria-hidden="true"><span class="isi d-block" style="width: {{ $p['total'] ? round($p['benar'] / $p['total'] * 100) : 0 }}%"></span></span>
                                            <span class="nilai">{{ $p['benar'] }}/{{ $p['total'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="stat-row">
                <div class="stat-box">
                    <div class="stat-num">{{ $ringkas['benar'] }}</div>
                    <div class="stat-cap">Benar</div>
                </div>
                <div class="stat-box">
                    <div class="stat-num">{{ $ringkas['salah'] }}</div>
                    <div class="stat-cap">Salah</div>
                </div>
                <div class="stat-box">
                    <div class="stat-num">{{ $ringkas['dilewati'] }}</div>
                    <div class="stat-cap">Dilewati</div>
                </div>
                <div class="stat-box">
                    <div class="stat-num">{{ $ringkas['ragu'] }}</div>
                    <div class="stat-cap">Ragu-ragu</div>
                </div>
                <div class="stat-box">
                    <div class="stat-num" style="font-size:1.15rem; padding-top:6px;">{{ $durasi ?? '-' }}</div>
                    <div class="stat-cap">Lama mengerjakan</div>
                </div>
            </div>

            {{-- ===== Pembahasan ===== --}}
            <div class="card form-card">
                <div class="card-body">
                    <div class="section-divider">
                        <span><i class="fas fa-list-ol me-2"></i>Pembahasan</span>
                        <span class="badge badge-pilih">{{ $daftar->count() }} soal</span>
                    </div>

                    <div class="peta-jawab" aria-label="Ringkasan tiap nomor soal">
                        @foreach ($nomor as $soalId => $noSoal)
                            @php
                                $jj = $jawaban[$soalId];
                                $st = $jj->jawaban_dipilih === null ? 'lewat' : ($jj->benar ? 'benar' : 'salah');
                            @endphp
                            <a href="{{ route('admin.kerjakan.hasil', $hasil) }}#soal-{{ $soalId }}" class="peta-no {{ $st }} {{ $jj->ragu ? 'ragu' : '' }}"
                               title="Soal {{ $noSoal }}: {{ $st === 'lewat' ? 'dilewati' : $st }}{{ $jj->ragu ? ', ragu-ragu' : '' }}">{{ $noSoal }}</a>
                        @endforeach
                    </div>
                    <div class="peta-legend">
                        <span class="l-benar">Benar</span><span class="l-salah">Salah</span><span>Dilewati</span><span class="l-ragu">Ditandai ragu-ragu</span>
                    </div>

                    <div class="tab-filter" role="navigation" aria-label="Filter pembahasan">
                        <a href="{{ route('admin.kerjakan.hasil', $hasil) }}" class="{{ $tampil === 'semua' ? 'aktif' : '' }}">Semua ({{ $ringkas['benar'] + $ringkas['salah'] + $ringkas['dilewati'] }})</a>
                        <a href="{{ route('admin.kerjakan.hasil', [$hasil, 'tampil' => 'salah']) }}" class="{{ $tampil === 'salah' ? 'aktif' : '' }}">Salah ({{ $ringkas['salah'] }})</a>
                        <a href="{{ route('admin.kerjakan.hasil', [$hasil, 'tampil' => 'dilewati']) }}" class="{{ $tampil === 'dilewati' ? 'aktif' : '' }}">Dilewati ({{ $ringkas['dilewati'] }})</a>
                        <a href="{{ route('admin.kerjakan.hasil', [$hasil, 'tampil' => 'ragu']) }}" class="{{ $tampil === 'ragu' ? 'aktif' : '' }}">Ragu-ragu ({{ $ringkas['ragu'] }})</a>
                    </div>

                    @forelse ($daftar as $soal)
                        @php
                            $j        = $jawaban[$soal->id];
                            $dilewati = $j->jawaban_dipilih === null;
                            $dilapor  = in_array($soal->id, $sudahDilaporkan, true);
                        @endphp

                        <div class="review-item {{ $dilewati ? 'r-lewat' : ($j->benar ? 'r-benar' : 'r-salah') }}" id="soal-{{ $soal->id }}">
                            <div class="soal-head">
                                <span class="soal-no">{{ $nomor[$soal->id] }}</span>
                                @php $ib = $infoBagian($soal->bagian); @endphp
                                <span class="badge {{ $ib['kelas'] }}"><i class="fas {{ $ib['ikon'] }} me-1"></i>{{ $ib['nama'] }}</span>
                                @if ($soal->kosakata)
                                    <span class="hanzi-chip">{{ $soal->kosakata->hanzi }}</span>
                                    <span class="text-muted" style="font-size:.76rem;">{{ $soal->kosakata->pinyin }}</span>
                                @endif
                                <span class="ms-auto">
                                    @if ($j->ragu)
                                        <span class="badge badge-ragu me-1"><i class="fas fa-flag me-1"></i>Ragu-ragu</span>
                                    @endif
                                    @if ($dilewati)
                                        <span class="badge badge-lewat"><i class="fas fa-forward me-1"></i>Dilewati</span>
                                    @elseif ($j->benar)
                                        <span class="badge badge-benar"><i class="fas fa-check me-1"></i>Benar</span>
                                    @else
                                        <span class="badge badge-salah"><i class="fas fa-times me-1"></i>Salah</span>
                                    @endif
                                </span>
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

                            @if ($dilewati)
                                <div class="catatan-lewat"><i class="fas fa-forward me-1"></i>Kamu tidak menjawab soal ini, jadi dihitung salah.</div>
                            @endif

                            <div class="opsi-list">
                                @foreach ($soal->pilihan as $huruf => $teks)
                                    @php
                                        $kelas = '';
                                        if ($huruf === $soal->jawaban_benar) {
                                            $kelas = 'benar';
                                        } elseif ($huruf === $j->jawaban_dipilih) {
                                            $kelas = 'salah';
                                        }
                                    @endphp
                                    <div class="opsi-pilih terkunci {{ $kelas }}">
                                        <span class="huruf">{{ $huruf }}</span>
                                        <span>{{ $teks }}</span>
                                        @if ($huruf === $j->jawaban_dipilih || $kelas === 'benar')
                                            <span class="tag-grup">
                                                @if ($huruf === $j->jawaban_dipilih)
                                                    <span class="kamu-tag">Jawabanmu</span>
                                                @endif
                                                @if ($kelas === 'benar')
                                                    <span class="kunci-tag">Kunci jawaban</span>
                                                @endif
                                            </span>
                                        @endif
                                        @if ($kelas === 'benar')
                                            <i class="fas fa-check-circle tanda" title="Jawaban benar"></i>
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                            @if ($soal->penjelasan)
                                <div class="jelas-box"><i class="fas fa-lightbulb me-1"></i>{!! nl2br(e(strip_tags($soal->penjelasan))) !!}</div>
                            @endif

                            {{-- ===== Lapor soal ===== --}}
                            <details class="lapor" @if ($errors->any() && old('_soal') == $soal->id) open @endif>
                                <summary>
                                    <i class="fas fa-flag"></i>
                                    {{ $dilapor ? 'Sudah dilaporkan, kirim ulang' : 'Laporkan soal ini' }}
                                </summary>

                                <form method="POST" action="{{ route('admin.kerjakan.lapor', [$hasil, $soal]) }}" class="lapor-form">
                                    @csrf
                                    <input type="hidden" name="_soal" value="{{ $soal->id }}">

                                    <div>
                                        <label class="lbl" for="jenis-{{ $soal->id }}">Masalahnya apa?</label>
                                        <select name="jenis" id="jenis-{{ $soal->id }}" class="form-select" required data-jenis-lapor>
                                            @foreach ($labelLaporan as $nilai => $label)
                                                <option value="{{ $nilai }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div data-pilihan-lapor>
                                        <label class="lbl" for="pilihan-{{ $soal->id }}">Pilihan mana yang ikut benar?</label>
                                        <select name="pilihan_bermasalah" id="pilihan-{{ $soal->id }}" class="form-select">
                                            <option value="">Pilih jawaban</option>
                                            @foreach ($soal->pilihan as $huruf => $teks)
                                                @if ($huruf !== $soal->jawaban_benar)
                                                    <option value="{{ $huruf }}">{{ $huruf }}. {{ \Illuminate\Support\Str::limit($teks, 50) }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <div class="help-text mt-1">Pilihan ini nantinya tidak dipakai lagi sebagai pengecoh.</div>
                                    </div>

                                    <div>
                                        <label class="lbl" for="catatan-{{ $soal->id }}">Catatan (opsional)</label>
                                        <textarea name="catatan" id="catatan-{{ $soal->id }}" rows="2" maxlength="1000" class="form-control" placeholder="Jelaskan singkat masalahnya"></textarea>
                                    </div>

                                    <div>
                                        <button class="btn btn-outline-secondary btn-sm"><i class="fas fa-paper-plane me-1"></i> Kirim laporan</button>
                                    </div>
                                </form>
                            </details>
                        </div>
                    @empty
                        <div class="empty-state">
                            <div class="empty-state-icon"><i class="fas fa-check"></i></div>
                            <div class="empty-state-title">Tidak ada soal di kategori ini</div>
                            <div class="empty-state-text"><a href="{{ route('admin.kerjakan.hasil', $hasil) }}">Lihat semua soal</a></div>
                        </div>
                    @endforelse
                </div>
            </div>

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
        // Pilihan "ikut benar" hanya relevan untuk jenis laporan "lebih dari satu jawaban benar".
        document.querySelectorAll('.lapor-form').forEach(function (form) {
            var jenis = form.querySelector('[data-jenis-lapor]');
            var kotak = form.querySelector('[data-pilihan-lapor]');
            var pilih = kotak.querySelector('select');

            function sinkron() {
                var perlu = jenis.value === 'ambigu_banyak_benar';
                kotak.style.display = perlu ? '' : 'none';
                pilih.required = perlu;
                if (!perlu) pilih.value = '';
            }

            jenis.addEventListener('change', sinkron);
            sinkron();
        });
    </script>
@endsection
