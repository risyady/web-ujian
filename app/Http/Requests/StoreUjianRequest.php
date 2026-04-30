<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUjianRequest extends FormRequest
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
            'judul_ujian' => 'required|string|max:150',
            'kelas' => 'required|string|max:15',
            'tahun_ajar' => 'required|string|max:10',
            'tipe_ujian' => 'required|string|in:harian,sts,uts,uas',
            'semester' => 'required|string|in:ganjil,genap',
            'kode_ujian' => 'nullable|string|max:6|unique:ujians, kode_ujian',
            'durasi_menit' => 'required|integer',
            'tanggal_ujian' => 'required|date',
            'waktu_mulai' => 'required|date_format:H:i:s',
            'waktu_selesai' => 'required|date_format:H:i:s|after:waktu_mulai',
        ];
    }
}
