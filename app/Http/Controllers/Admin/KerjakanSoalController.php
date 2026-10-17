<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GrupSoal;
use App\Models\HasilUjian;
use App\Models\JawabanUjian;
use App\Models\LaporanSoal;
use App\Models\LevelTocfl;
use App\Models\Soal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Mengerjakan soal dari paket (GrupSoal).
 *
 * Alur: index (daftar paket) -> detail -> mulai (buat HasilUjian) -> kerjakan (satu soal per halaman)
 *       -> jawab (simpan + pindah soal) -> selesai (hitung skor) -> hasil (pembahasan + lapor soal).
 *
 * Latihan maupun ujian dikerjakan dengan cara yang sama: tidak ada umpan balik selama mengerjakan,
 * jawaban boleh diubah, navigasi bebas. Benar/salah dan pembahasan baru muncul di halaman hasil.
 * Jenis paket hanya memengaruhi label (dan batas waktu, kalau paketnya punya durasi).
 */
class KerjakanSoalController extends Controller
{
    /** Toleransi (detik) setelah waktu habis, untuk jawaban terakhir yang masih dalam perjalanan dari browser. */
    private const TOLERANSI_DETIK = 10;

    /** Label jenis laporan soal untuk form lapor di halaman hasil. */
    private const LABEL_LAPORAN = [
        LaporanSoal::JENIS_AMBIGU         => 'Lebih dari satu jawaban benar',
        LaporanSoal::JENIS_KUNCI_SALAH    => 'Kunci jawaban salah',
        LaporanSoal::JENIS_TIDAK_ADA_BENAR => 'Tidak ada jawaban yang benar',
        LaporanSoal::JENIS_TYPO           => 'Salah ketik',
        LaporanSoal::JENIS_AUDIO          => 'Audio bermasalah',
        LaporanSoal::JENIS_LAINNYA        => 'Lainnya',
    ];

    // ================================================================ daftar & detail paket

    public function index(Request $request): View
    {
        $this->tutupYangKedaluwarsa();

        $cari  = trim((string) $request->query('search', ''));
        $jenis = $request->query('jenis');
        $level = (int) $request->query('level', 0);

        $grups = GrupSoal::aktif()
            ->with('levelTocfl')
            ->withCount(['soals as soals_count' => fn ($q) => $q->dapatDikerjakan()])
            ->whereHas('soals', fn ($q) => $q->dapatDikerjakan())
            ->when(
                in_array($jenis, [GrupSoal::JENIS_LATIHAN, GrupSoal::JENIS_UJIAN], true),
                fn ($q) => $q->where('jenis', $jenis)
            )
            ->when($level > 0, fn ($q) => $q->where('level_tocfl_id', $level))
            ->when($cari !== '', fn ($q) => $q->where(
                fn ($w) => $w->where('judul', 'like', "%{$cari}%")->orWhere('deskripsi', 'like', "%{$cari}%")
            ))
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $ids = $grups->pluck('id');

        // Statistik percobaan selesai per paket + percobaan yang sedang berjalan.
        $statistik = HasilUjian::selesai()
            ->whereIn('grup_soal_id', $ids)
            ->selectRaw('grup_soal_id, COUNT(*) AS percobaan, MAX(skor) AS skor_terbaik')
            ->groupBy('grup_soal_id')
            ->get()
            ->keyBy('grup_soal_id');

        $berjalan = HasilUjian::berjalan()->whereIn('grup_soal_id', $ids)->get()->keyBy('grup_soal_id');

        // Jumlah soal yang sudah dijawab pada percobaan berjalan, untuk bar progres di kartu paket.
        $jumlahDijawab = JawabanUjian::whereIn('hasil_ujian_id', $berjalan->pluck('id'))
            ->whereNotNull('jawaban_dipilih')
            ->selectRaw('hasil_ujian_id, COUNT(*) AS jumlah')
            ->groupBy('hasil_ujian_id')
            ->pluck('jumlah', 'hasil_ujian_id');

        $dijawab = $berjalan->map(fn (HasilUjian $h) => (int) ($jumlahDijawab[$h->id] ?? 0));

        $ringkasan = [
            'paket'    => $grups->total(),
            'selesai'  => HasilUjian::selesai()->count(),
            'berjalan' => HasilUjian::berjalan()->count(),
        ];

        return view('pages.kerjakan.index', [
            'grups'     => $grups,
            'statistik' => $statistik,
            'berjalan'  => $berjalan,
            'dijawab'   => $dijawab,
            'ringkasan' => $ringkasan,
            'levels'    => LevelTocfl::orderBy('tingkat')->get(['id', 'nama']),
            'cari'      => $cari,
            'jenis'     => $jenis,
            'level'     => $level,
        ]);
    }

