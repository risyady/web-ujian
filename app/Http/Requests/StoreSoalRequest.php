<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSoalRequest extends FormRequest
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
            'teks_soal' => 'required|string',
            'tipe_soal' => 'required|in:objektif,ganda_kompleks,menjodohkan,isian,essay',
            'path_gambar' => 'nullable|string',

            'pilihan_jawaban' => 'nullable|array',
            'pilihan_jawaban.*.teks_pilihan' => 'nullable|string',
            'pilihan_jawaban.*.teks_pasangan' => 'nullable|string',
            'pilihan_jawaban.*.persentase_nilai' => 'nullable|integer|min:0|max:100|exclude_if:tipe_soal,isian|exclude_if:tipe_soal,essay',
            'pilihan_jawaban.*.is_true' => 'nullable|boolean',
        ];
    }
}
