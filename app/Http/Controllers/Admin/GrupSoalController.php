<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GrupKosakata;
use App\Models\GrupSoal;
use App\Models\LevelTocfl;
use App\Models\Soal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * CRUD paket soal (latihan / ujian). Soal di dalam paket diurus SoalController.
 *
 * Catatan: GrupSoal::soals() sudah punya orderBy(urutan, id). Query agregat dengan
 * GROUP BY di sini memakai reorder() supaya tidak kena ONLY_FULL_GROUP_BY di MySQL.
 */
class GrupSoalController extends Controller
{
    private const PAKET_PER_HALAMAN = 12;
    private const SOAL_PER_HALAMAN  = 25;

    // ------------------------------------------------------------ daftar

    public function index(Request $request)
    {
        $cari  = trim((string) $request->query('search', ''));
        $level = $request->filled('level') ? (int) $request->query('level') : null;
        $jenis = $request->query('jenis');
        $aktif = $request->query('aktif');

        if (! in_array($jenis, [GrupSoal::JENIS_LATIHAN, GrupSoal::JENIS_UJIAN], true)) {
            $jenis = null;
        }
        if (! in_array($aktif, ['1', '0'], true)) {
            $aktif = null;
        }

        // Angka di pil jenis: total keseluruhan, tidak ikut filter.
        $ringkas = [
            'semua'   => GrupSoal::count(),
            'latihan' => GrupSoal::latihan()->count(),
            'ujian'   => GrupSoal::ujian()->count(),
        ];

        $paket = GrupSoal::query()
            ->with(['levelTocfl', 'grupKosakata'])
            ->withCount([
                'soals',
                'soals as perlu_dicek_count' => fn ($q) => $q->where('status_validasi', Soal::STATUS_PERLU_DICEK),
                'hasilUjians as percobaan_count' => fn ($q) => $q->whereNotNull('selesai_pada'),
            ])
            ->when($cari !== '', fn ($q) => $q->where('judul', 'like', "%{$cari}%"))
            ->when($jenis, fn ($q) => $q->where('jenis', $jenis))
            ->when($level, fn ($q) => $q->where('level_tocfl_id', $level))
            ->when($aktif !== null, fn ($q) => $q->where('aktif', $aktif === '1'))
            ->orderByDesc('id')
            ->paginate(self::PAKET_PER_HALAMAN)
            ->withQueryString();

        $levels = LevelTocfl::orderBy('tingkat')->get();

        return view('pages.grup-soal.index', compact('paket', 'levels', 'ringkas', 'cari', 'level', 'jenis', 'aktif'));
    }

    // ------------------------------------------------------------ detail

    public function show(Request $request, GrupSoal $grupSoal)
    {
        $grupSoal->load(['levelTocfl', 'grupKosakata']);

        $cari   = trim((string) $request->query('search', ''));
        $bagian = $request->query('bagian');
        $status = $request->query('status');

        if (! in_array($bagian, Soal::BAGIAN, true)) {
            $bagian = null;
        }
        if (! in_array($status, array_keys(\App\Support\LabelSoal::STATUS), true)) {
            $status = null;
        }

        $soals = $grupSoal->soals()
            ->when($cari !== '', fn ($q) => $q->where('pertanyaan', 'like', "%{$cari}%"))
            ->when($bagian, fn ($q) => $q->where('bagian', $bagian))
            ->when($status, fn ($q) => $q->where('status_validasi', $status))
            ->paginate(self::SOAL_PER_HALAMAN)
            ->withQueryString();

        $perStatus = $grupSoal->soals()->reorder()
            ->selectRaw('status_validasi, COUNT(*) as jml')
            ->groupBy('status_validasi')
            ->pluck('jml', 'status_validasi');

        $perBagian = $grupSoal->soals()->reorder()
            ->selectRaw('bagian, COUNT(*) as jml')
            ->groupBy('bagian')
            ->pluck('jml', 'bagian');

        $totalSoal = (int) $perStatus->sum();
        $percobaan = $grupSoal->hasilUjians()->selesai()->count();
        $rataSkor  = $percobaan > 0 ? round((float) $grupSoal->hasilUjians()->selesai()->avg('skor'), 1) : null;

        return view('pages.grup-soal.show', compact(
            'grupSoal', 'soals', 'perStatus', 'perBagian', 'totalSoal', 'percobaan', 'rataSkor',
            'cari', 'bagian', 'status'
        ));
    }

