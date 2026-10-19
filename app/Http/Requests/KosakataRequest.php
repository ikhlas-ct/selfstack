<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validasi tambah/ubah radikal. Aturannya juga dipakai oleh import Excel
 * (lihat App\Imports\RadikalImport) supaya form dan Excel selalu sama.
 */
class RadikalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * "氵, 氺" / "氵氺" / "氵;氺" -> ["氵", "氺"]
     * Varian radikal selalu satu karakter, jadi teks yang disambung ikut dipecah per karakter.
     */
    public static function pecahVarian(?string $teks): array
    {
        if ($teks === null || trim($teks) === '') {
            return [];
        }

        $bagian = preg_split('/[\s,;，、；]+/u', $teks, -1, PREG_SPLIT_NO_EMPTY);
        $huruf  = [];
        foreach ($bagian as $b) {
            array_push($huruf, ...mb_str_split($b));
        }

        return array_values(array_unique($huruf));
    }

    protected function prepareForValidation(): void
    {
        $bersih = [];

        foreach (['hanzi', 'pinyin', 'arti_indonesia', 'keterangan', 'nomor_kangxi', 'jumlah_goresan', 'urutan'] as $kolom) {
            $nilai = $this->input($kolom);
            $nilai = is_string($nilai) ? trim($nilai) : $nilai;
            $bersih[$kolom] = $nilai === '' ? null : $nilai;
        }

        $varian = $this->input('varian');
        $bersih['varian'] = is_array($varian) ? $varian : self::pecahVarian($varian);

        $this->merge($bersih);
    }

    public function rules(): array
    {
        return self::aturan($this->route('radikal')?->id);
    }

    public function messages(): array
    {
        return self::pesan();
    }

    public function attributes(): array
    {
        return self::atribut();
    }

    public static function aturan(?int $abaikanId = null): array
    {
        return [
            'nomor_kangxi'   => ['nullable', 'integer', 'between:1,214', Rule::unique('radikals', 'nomor_kangxi')->ignore($abaikanId)],
            'hanzi'          => ['required', 'string', 'max:8', Rule::unique('radikals', 'hanzi')->ignore($abaikanId)],
            'varian'         => ['nullable', 'array', 'max:10'],
            'varian.*'       => ['string', 'max:8'],
            'pinyin'         => ['required', 'string', 'max:100'],
            'arti_indonesia' => ['required', 'string', 'max:500'],
            'keterangan'     => ['nullable', 'string', 'max:2000'],
            'jumlah_goresan' => ['nullable', 'integer', 'between:1,30'],
            'urutan'         => ['nullable', 'integer', 'between:0,65535'],
        ];
    }

    public static function pesan(): array
    {
        return [
            'required'     => ':attribute wajib diisi.',
            'unique'       => ':attribute ini sudah dipakai radikal lain.',
            'integer'      => ':attribute harus berupa angka bulat.',
            'between'      => ':attribute harus di antara :min dan :max.',
            'max'          => ':attribute maksimal :max karakter.',
            'string'       => ':attribute tidak valid.',
            'array'        => ':attribute tidak valid.',
            'varian.max'   => 'Varian maksimal :max buah.',
            'varian.*.max' => 'Setiap varian maksimal :max karakter.',
        ];
    }

    public static function atribut(): array
    {
        return [
            'nomor_kangxi'   => 'Nomor Kangxi',
            'hanzi'          => 'Hanzi',
            'varian'         => 'Varian',
            'varian.*'       => 'Varian',
            'pinyin'         => 'Pinyin',
            'arti_indonesia' => 'Arti Indonesia',
            'keterangan'     => 'Keterangan',
            'jumlah_goresan' => 'Jumlah goresan',
            'urutan'         => 'Urutan',
        ];
    }
}
