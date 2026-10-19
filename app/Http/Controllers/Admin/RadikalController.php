<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\RadikalExport;
use App\Exports\RadikalTemplateExport;
use App\Http\Requests\RadikalRequest;
use App\Imports\RadikalImport;
use App\Models\Radikal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RadikalController extends Controller
{
    private const PER_HALAMAN = 25;

    // ------------------------------------------------------------------ INDEX

    public function index(Request $request)
    {
        $radikals = $this->query($request)->paginate(self::PER_HALAMAN)->withQueryString();

        $stats = [
            'total'          => Radikal::count(),
            'dengan_karakter' => Radikal::has('karakters')->count(),
            'tanpa_karakter' => Radikal::doesntHave('karakters')->count(),
            'tanpa_keterangan' => Radikal::where(fn ($q) => $q->whereNull('keterangan')->orWhere('keterangan', ''))->count(),
        ];

        $goresans = Radikal::whereNotNull('jumlah_goresan')
            ->distinct()->orderBy('jumlah_goresan')->pluck('jumlah_goresan');

        $view = view('pages.radikal.index', compact('radikals', 'stats', 'goresans'));

        // Filter/pagination memuat ulang potongan tabel saja lewat AJAX.
        return $request->ajax() ? $view->fragment('tabel') : $view;
    }

    // ------------------------------------------------------------------ CREATE / STORE

    public function create()
    {
        return view('pages.radikal.create', ['radikal' => new Radikal()]);
    }

    public function store(RadikalRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['varian'] = ! empty($data['varian']) ? $data['varian'] : null;
        $data['urutan'] ??= ((int) Radikal::max('urutan')) + 1;

        $radikal = Radikal::create($data);

        return redirect()->route('admin.radikal.index')
            ->with('success', "Radikal {$radikal->hanzi} berhasil ditambahkan.");
    }

    // ------------------------------------------------------------------ SHOW

    public function show(Radikal $radikal)
    {
        $radikal->loadCount('soals')->load([
            'progres',
            'karakters' => fn ($q) => $q
                ->select(['id', 'radikal_id', 'hanzi', 'pinyin', 'arti_indonesia', 'bentuk_radikal', 'jumlah_goresan', 'urutan'])
                ->orderBy('urutan'),
        ]);

        return view('pages.radikal.show', compact('radikal'));
    }

    // ------------------------------------------------------------------ EDIT / UPDATE

    public function edit(Radikal $radikal)
    {
        $radikal->loadCount(['karakters', 'soals']);

        return view('pages.radikal.edit', compact('radikal'));
    }

    public function update(RadikalRequest $request, Radikal $radikal): RedirectResponse
    {
        $data = $request->validated();

        $data['varian'] = ! empty($data['varian']) ? $data['varian'] : null;
        // Urutan dikosongkan = tidak berubah (kolom di database wajib terisi).
        $data['urutan'] ??= $radikal->urutan;

        $radikal->update($data);

        return redirect()->route('admin.radikal.index')
            ->with('success', "Radikal {$radikal->hanzi} berhasil diperbarui.");
    }

    // ------------------------------------------------------------------ DESTROY

    public function destroy(Radikal $radikal): RedirectResponse
    {
        $hanzi = $radikal->hanzi;

        DB::transaction(function () use ($radikal) {
            // Soal tidak ikut terhapus (FK-nya jadi NULL), jadi sembunyikan dari latihan & ujian.
            $radikal->soals()->update([
                'status_validasi'  => 'ditolak',
                'catatan_validasi' => "Radikal {$radikal->hanzi} sudah dihapus.",
                'dicek_pada'       => now(),
            ]);

            // Progres ikut terhapus (cascade); karakter, soal, dan riwayat review hanya kehilangan tautannya (SET NULL).
            $radikal->delete();
        });

        return redirect()->route('admin.radikal.index')
            ->with('success', "Radikal {$hanzi} berhasil dihapus.");
    }

    // ------------------------------------------------------------------ EXCEL

    public function import()
    {
        return view('pages.radikal.import');
    }

    public function importStore(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:5120'],
            'mode' => ['required', 'in:perbarui,lewati'],
        ], [
            'file.required' => 'Pilih file Excel dulu.',
            'file.mimes'    => 'File harus berformat .xlsx atau .xls.',
            'file.max'      => 'Ukuran file maksimal 5 MB.',
            'mode.required' => 'Pilih cara menangani data yang sudah ada.',
        ]);

        try {
            $hasil = (new RadikalImport($request->input('mode')))->import($request->file('file')->getRealPath());
        } catch (\RuntimeException $e) {
            return redirect()->route('admin.radikal.import')->with('error', $e->getMessage());
        } catch (\Throwable $e) {
            report($e);

            return redirect()->route('admin.radikal.import')
                ->with('error', 'File Excel tidak bisa dibaca. Pastikan file .xlsx/.xls valid dan tidak diproteksi password.');
        }

        return redirect()->route('admin.radikal.import')->with('hasil', $hasil);
    }

    public function template()
    {
        return (new RadikalTemplateExport())->download('template-import-radikal.xlsx');
    }

    /** Export mengikuti filter yang sedang aktif di halaman index. */
    public function export(Request $request)
    {
        $data = $this->query($request)->get();

        return (new RadikalExport($data))->download('radikal-' . now()->format('Ymd-His') . '.xlsx');
    }

    // ------------------------------------------------------------------ FILTER (dipakai index & export)

    private function query(Request $request)
    {
        $q = Radikal::query()->withCount(['karakters', 'soals']);

        if ($cari = trim((string) $request->input('search'))) {
            $like = '%' . addcslashes($cari, '%_\\') . '%';

            $q->where(function ($w) use ($like, $cari) {
                $w->where('hanzi', 'like', $like)
                    ->orWhere('pinyin', 'like', $like)
                    ->orWhere('arti_indonesia', 'like', $like)
                    ->orWhere('keterangan', 'like', $like)
                    ->orWhere('varian', 'like', $like);

                if (ctype_digit($cari)) {
                    $w->orWhere('nomor_kangxi', (int) $cari);
                }
            });
        }

        if ($request->filled('jumlah_goresan') && ctype_digit((string) $request->input('jumlah_goresan'))) {
            $q->where('jumlah_goresan', (int) $request->input('jumlah_goresan'));
        }

        match ($request->input('lengkap')) {
            'ada_karakter'     => $q->has('karakters'),
            'tanpa_karakter'   => $q->doesntHave('karakters'),
            'tanpa_keterangan' => $q->where(fn ($w) => $w->whereNull('keterangan')->orWhere('keterangan', '')),
            'ada_varian'       => $q->whereRaw('JSON_LENGTH(varian) > 0'),
            'tanpa_varian'     => $q->where(fn ($w) => $w->whereNull('varian')->orWhereRaw('JSON_LENGTH(varian) = 0')),
            'tanpa_nomor'      => $q->whereNull('nomor_kangxi'),
            default            => null,
        };

        if ($request->input('urut') === 'kangxi') {
            $q->orderByRaw('nomor_kangxi IS NULL')->orderBy('nomor_kangxi')->orderBy('id');
        } else {
            $q->orderBy('urutan')->orderBy('nomor_kangxi')->orderBy('id');
        }

        return $q;
    }
}
