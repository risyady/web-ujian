<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUjianRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['guru']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'judul_ujian' => 'sometimes|string|max:150',
            'kelas' => 'sometimes|string|max:15',
            'tahun_ajar' => 'sometimes|string|max:10',
            'tipe_ujian' => 'sometimes|string|in:harian,sts,uts,uas',
            'semester' => 'sometimes|string|in:ganjil,genap',
            'kode_ujian' => 'sometimes|nullable|string|max:6|unique:ujians,kode_ujian,except,id',
            'durasi_menit' => 'sometimes|integer',
            'tanggal_ujian' => 'sometimes|date',
            'waktu_mulai' => 'sometimes|date_format:H:i:s',
            'waktu_selesai' => 'sometimes|date_format:H:i:s|after:waktu_mulai',
            'status' => 'sometimes|string|in:draft,published,ongoing,finished'
        ];
    }
}
