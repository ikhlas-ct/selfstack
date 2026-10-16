<?php

namespace App\Http\Requests;

use App\Models\Kosakata;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KosakataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Baris contoh kalimat yang kosong semua (hanzi, pinyin, arti, catatan)
     * dibuang dulu supaya tidak kena validasi "wajib diisi".
     * Saat edit, baris lama yang dikosongkan otomatis ikut terhapus.
     */
    protected function prepareForValidation(): void
    {
        $contoh = collect($this->input('contoh_kalimat', []))
            ->filter(function ($baris) {
                if (! is_array($baris)) {
                    return false;
                }

                return collect($baris)
                    ->only(['hanzi', 'pinyin', 'arti_indonesia', 'catatan_tata_bahasa'])
                    ->filter(fn ($nilai) => filled($nilai))
                    ->isNotEmpty();
            })
            ->values()
            ->all();

        $this->merge(['contoh_kalimat' => $contoh]);
    }

    public function rules(): array
    {
        // Saat edit, route punya parameter {kosakata} (model hasil route-model binding).
        $id = $this->route('kosakata')?->id;

        return [
            'kategori_id'    => ['nullable', 'integer', 'exists:kategoris,id'],
            'level_tocfl_id' => ['nullable', 'integer', 'exists:level_tocfls,id'],

            'hanzi'          => [
                'required', 'string', 'max:20',
                // Di database yang unik adalah gabungan hanzi + pinyin (kosakatas_hanzi_pinyin_unique).
                Rule::unique('kosakatas', 'hanzi')
                    ->where(fn ($q) => $q->where('pinyin', $this->input('pinyin')))
                    ->ignore($id),
            ],
            'pinyin'         => ['required', 'string', 'max:60'],
            'zhuyin'         => ['nullable', 'string', 'max:100'],
            'baca_indonesia' => ['nullable', 'string', 'max:60'],

            // arti_indonesia di database boleh NULL (mis. data awal dari daftar TOCFL yang baru berbahasa Inggris).
            'arti_indonesia' => ['nullable', 'string', 'max:150'],
            'arti_singkat'   => ['nullable', 'string', 'max:100'],
            'english'        => ['nullable', 'string', 'max:150'],

            'jenis_kata'     => ['nullable', 'string', Rule::in(Kosakata::JENIS_KATA)],
            'sub_jenis'      => ['nullable', 'string', Rule::in(Kosakata::SUB_JENIS)],

            'kegunaan'       => ['nullable', 'string', 'max:2000'],
            'kata_pengait'   => ['nullable', 'string', 'max:1000'],
            'urutan'         => ['nullable', 'integer', 'min:0'],

            'contoh_kalimat'                       => ['nullable', 'array', 'max:20'],
            'contoh_kalimat.*.id'                  => ['nullable', 'integer'],
            'contoh_kalimat.*.hanzi'               => ['required', 'string', 'max:255'],
            'contoh_kalimat.*.pinyin'              => ['required', 'string', 'max:255'],
            'contoh_kalimat.*.arti_indonesia'      => ['required', 'string', 'max:255'],
            'contoh_kalimat.*.catatan_tata_bahasa' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'required'     => ':attribute wajib diisi.',
            'max.string'   => ':attribute maksimal :max karakter.',
            'max.array'    => ':attribute maksimal :max baris.',
            'integer'      => ':attribute harus berupa angka bulat.',
            'min.numeric'  => ':attribute tidak boleh negatif.',
            'exists'       => ':attribute yang dipilih tidak valid.',
            'in'           => ':attribute yang dipilih tidak valid.',
            'hanzi.unique' => 'Kombinasi hanzi dan pinyin ini sudah ada di daftar kosakata.',
        ];
    }

    public function attributes(): array
    {
        return [
            'kategori_id'    => 'Kategori',
            'level_tocfl_id' => 'Level TOCFL',
            'hanzi'          => 'Hanzi',
            'pinyin'         => 'Pinyin',
            'zhuyin'         => 'Zhuyin',
            'baca_indonesia' => 'Cara baca Indonesia',
            'arti_indonesia' => 'Arti Indonesia',
            'arti_singkat'   => 'Arti singkat',
            'english'        => 'Arti Inggris',
            'jenis_kata'     => 'Jenis kata',
            'sub_jenis'      => 'Sub jenis',
            'kegunaan'       => 'Kegunaan kata',
            'kata_pengait'   => 'Kata pengait',
            'urutan'         => 'Urutan',

            'contoh_kalimat'                       => 'Contoh kalimat',
            'contoh_kalimat.*.hanzi'               => 'Hanzi contoh kalimat',
            'contoh_kalimat.*.pinyin'              => 'Pinyin contoh kalimat',
            'contoh_kalimat.*.arti_indonesia'      => 'Arti contoh kalimat',
            'contoh_kalimat.*.catatan_tata_bahasa' => 'Catatan tata bahasa',
        ];
    }
}