    public function detail(GrupSoal $grupSoal): View
    {
        abort_unless($grupSoal->aktif, 404);

        $this->tutupYangKedaluwarsa();

        $grupSoal->load('levelTocfl');

        $jumlahSoal = $grupSoal->soals()->dapatDikerjakan()->count();

        // reorder(): relasi soals() sudah punya orderBy, yang bentrok dengan GROUP BY.
        $perBagian = $grupSoal->soals()->dapatDikerjakan()->reorder()
            ->selectRaw('bagian, COUNT(*) AS jumlah')
            ->groupBy('bagian')
            ->pluck('jumlah', 'bagian');

        $berjalan = $grupSoal->hasilUjians()->berjalan()->latest('id')->first();

        $dijawabBerjalan = $berjalan
            ? $berjalan->jawabanUjians()->whereNotNull('jawaban_dipilih')->count()
            : 0;

        $riwayat = $grupSoal->hasilUjians()->selesai()->latest('selesai_pada')->latest('id')->take(10)->get();

        $selesaiCount = $grupSoal->hasilUjians()->selesai()->count();

        return view('pages.kerjakan.detail', [
            'grup'            => $grupSoal,
            'jumlahSoal'      => $jumlahSoal,
            'perBagian'       => $perBagian,
            'berjalan'        => $berjalan,
            'dijawabBerjalan' => $dijawabBerjalan,
            'nomorLanjut'     => $berjalan ? $this->nomorLanjut($berjalan, $grupSoal) : 1,
            'riwayat'         => $riwayat,
            'selesaiCount'    => $selesaiCount,
            'sisaPercobaan'   => $grupSoal->maks_percobaan === null
                ? null
                : max(0, $grupSoal->maks_percobaan - $selesaiCount),
            'boleh'           => $grupSoal->masihBolehMencoba(),
        ]);
    }

    // ================================================================ mulai / kerjakan

    public function mulai(GrupSoal $grupSoal): RedirectResponse
    {
        abort_unless($grupSoal->aktif, 404);

        $this->tutupYangKedaluwarsa();

        // Selalu lanjutkan percobaan yang masih berjalan, jangan buat yang paralel.
        $berjalan = $grupSoal->hasilUjians()->berjalan()->latest('id')->first();

        if ($berjalan) {
            return redirect()->route('admin.kerjakan.soal', [$berjalan, 'no' => $this->nomorLanjut($berjalan, $grupSoal)]);
        }

        if (! $grupSoal->masihBolehMencoba()) {
            return back()->with('error', 'Batas percobaan paket ini sudah habis.');
        }

        $jumlah = $grupSoal->soals()->dapatDikerjakan()->count();

        if ($jumlah === 0) {
            return back()->with('error', 'Paket ini belum punya soal yang bisa dikerjakan.');
        }

        $hasil = $grupSoal->hasilUjians()->create([
            'jumlah_soal' => $jumlah,
            'mulai_pada'  => now(),
        ]);

        return redirect()->route('admin.kerjakan.soal', [$hasil, 'no' => 1]);
    }

    public function kerjakan(Request $request, HasilUjian $hasilUjian): View|RedirectResponse
    {
        if ($alih = $this->periksaStatus($hasilUjian)) {
            return $alih;
        }

        $hasilUjian->load('grupSoal.levelTocfl');
        $grup    = $hasilUjian->grupSoal;
        $latihan = $grup->jenis === GrupSoal::JENIS_LATIHAN;

        $ids = $grup->soals()->dapatDikerjakan()->pluck('id')->values();

        if ($ids->isEmpty()) {
            $this->tutup($hasilUjian);

            return redirect()->route('admin.kerjakan.hasil', $hasilUjian)
                ->with('error', 'Paket ini sudah tidak punya soal yang bisa dikerjakan.');
        }

        $no   = max(1, min($ids->count(), (int) $request->query('no', 1)));
        $soal = Soal::with('kosakata')->findOrFail($ids[$no - 1]);

        $jawaban = $hasilUjian->jawabanUjians()->get()->keyBy('soal_id');

        // Status tiap nomor untuk kotak navigasi.
        // Selama mengerjakan: sudah dijawab atau belum (tanpa benar/salah), plus tanda ragu-ragu.
        $nav = $ids->map(function ($id, $i) use ($jawaban) {
            $j = $jawaban[$id] ?? null;

            return [
                'no'     => $i + 1,
                'status' => $j && $j->jawaban_dipilih !== null ? 'terjawab' : 'kosong',
                'ragu'   => (bool) ($j?->ragu),
            ];
        });

        $jawabanSaatIni = $jawaban[$soal->id] ?? null;

        return view('pages.kerjakan.soal', [
            'hasil'          => $hasilUjian,
            'grup'           => $grup,
            'soal'           => $soal,
            'no'             => $no,
            'total'          => $ids->count(),
            'nav'            => $nav,
            'belumDijawab'   => $nav->where('status', 'kosong')->count(),
            'ragu'           => (bool) ($jawabanSaatIni?->ragu),
            'jumlahRagu'     => $nav->where('ragu', true)->count(),
            'jawabanSaatIni' => $jawabanSaatIni,
            'latihan'        => $latihan, // hanya untuk label
            'sisaDetik'      => $this->sisaDetik($hasilUjian),
        ]);
    }

