<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['admin', 'superadmin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:150',
            'email' => 'required|email|unique:users|max:150',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:admin,guru,siswa',
            'nisn' => 'required_if:role,siswa|string|unique:users|max:15',
            'jurusan_id' => 'required_if:role,siswa|exists:jurusans,id',
        ];
    }
}
