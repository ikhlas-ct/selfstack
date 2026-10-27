@extends('layouts.user.user')

@section('title', $mode === 'review' ? 'Review Harian' : 'Latihan Soal')

@section('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    body, .card, .btn, h4, h5 { font-family: 'Plus Jakarta Sans', sans-serif; }

    .ph-card { background: #fff; border: 1px solid #e9ecef; border-radius: 14px; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; gap: 16px; flex-wrap: wrap; margin-bottom: 1.25rem; position: relative; overflow: hidden; box-shadow: 0 1px 6px rgba(0,0,0,.05); }
    .ph-card::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; border-radius: 14px 0 0 14px; background: #0369a1; }
    .ph-left { display: flex; align-items: center; gap: 12px; }
    .ph-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1rem; flex-shrink: 0; background: #e0f2fe; color: #0369a1; }
    .ph-title { font-size: 1.05rem; font-weight: 700; color: #1e293b; letter-spacing: -.2px; line-height: 1.2; margin: 0; }
    .btn-outline-secondary { border-radius: 10px; font-size: .83rem; border-color: #e2e8f0; color: #64748b; padding: 7px 12px; }

    .fc-wrap { max-width: 640px; margin: 0 auto; }

    /* progres */
    .fc-progres-teks { display: flex; justify-content: space-between; font-size: .78rem; font-weight: 600; color: #64748b; margin-bottom: 6px; }
    .fc-bar { height: 8px; background: #e2e8f0; border-radius: 99px; overflow: hidden; margin-bottom: 18px; }
    .fc-bar > span { display: block; height: 100%; width: 0; background: linear-gradient(90deg, #1a73e8, #16a34a); border-radius: 99px; transition: width .25s ease; }

    /* kartu */
    .fc-scene { perspective: 1400px; }
    .fc-kartu { position: relative; height: 400px; cursor: pointer; transform-style: preserve-3d; transition: transform .45s ease; }
    .fc-kartu.balik { transform: rotateY(180deg); }
    .fc-sisi { position: absolute; inset: 0; backface-visibility: hidden; -webkit-backface-visibility: hidden; background: #fff; border-radius: 20px; box-shadow: 0 4px 24px rgba(0,0,0,.09); border: 1px solid #e9ecef; padding: 24px; display: flex; flex-direction: column; }
    .fc-depan { align-items: center; justify-content: center; text-align: center; }
    .fc-belakang { transform: rotateY(180deg); overflow-y: auto; }

    /* sisi depan = soal */
    .fc-tanya { font-size: .95rem; font-weight: 700; color: #0369a1; margin-bottom: 14px; }
    .fc-clue { color: #1e293b; line-height: 1.2; word-break: break-all; }
    .fc-clue-hanzi  { font-size: 5.2rem; font-weight: 600; font-family: 'Noto Sans TC', 'PingFang TC', 'Microsoft JhengHei', 'Plus Jakarta Sans', sans-serif; }
    .fc-clue-pinyin { font-size: 2.6rem; font-weight: 700; color: #1a73e8; word-break: normal; }
    .fc-clue-arti   { font-size: 1.9rem; font-weight: 700; line-height: 1.3; word-break: normal; }
    .fc-bantu { font-size: .85rem; color: #64748b; margin-top: 12px; min-height: 1.2em; }
    .fc-hint { font-size: .78rem; color: #94a3b8; margin-top: 18px; }
    .fc-streak { position: absolute; bottom: 14px; left: 0; right: 0; text-align: center; font-size: .75rem; font-weight: 600; color: #94a3b8; }
    .fc-streak b { color: #16a34a; }
    .fc-tag { position: absolute; top: 14px; left: 18px; font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #0369a1; background: #e0f2fe; padding: 3px 9px; border-radius: 6px; }
    .fc-tag:empty { display: none; }

    .btn-speak { border: none; background: #e8f0fe; color: #1a73e8; width: 34px; height: 34px; border-radius: 50%; font-size: .8rem; transition: all .15s; flex-shrink: 0; }
    .btn-speak:hover { background: #1a73e8; color: #fff; }
    .fc-speak-depan { position: absolute; top: 12px; right: 14px; }

    /* sisi belakang = kunci jawaban */
    .fc-b-tanya { font-size: .72rem; font-weight: 600; color: #94a3b8; margin-bottom: 10px; }
    .fc-b-hanzi { font-size: 2rem; font-weight: 600; color: #1e293b; font-family: 'Noto Sans TC', 'PingFang TC', 'Microsoft JhengHei', sans-serif; line-height: 1.2; }
    .fc-b-pinyin { font-size: 1.15rem; font-weight: 600; color: #1a73e8; margin-top: 2px; }
    .fc-b-baca { font-size: .85rem; color: #64748b; font-style: italic; }
    .fc-b-arti { font-size: 1.25rem; font-weight: 700; color: #1e293b; margin-top: 12px; }
    .fc-b-english { font-size: .85rem; color: #64748b; }
    .fc-target { display: inline-block; background: #fef9c3; border-radius: 8px; padding: 0 8px; box-shadow: 0 0 0 2px #fde047; }
    .fc-contoh-judul { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #94a3b8; margin: 16px 0 6px; }
    .fc-contoh { background: #f8fafc; border-radius: 10px; padding: 10px 12px; margin-bottom: 8px; }
    .fc-contoh .h { font-size: 1rem; font-weight: 600; color: #1e293b; font-family: 'Noto Sans TC', 'PingFang TC', 'Microsoft JhengHei', sans-serif; }
    .fc-contoh .p { font-size: .78rem; color: #1a73e8; }
    .fc-contoh .a { font-size: .78rem; color: #64748b; }

    /* tombol nilai */
    .fc-aksi { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 18px; }
    .fc-btn { border: 2px solid transparent; border-radius: 14px; padding: 14px 10px; font-weight: 700; font-size: .95rem; transition: transform .1s, opacity .2s; }
    .fc-btn:active:not(:disabled) { transform: scale(.97); }
    .fc-btn:disabled { opacity: .35; cursor: not-allowed; }
    .fc-btn small { display: block; font-weight: 500; font-size: .7rem; opacity: .8; margin-top: 2px; }
    .fc-salah { background: #fff7ed; color: #c2410c; border-color: #fdba74; }
    .fc-benar { background: #f0fdf4; color: #15803d; border-color: #86efac; }
    .fc-pintasan { text-align: center; font-size: .72rem; color: #94a3b8; margin-top: 12px; }
    .fc-peringatan { font-size: .78rem; color: #b45309; text-align: center; margin-top: 10px; }

    /* ringkasan */
    .fc-selesai { background: #fff; border-radius: 20px; box-shadow: 0 4px 24px rgba(0,0,0,.09); padding: 32px 24px; text-align: center; }
    .fc-selesai .ikon { width: 72px; height: 72px; border-radius: 50%; background: #dcfce7; color: #16a34a; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; font-size: 1.8rem; }
    .fc-skor { display: flex; justify-content: center; gap: 32px; margin: 18px 0; }
    .fc-skor .n { font-size: 2rem; font-weight: 800; line-height: 1; }
    .fc-skor .l { font-size: .72rem; font-weight: 600; text-transform: uppercase; color: #64748b; margin-top: 4px; }
    .fc-perubahan { font-size: .82rem; font-weight: 600; color: #0369a1; background: #e0f2fe; border-radius: 10px; padding: 8px 12px; margin-bottom: 16px; }
    .fc-daftar-salah { text-align: left; border-top: 1px solid #f1f5f9; margin-top: 8px; padding-top: 14px; }
    .fc-daftar-salah .baris { display: flex; gap: 10px; align-items: baseline; padding: 6px 0; border-bottom: 1px solid #f8fafc; font-size: .85rem; }
    .fc-daftar-salah .baris .h { font-size: 1.1rem; font-weight: 600; min-width: 64px; font-family: 'Noto Sans TC', 'PingFang TC', 'Microsoft JhengHei', sans-serif; }
    .fc-daftar-salah .baris .p { color: #1a73e8; min-width: 80px; }
    .btn-primary { background: linear-gradient(135deg, #1a73e8, #1558b0); border: none; border-radius: 10px; font-weight: 600; font-size: .85rem; padding: 9px 20px; }

    @media (max-width: 575px) {
        .fc-kartu { height: 440px; }
        .fc-clue-hanzi { font-size: 4rem; }
        .fc-clue-pinyin { font-size: 2.1rem; }
        .fc-clue-arti { font-size: 1.6rem; }
    }
</style>
@endsection

@section('content')
    <div class="container">

        <div class="ph-card">
            <div class="ph-left">
                <div class="ph-icon"><i class="fas {{ $mode === 'review' ? 'fa-redo' : 'fa-layer-group' }}"></i></div>
                <h5 class="ph-title">{{ $mode === 'review' ? 'Review Harian' : 'Latihan Soal' }}</h5>
            </div>
            <a href="{{ route('admin.flashcard.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-times me-1"></i> Keluar
            </a>
        </div>

        <div class="page-inner">
            <div class="fc-wrap">

                {{-- Sesi --}}
                <div id="area-kartu">
                    <div class="fc-progres-teks">
                        <span id="teks-posisi">1 / 1</span>
                        <span id="teks-skor"></span>
                    </div>
                    <div class="fc-bar"><span id="bar"></span></div>

                    <div class="fc-scene">
                        <div class="fc-kartu" id="kartu" tabindex="0" role="button" aria-label="Lihat jawaban">
                            {{-- Depan: soal --}}
                            <div class="fc-sisi fc-depan">
                                <span class="fc-tag" id="d-tag"></span>
                                <button type="button" class="btn-speak fc-speak-depan" id="d-speak" title="Dengar"><i class="fas fa-volume-up"></i></button>
                                <div class="fc-tanya" id="d-tanya"></div>
                                <div class="fc-clue" id="d-clue"></div>
                                <div class="fc-bantu" id="d-bantu"></div>
                                <div class="fc-hint"><i class="fas fa-hand-pointer me-1"></i>Jawab dulu di kepala atau di kertas, lalu ketuk kartu</div>
                                <div class="fc-streak" id="d-streak"></div>
                            </div>
                            {{-- Belakang: kunci jawaban --}}
                            <div class="fc-sisi fc-belakang">
                                <div class="fc-b-tanya" id="b-tanya"></div>
                                <div class="d-flex align-items-start justify-content-between gap-2">
                                    <div>
                                        <div class="fc-b-hanzi" id="b-hanzi"></div>
                                        <div class="fc-b-pinyin" id="b-pinyin"></div>
                                        <div class="fc-b-baca" id="b-baca"></div>
                                    </div>
                                    <button type="button" class="btn-speak" id="b-speak" title="Dengar"><i class="fas fa-volume-up"></i></button>
                                </div>
                                <div class="fc-b-arti" id="b-arti"></div>
                                <div class="fc-b-english" id="b-english"></div>
                                <div id="b-contoh"></div>
                            </div>
                        </div>
                    </div>

                    <div class="fc-aksi">
                        <button type="button" class="fc-btn fc-salah" id="btn-salah" disabled>
                            <i class="fas fa-times me-1"></i> Salah<small>hitungan mulai dari 0</small>
                        </button>
                        <button type="button" class="fc-btn fc-benar" id="btn-benar" disabled>
                            <i class="fas fa-check me-1"></i> Benar<small>beruntun +1</small>
                        </button>
                    </div>
                    <div class="fc-pintasan d-none d-md-block">
                        Spasi: lihat jawaban &nbsp;·&nbsp; ← atau 1: Salah &nbsp;·&nbsp; → atau 2: Benar
                    </div>
                    <div class="fc-peringatan d-none" id="peringatan"></div>
                </div>

                {{-- Ringkasan --}}
                <div id="area-selesai" class="d-none">
                    <div class="fc-selesai">
                        <div class="ikon"><i class="fas fa-flag-checkered"></i></div>
                        <h4 class="fw-bold mb-1" style="color:#1e293b;" id="s-judul">Sesi selesai</h4>
                        <div class="text-muted" style="font-size:.85rem;" id="s-ket"></div>

                        <div class="fc-skor">
                            <div><div class="n" style="color:#16a34a;" id="s-benar">0</div><div class="l">Benar</div></div>
                            <div><div class="n" style="color:#ea580c;" id="s-salah">0</div><div class="l">Salah</div></div>
                        </div>

                        <div class="fc-perubahan d-none" id="s-perubahan"></div>

                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                            <button type="button" class="btn btn-primary d-none" id="btn-ulang">
                                <i class="fas fa-redo me-1"></i> Ulangi yang salah
                            </button>
                            <a href="{{ route('admin.flashcard.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-layer-group me-1"></i> Latihan
                            </a>
                            <a href="{{ route('admin.hafalan.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-chart-pie me-1"></i> Progres Hafalan
                            </a>
                        </div>

                        <div class="fc-daftar-salah d-none" id="s-daftar">
                            <div class="fw-bold mb-2" style="font-size:.8rem; color:#64748b;">KATA YANG SALAH</div>
                            <div id="s-daftar-isi"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
(function () {
    const KARTU       = @json($kartu);
    const MODE        = @json($mode);
    const JENIS_AKTIF = @json($jenis);
    const TARGET      = @json($targetBeruntun);
    const URL_JAWAB   = @json(route('admin.flashcard.jawab'));
    const TOKEN       = @json(csrf_token());

    /*
     * Jenis soal. clue = yang ditampilkan di depan, target = yang harus dijawab
     * (disorot di belakang), perlu = kolom yang harus terisi agar soal bisa dibuat.
     * bantu = petunjuk kecil di bawah soal (opsional).
     */
    const JENIS = {
        hanzi_pinyin: { tanya: 'Pinyin-nya apa?',      clue: 'hanzi',  target: 'pinyin', perlu: ['pinyin'] },
        pinyin_hanzi: { tanya: 'Hanzi-nya bagaimana?', clue: 'pinyin', target: 'hanzi',  perlu: ['pinyin'],
                        bantu: k => k.arti ? 'Arti: ' + k.arti : '' },
        arti_hanzi:   { tanya: 'Hanzi-nya bagaimana?', clue: 'arti',   target: 'hanzi',  perlu: ['arti'],
                        bantu: k => Array.from(k.hanzi).length + ' karakter' },
        arti_pinyin:  { tanya: 'Pinyin-nya apa?',      clue: 'arti',   target: 'pinyin', perlu: ['pinyin', 'arti'] },
        hanzi_arti:   { tanya: 'Apa artinya?',         clue: 'hanzi',  target: 'arti',   perlu: ['arti'] },
    };

    const $ = id => document.getElementById(id);
    const el = {
        kartu: $('kartu'), posisi: $('teks-posisi'), skor: $('teks-skor'), bar: $('bar'),
        dTag: $('d-tag'), dTanya: $('d-tanya'), dClue: $('d-clue'), dBantu: $('d-bantu'),
        dStreak: $('d-streak'), dSpeak: $('d-speak'),
        bTanya: $('b-tanya'), bHanzi: $('b-hanzi'), bPinyin: $('b-pinyin'), bBaca: $('b-baca'),
        bArti: $('b-arti'), bEnglish: $('b-english'), bContoh: $('b-contoh'), bSpeak: $('b-speak'),
        btnSalah: $('btn-salah'), btnBenar: $('btn-benar'), peringatan: $('peringatan'),
        areaKartu: $('area-kartu'), areaSelesai: $('area-selesai'),
    };
    const sisiJawaban = { hanzi: el.bHanzi, pinyin: el.bPinyin, arti: el.bArti };

    let antrian = KARTU.slice();
    let idx = 0;
    let terbalik = false;
    let catat = true;          // false saat "Ulangi yang salah" (tidak dicatat dua kali)
    let jumlahBenar = 0;
    let daftarSalah = [];
    let gagalSimpan = 0;
    let jumlahNaik = 0;        // kata yang naik ke Ingat Sepenuhnya di sesi ini
    let jumlahTurun = 0;       // kata yang turun dari Ingat Sepenuhnya di sesi ini
    let jenisKini = null;
    let jenisTerakhir = null;
    let mulaiTampil = 0;

    /* ---------------- pilih jenis soal ---------------- */
    function pilihJenis(k) {
        let pool = JENIS_AKTIF.filter(j => JENIS[j] && JENIS[j].perlu.every(f => k[f]));
        if (!pool.length) pool = ['hanzi_arti'];            // cadangan kalau data kata kosong
        if (pool.length > 1) pool = pool.filter(j => j !== jenisTerakhir); // jangan jenis sama berturut-turut
        return pool[Math.floor(Math.random() * pool.length)];
    }

    /* ---------------- tampilan kartu ---------------- */
    function tampil() {
        const k = antrian[idx];
        terbalik = false;
        el.kartu.classList.remove('balik');
        el.btnSalah.disabled = true;
        el.btnBenar.disabled = true;

        jenisKini = pilihJenis(k);
        jenisTerakhir = jenisKini;
        const j = JENIS[jenisKini];

        // depan: soal
        el.dTag.textContent = [k.level, k.kategori].filter(Boolean).join(' · ');
        el.dTanya.textContent = j.tanya;
        el.dClue.className = 'fc-clue fc-clue-' + j.clue;
        el.dClue.textContent = k[j.clue] || '';
        el.dBantu.textContent = j.bantu ? j.bantu(k) : '';
        el.dStreak.innerHTML = '';
        const b = document.createElement('b');
        b.textContent = Math.min(k.beruntun || 0, TARGET) + ' / ' + TARGET;
        el.dStreak.append('Benar beruntun: ', b);
        // suara hanya untuk soal yang tidak membocorkan jawaban
        el.dSpeak.classList.toggle('d-none', jenisKini !== 'hanzi_arti');

        // belakang: kunci jawaban, bagian yang ditanyakan disorot
        el.bTanya.textContent = 'Soal: ' + j.tanya;
        el.bHanzi.textContent = k.hanzi;
        el.bPinyin.textContent = k.pinyin || '';
        el.bBaca.textContent = k.baca ? '(' + k.baca + ')' : '';
        el.bArti.textContent = k.arti || '';
        el.bEnglish.textContent = k.english || '';
        Object.entries(sisiJawaban).forEach(([nama, node]) => {
            node.classList.toggle('fc-target', nama === j.target);
        });

        el.bContoh.innerHTML = '';
        if (k.contoh && k.contoh.length) {
            const judul = document.createElement('div');
            judul.className = 'fc-contoh-judul';
            judul.textContent = 'Contoh kalimat';
            el.bContoh.appendChild(judul);
            k.contoh.forEach(c => {
                const kotak = document.createElement('div');
                kotak.className = 'fc-contoh';
                [['h', c.hanzi], ['p', c.pinyin], ['a', c.arti]].forEach(([kelas, teks]) => {
                    if (!teks) return;
                    const d = document.createElement('div');
                    d.className = kelas;
                    d.textContent = teks;
                    kotak.appendChild(d);
                });
                el.bContoh.appendChild(kotak);
            });
        }

        el.posisi.textContent = (idx + 1) + ' / ' + antrian.length;
        el.skor.textContent = 'Benar ' + jumlahBenar + ' · Salah ' + daftarSalah.length;
        el.bar.style.width = (idx / antrian.length * 100) + '%';
        mulaiTampil = Date.now();
    }

    function balik() {
        if (idx >= antrian.length) return;
        terbalik = !terbalik;
        el.kartu.classList.toggle('balik', terbalik);
        // Benar/Salah hanya aktif saat jawaban terlihat; dibalik lagi = nonaktif
        el.btnSalah.disabled = !terbalik;
        el.btnBenar.disabled = !terbalik;
    }

    /* ---------------- penilaian ---------------- */
    function jawab(benar) {
        if (!terbalik) return;
        const k = antrian[idx];
        const durasi = Math.round(Date.now() - mulaiTampil);

        if (benar) jumlahBenar++;
        else if (!daftarSalah.some(x => x.id === k.id)) daftarSalah.push(k);

        if (catat) {
            k.beruntun = benar ? (k.beruntun || 0) + 1 : 0;
            simpan(k.id, benar, durasi);
        }

        idx++;
        if (idx >= antrian.length) selesai();
        else tampil();
    }

    // Dikirim di latar belakang supaya soal berikutnya langsung muncul.
    function simpan(kosakataId, benar, durasi) {
        fetch(URL_JAWAB, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': TOKEN,
            },
            body: JSON.stringify({ kosakata_id: kosakataId, benar: benar, durasi_ms: durasi }),
            keepalive: true,
        }).then(r => {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        }).then(d => {
            if (d.naik) jumlahNaik++;
            if (d.turun) jumlahTurun++;
            perbaruiPerubahan();
        }).catch(() => {
            gagalSimpan++;
            el.peringatan.textContent = gagalSimpan + ' jawaban belum tersimpan. Cek koneksi, lalu coba lagi nanti.';
            el.peringatan.classList.remove('d-none');
        });
    }

    // Respons server bisa tiba setelah ringkasan tampil, jadi diperbarui langsung.
    function perbaruiPerubahan() {
        const bagian = [];
        if (jumlahNaik)  bagian.push(jumlahNaik + ' kata naik ke Ingat Sepenuhnya');
        if (jumlahTurun) bagian.push(jumlahTurun + ' kata kembali ke Lupa & Ingat');
        const kotak = $('s-perubahan');
        kotak.textContent = bagian.join(', ');
        kotak.classList.toggle('d-none', bagian.length === 0);
    }

    function selesai() {
        el.areaKartu.classList.add('d-none');
        el.areaSelesai.classList.remove('d-none');

        $('s-benar').textContent = jumlahBenar;
        $('s-salah').textContent = daftarSalah.length;
        $('s-judul').textContent = daftarSalah.length === 0 ? 'Sempurna!' : 'Sesi selesai';
        $('s-ket').textContent = !catat
            ? 'Pengulangan ini tidak mengubah progres.'
            : (MODE === 'review'
                ? 'Progres dan jadwal review sudah diperbarui.'
                : 'Progres kata sudah diperbarui sesuai penilaianmu.');
        perbaruiPerubahan();

        const adaSalah = daftarSalah.length > 0;
        $('btn-ulang').classList.toggle('d-none', !adaSalah);
        $('s-daftar').classList.toggle('d-none', !adaSalah);

        const isi = $('s-daftar-isi');
        isi.innerHTML = '';
        daftarSalah.forEach(k => {
            const baris = document.createElement('div');
            baris.className = 'baris';
            [['h', k.hanzi], ['p', k.pinyin || ''], ['a', k.arti || '']].forEach(([kelas, teks]) => {
                const s = document.createElement('span');
                s.className = kelas;
                s.textContent = teks;
                baris.appendChild(s);
            });
            isi.appendChild(baris);
        });

        if (gagalSimpan > 0) {
            $('s-ket').textContent += ' (' + gagalSimpan + ' jawaban gagal tersimpan.)';
        }
    }

    $('btn-ulang').addEventListener('click', () => {
        antrian = daftarSalah.slice();
        daftarSalah = [];
        jumlahBenar = 0;
        idx = 0;
        catat = false;
        el.areaSelesai.classList.add('d-none');
        el.areaKartu.classList.remove('d-none');
        tampil();
    });

    /* ---------------- interaksi ---------------- */
    el.kartu.addEventListener('click', balik);
    el.btnSalah.addEventListener('click', () => jawab(false));
    el.btnBenar.addEventListener('click', () => jawab(true));

    document.addEventListener('keydown', e => {
        if (el.areaKartu.classList.contains('d-none')) return;
        if (e.target.matches('input, select, textarea')) return;

        if (e.code === 'Space' || e.code === 'Enter') { e.preventDefault(); balik(); }
        else if (e.code === 'ArrowLeft'  || e.key === '1') jawab(false);
        else if (e.code === 'ArrowRight' || e.key === '2') jawab(true);
    });

    /* ---------------- suara (sama dengan halaman detail) ---------------- */
    const KUNCI_SUARA = 'suara_mandarin';
    const bisaSuara = 'speechSynthesis' in window;
    let suara = null;

    function pilihSuara() {
        const daftar = window.speechSynthesis.getVoices().filter(v => /^zh/i.test(v.lang));
        let tersimpan = null;
        try { tersimpan = localStorage.getItem(KUNCI_SUARA); } catch (e) {}
        suara = daftar.find(v => v.name === tersimpan)
            || daftar.find(v => /^zh[-_]TW$/i.test(v.lang) && /google/i.test(v.name))
            || daftar.find(v => /^zh[-_](TW|HK)/i.test(v.lang))
            || daftar[0]
            || null;
    }
    if (bisaSuara) {
        pilihSuara();
        window.speechSynthesis.onvoiceschanged = pilihSuara;
    }

    function ucapkan() {
        const k = antrian[idx];
        if (!k || !bisaSuara || !suara) return;
        window.speechSynthesis.cancel();
        const u = new SpeechSynthesisUtterance(k.hanzi);
        u.voice = suara;
        u.lang = suara.lang;
        u.rate = 0.9;
        window.speechSynthesis.speak(u);
    }
    [el.dSpeak, el.bSpeak].forEach(btn => btn.addEventListener('click', e => {
        e.stopPropagation(); // jangan ikut membalik kartu
        ucapkan();
    }));

    tampil();
})();
</script>
@endsection
