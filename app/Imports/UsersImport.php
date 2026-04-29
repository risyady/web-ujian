<?php

namespace App\Imports;

use App\Models\Jurusan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UsersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use SkipsErrors, SkipsFailures;
    
    public array $jurusans = [];

    public function __construct()
    {
        $this->jurusans = Jurusan::pluck('id', 'kode_jurusan')->toArray();
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new User([
            'nama' => $row['nama'],
            'email' => $row['email'],
            'password' => Hash::make('123456'),
            'role' => $row['role'],
            'nisn' => isset($row['nisn']) ? (string) $row['nisn'] : null,
            'jurusan_id' => $this->jurusans[$row['kode_jurusan']] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:150',
            'email' => 'required|email|unique:users|max:150',
            'role' => 'required|string|in:guru,siswa',
            'nisn' => 'nullable|required_if:role,siswa|string|unique:users|max:15',
            'kode_jurusan' => 'nullable|required_if:role,siswa|in:' . implode(',', array_keys($this->jurusans)),
        ];
    }
}