    /**
     * Simpan jawaban soal yang sedang tampil, lalu pindah.
     * Field "ke": nomor soal tujuan, atau "selesai" untuk mengakhiri percobaan.
     */
    public function jawab(Request $request, HasilUjian $hasilUjian): RedirectResponse|JsonResponse
    {
        if ($alih = $this->periksaStatus($hasilUjian)) {
            // Flash message sudah tersimpan di session oleh periksaStatus().
            return $request->expectsJson()
                ? response()->json(['halaman' => 'hasil', 'redirect' => $alih->getTargetUrl()])
                : $alih;
        }

        $data = $request->validate([
            'soal_id'  => ['required', 'integer'],
            'no'       => ['nullable', 'integer', 'min:1'],
            'jawaban'  => ['nullable', Rule::in(['A', 'B', 'C', 'D'])],
            'ragu'     => ['nullable', 'boolean'],
            'waktu_ms' => ['nullable', 'integer', 'min:0'],
            'ke'       => ['required', 'regex:/^(\d+|selesai)$/'],
        ]);

        $grup = $hasilUjian->grupSoal;

        $soal = Soal::dapatDikerjakan()
            ->where('grup_soal_id', $grup->id)
            ->find($data['soal_id']);

        abort_if($soal === null, 404);

        $huruf = $data['jawaban'] ?? null;
        $ragu  = $request->boolean('ragu');

        $sudah = JawabanUjian::where('hasil_ujian_id', $hasilUjian->id)->where('soal_id', $soal->id)->first();

        // Simpan kalau ada pilihan baru, atau kalau tanda ragu-ragu berubah (soal belum dijawab pun boleh ditandai).
        $perluSimpan = $huruf !== null || ($sudah ? (bool) $sudah->ragu !== $ragu : $ragu);

        if ($perluSimpan) {
            $rekam = $sudah ?? new JawabanUjian(['hasil_ujian_id' => $hasilUjian->id, 'soal_id' => $soal->id]);

            $nilai = ['ragu' => $ragu];

            if ($huruf !== null) {
                // Jawaban boleh diubah kapan saja sebelum percobaan diakhiri.
                $nilai += [
                    'jawaban_dipilih' => $huruf,
                    'benar'           => $soal->periksaJawaban($huruf),
                    'waktu_jawab_ms'  => min((int) ($data['waktu_ms'] ?? 0), 3_600_000),
                    'dijawab_pada'    => now(),
                ];
            } elseif (! $sudah) {
                // Hanya ditandai ragu-ragu, belum ada jawaban.
                $nilai += ['jawaban_dipilih' => null, 'benar' => false];
            }

            // forceFill: kolom "ragu" tidak perlu didaftarkan di $fillable model.
            $rekam->forceFill($nilai)->save();

            // TODO (SM-2): karena jawaban boleh diubah, catat RiwayatReview (sumber = SOAL,
            // jenis_item = $soal->jenisItemReview()) dan perbarui progres_* saat percobaan ditutup
            // (tutup()), bukan di sini, memakai service penjadwalan yang sama dengan flashcard.
        }

        if ($data['ke'] === 'selesai') {
            $this->tutup($hasilUjian);

            if ($request->expectsJson()) {
                session()->flash('success', 'Selesai dikerjakan.');

                return response()->json(['halaman' => 'hasil', 'redirect' => route('admin.kerjakan.hasil', $hasilUjian)]);
            }

            return redirect()->route('admin.kerjakan.hasil', $hasilUjian)->with('success', 'Selesai dikerjakan.');
        }

        $total = $grup->soals()->dapatDikerjakan()->count();
        $ke    = max(1, min($total, (int) $data['ke']));

        $url = route('admin.kerjakan.soal', [$hasilUjian, 'no' => $ke]);

        return $request->expectsJson()
            ? response()->json(['halaman' => 'soal', 'redirect' => $url])
            : redirect()->to($url);
    }

