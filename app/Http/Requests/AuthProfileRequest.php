<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AuthProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {        
        return [
            'nama' => 'sometimes|string|max:150',
            'email' => 'sometimes|email|max:150|unique:users,email,except,id',
            'password' => 'sometimes|string|min:6',
        ];
    }
}