    // ------------------------------------------------------------ tambah

    public function create()
    {
        $grupSoal = new GrupSoal([
            'jenis' => GrupSoal::JENIS_LATIHAN,
            'aktif' => true,
        ]);

        return view('pages.grup-soal.create', array_merge(['grupSoal' => $grupSoal], $this->dataForm()));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validasi($request);
        $data['sumber'] = GrupSoal::SUMBER_MANUAL;

        $paket = GrupSoal::create($data);

        return redirect()
            ->route('admin.grup-soal.show', $paket)
            ->with('sukses', 'Paket soal dibuat. Sekarang tambahkan soalnya.');
    }

    // ------------------------------------------------------------ ubah

    public function edit(GrupSoal $grupSoal)
    {
        return view('pages.grup-soal.edit', array_merge(['grupSoal' => $grupSoal], $this->dataForm()));
    }

    public function update(Request $request, GrupSoal $grupSoal): RedirectResponse
    {
        $grupSoal->update($this->validasi($request));

        return redirect()
            ->route('admin.grup-soal.show', $grupSoal)
            ->with('sukses', 'Paket soal diperbarui.');
    }

    /** Tombol aktif / nonaktif di daftar (menyembunyikan tanpa menghapus). */
    public function toggleAktif(GrupSoal $grupSoal): RedirectResponse
    {
        $grupSoal->update(['aktif' => ! $grupSoal->aktif]);

        return back()->with('sukses', $grupSoal->aktif ? 'Paket diaktifkan.' : 'Paket dinonaktifkan.');
    }

    // ------------------------------------------------------------ hapus

    /** Menghapus paket ikut menghapus soal, riwayat percobaan, dan jawabannya (cascade di database). */
    public function destroy(GrupSoal $grupSoal): RedirectResponse
    {
        $judul = $grupSoal->judul;
        $grupSoal->delete();

        return redirect()
            ->route('admin.grup-soal.index')
            ->with('sukses', 'Paket "' . $judul . '" dihapus.');
    }

    // ------------------------------------------------------------ helper

    private function dataForm(): array
    {
        return [
            'levels'        => LevelTocfl::orderBy('tingkat')->get(),
            'grupKosakatas' => GrupKosakata::orderBy('nama')->get(['id', 'nama']),
        ];
    }

    private function validasi(Request $request): array
    {
        $data = $request->validate([
            'judul'            => ['required', 'string', 'max:500'],
            'deskripsi'        => ['nullable', 'string', 'max:5000'],
            'jenis'            => ['required', Rule::in([GrupSoal::JENIS_LATIHAN, GrupSoal::JENIS_UJIAN])],
            'level_tocfl_id'   => ['nullable', 'integer', 'exists:level_tocfls,id'],
            'grup_kosakata_id' => ['nullable', 'integer', 'exists:grup_kosakatas,id'],
            'durasi_menit'     => ['nullable', 'integer', 'min:1', 'max:1440'],
            'nilai_lulus'      => ['nullable', 'integer', 'min:0', 'max:100'],
            'maks_percobaan'   => ['nullable', 'integer', 'min:1', 'max:255'],
        ], [], [
            'judul'            => 'judul',
            'level_tocfl_id'   => 'level TOCFL',
            'grup_kosakata_id' => 'grup kosakata',
            'durasi_menit'     => 'durasi',
            'nilai_lulus'      => 'nilai lulus',
            'maks_percobaan'   => 'maksimal percobaan',
        ]);

        $data['aktif'] = $request->boolean('aktif');

        // Nilai lulus hanya dipakai untuk ujian (lihat HasilUjian::selesaikan()).
        if ($data['jenis'] !== GrupSoal::JENIS_UJIAN) {
            $data['nilai_lulus'] = null;
        }

        return $data;
    }
}