    /** Akhiri percobaan tanpa menyimpan jawaban baru (dipakai halaman detail paket). */
    public function selesai(HasilUjian $hasilUjian): RedirectResponse
    {
        if ($alih = $this->periksaStatus($hasilUjian)) {
            return $alih;
        }

        $this->tutup($hasilUjian);

        return redirect()->route('admin.kerjakan.hasil', $hasilUjian)->with('success', 'Percobaan diakhiri.');
    }

    // ================================================================ hasil & lapor

    public function hasil(Request $request, HasilUjian $hasilUjian): View|RedirectResponse
    {
        if ($hasilUjian->selesai_pada === null) {
            return redirect()->route('admin.kerjakan.soal', $hasilUjian);
        }

        $hasilUjian->load('grupSoal.levelTocfl');
        $grup = $hasilUjian->grupSoal;

        $jawaban = $hasilUjian->jawabanUjians()->get()->keyBy('soal_id');

        $soals = Soal::with('kosakata')
            ->whereIn('id', $jawaban->keys())
            ->orderBy('urutan')->orderBy('id')
            ->get();

        $nomor = $soals->pluck('id')->flip()->map(fn ($i) => $i + 1);

        $benar    = $jawaban->where('benar', true)->count();
        $dilewati = $jawaban->whereNull('jawaban_dipilih')->count();

        $ringkas = [
            'benar'    => $benar,
            'dilewati' => $dilewati,
            'salah'    => max(0, $jawaban->count() - $benar - $dilewati),
            'ragu'     => $jawaban->where('ragu', true)->count(),
        ];

        $perBagian = $soals->groupBy('bagian')->map(fn ($g) => [
            'total' => $g->count(),
            'benar' => $g->filter(fn ($s) => $jawaban[$s->id]->benar)->count(),
        ]);

        $tampil = in_array($request->query('tampil'), ['salah', 'dilewati', 'ragu'], true)
            ? $request->query('tampil')
            : 'semua';

        $daftar = $soals->filter(function ($s) use ($jawaban, $tampil) {
            $j = $jawaban[$s->id];

            return match ($tampil) {
                'salah'    => ! $j->benar && $j->jawaban_dipilih !== null,
                'dilewati' => $j->jawaban_dipilih === null,
                'ragu'     => (bool) $j->ragu,
                default    => true,
            };
        });

        $mulai   = $hasilUjian->mulai_pada ?? $hasilUjian->created_at;
        $detik   = $mulai ? max(0, $hasilUjian->selesai_pada->timestamp - $mulai->timestamp) : null;
        $durasi  = $detik === null ? null : intdiv($detik, 60) . ' mnt ' . ($detik % 60) . ' dtk';

        return view('pages.kerjakan.hasil', [
            'hasil'           => $hasilUjian,
            'grup'            => $grup,
            'jawaban'         => $jawaban,
            'daftar'          => $daftar,
            'nomor'           => $nomor,
            'ringkas'         => $ringkas,
            'perBagian'       => $perBagian,
            'tampil'          => $tampil,
            'durasi'          => $durasi,
            'percobaanKe'     => $grup->hasilUjians()->selesai()->where('id', '<=', $hasilUjian->id)->count(),
            'skorTerbaik'     => $grup->hasilUjians()->selesai()->max('skor'),
            'sudahDilaporkan' => $hasilUjian->laporanSoals()->pluck('soal_id')->all(),
            'labelLaporan'    => self::LABEL_LAPORAN,
            'boleh'           => $grup->aktif && $grup->masihBolehMencoba(),
        ]);
    }

