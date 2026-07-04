<?php

namespace App\Http\Requests;

use App\Models\PengaturanUjian;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Override;

class UpdatePengaturanUjianRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['guru', 'superadmin']);
    }

    #[Override]
    protected function prepareForValidation()
    {
        $ujian = $this->route('ujian');
        $current = $ujian->pengaturan;
        
        $this->mergeIfMissing([
            'bobot_objektif' => $current->bobot_objektif,
            'bobot_ganda_kompleks' => $current->bobot_ganda_kompleks,
            'bobot_menjodohkan' => $current->bobot_menjodohkan,
            'bobot_isian' => $current->bobot_isian,
            'bobot_essay' => $current->bobot_essay
        ]);
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $ujian = $this->route('ujian');

        $currentSettings = $ujian->pengaturan;

        return [
            'bobot_ganda_kompleks' => 'required|integer|min:0|max:100',
            'bobot_menjodohkan' => 'required|integer|min:0|max:100',
            'bobot_isian' => 'required|integer|min:0|max:100',
            'bobot_essay' => 'required|integer|min:0|max:100',
            'bobot_objektif' => [
                'required',
                'integer',
                'min:0',
                'max:100',
                function ($attribute, $value, $fail) {
                    $total = $this->bobot_ganda_kompleks 
                    + $this->bobot_menjodohkan 
                    + $this->bobot_isian 
                    + $this->bobot_essay 
                    + $this->bobot_objektif;
                    
                    if ($total !== 100) {
                        $fail("Total bobot harus 100, sekarang {$total}.");
                    } 
                }
            ],
        ];
    }
}
