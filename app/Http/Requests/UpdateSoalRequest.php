<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSoalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['guru', 'superadmin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ujian_id' => 'sometimes|exists:ujians,id',
            'teks_soal' => 'sometimes|string',
            'tipe_soal' => 'sometimes|in:objektif,ganda_kompleks,menjodohkan,isian,essay',
            'path_gambar' => 'sometimes|nullable|file|mimes:png,jpg,jpeg|max:2048',

            'pilihan_jawaban' => 'sometimes|nullable|array',
            'pilihan_jawaban.*.teks_pilihan' => 'sometimes|nullable|string',
            'pilihan_jawaban.*.teks_pasangan' => 'sometimes|nullable|string',
            'pilihan_jawaban.*.persentase_nilai' => 'sometimes|nullable|integer|min:0|max:100|exclude_if:tipe_soal,isian|exclude_if:tipe_soal,essay',
            'pilihan_jawaban.*.is_true' => 'sometimes|nullable|boolean',

            'hapus_gambar' => 'nullable|boolean'
        ];
    }
}