    public function lapor(Request $request, HasilUjian $hasilUjian, Soal $soal): RedirectResponse
    {
        abort_if($hasilUjian->selesai_pada === null, 404);
        abort_unless($soal->grup_soal_id === $hasilUjian->grup_soal_id, 404);

        $data = $request->validate([
            'jenis'              => ['required', Rule::in(LaporanSoal::JENIS)],
            'pilihan_bermasalah' => [
                Rule::requiredIf($request->input('jenis') === LaporanSoal::JENIS_AMBIGU),
                'nullable',
                Rule::in(['A', 'B', 'C', 'D']),
            ],
            'catatan'            => ['nullable', 'string', 'max:1000'],
        ], [
            'pilihan_bermasalah.required' => 'Pilih pilihan jawaban yang ikut benar.',
        ]);

        $pilihan = $data['pilihan_bermasalah'] ?? null;

        // Satu laporan terbuka per soal per percobaan; kirim ulang = memperbarui.
        LaporanSoal::updateOrCreate(
            [
                'soal_id'        => $soal->id,
                'hasil_ujian_id' => $hasilUjian->id,
                'status'         => LaporanSoal::STATUS_BARU,
            ],
            [
                'jenis'                   => $data['jenis'],
                'pilihan_bermasalah'      => $pilihan,
                'teks_pilihan_bermasalah' => $pilihan ? $soal->teksPilihan($pilihan) : null,
                'catatan'                 => $data['catatan'] ?? null,
            ]
        );

        return redirect()
            ->to(url()->previous() . '#soal-' . $soal->id)
            ->with('success', 'Laporan terkirim. Terima kasih, soal akan diperiksa.');
    }

    // ================================================================ helper

    /**
     * Kalau percobaan sudah selesai -> ke halaman hasil. Kalau waktunya habis -> tutup dan ke hasil.
     * Null berarti percobaan masih boleh dikerjakan.
     */
    private function periksaStatus(HasilUjian $hasil): ?RedirectResponse
    {
        if ($hasil->selesai_pada !== null) {
            return redirect()->route('admin.kerjakan.hasil', $hasil);
        }

        $hasil->loadMissing('grupSoal');

        if ($this->kedaluwarsa($hasil)) {
            $this->tutup($hasil, $this->batasWaktu($hasil));

            return redirect()->route('admin.kerjakan.hasil', $hasil)
                ->with('peringatan', 'Waktu habis. Jawaban yang sudah tersimpan dinilai.');
        }

        return null;
    }

    /**
     * Menutup percobaan: soal yang belum dijawab dicatat sebagai dilewati (jawaban null),
     * lalu skor dihitung lewat HasilUjian::selesaikan().
     */
    private function tutup(HasilUjian $hasil, ?Carbon $selesaiPada = null): void
    {
        DB::transaction(function () use ($hasil, $selesaiPada) {
            $hasil->loadMissing('grupSoal');

            $semua = $hasil->grupSoal->soals()->dapatDikerjakan()->pluck('id');
            $sudah = $hasil->jawabanUjians()->pluck('soal_id');

            foreach ($semua->diff($sudah) as $soalId) {
                $hasil->jawabanUjians()->create([
                    'soal_id'         => $soalId,
                    'jawaban_dipilih' => null,
                    'benar'           => false,
                ]);
            }

            $hasil->selesaikan();

            // Percobaan yang kedaluwarsa dicatat selesai tepat di batas waktunya, bukan saat ditutup.
            if ($selesaiPada) {
                $hasil->update(['selesai_pada' => $selesaiPada]);
            }
        });
    }

    /** Tutup semua percobaan berjalan yang waktunya sudah habis (mis. tab ditutup di tengah ujian). */
    private function tutupYangKedaluwarsa(): void
    {
        HasilUjian::berjalan()->with('grupSoal')->get()->each(function (HasilUjian $hasil) {
            if ($hasil->grupSoal && $this->kedaluwarsa($hasil)) {
                $this->tutup($hasil, $this->batasWaktu($hasil));
            }
        });
    }

    private function batasWaktu(HasilUjian $hasil): ?Carbon
    {
        $menit = $hasil->grupSoal?->durasi_menit;

        if (! $menit) {
            return null;
        }

        return ($hasil->mulai_pada ?? $hasil->created_at)?->copy()->addMinutes($menit);
    }

    private function kedaluwarsa(HasilUjian $hasil): bool
    {
        $batas = $this->batasWaktu($hasil);

        return $batas !== null && now()->timestamp > $batas->timestamp + self::TOLERANSI_DETIK;
    }

    private function sisaDetik(HasilUjian $hasil): ?int
    {
        $batas = $this->batasWaktu($hasil);

        return $batas ? max(0, $batas->timestamp - now()->timestamp) : null;
    }

    /** Nomor soal pertama yang belum dijawab (1 kalau semuanya sudah). */
    private function nomorLanjut(HasilUjian $hasil, GrupSoal $grup): int
    {
        $ids     = $grup->soals()->dapatDikerjakan()->pluck('id')->values();
        $dijawab = $hasil->jawabanUjians()->whereNotNull('jawaban_dipilih')->pluck('soal_id');

        $posisi = $ids->search(fn ($id) => ! $dijawab->contains($id));

        return $posisi === false ? 1 : $posisi + 1;
    }
}
