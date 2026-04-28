<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
        $user = $this->route('user');

        return [
            'nama' => 'sometimes|string|max:150',
            'email' => [
                'sometimes',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => 'sometimes|string|min:6',
            'role' => 'sometimes|string|in:admin,guru,siswa',
            'nisn' => [
                'sometimes',
                'string',
                'max:15',
                Rule::unique('users', 'nisn')->ignore($user->id),
            ],
            'jurusan_id' => 'sometimes|exists:jurusans,id',
        ];
    }
}
