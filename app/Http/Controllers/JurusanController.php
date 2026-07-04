<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use Illuminate\Http\Request;

class JurusanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $jurusan = Jurusan::latest()->paginate(10);
        return $this->successResponse($jurusan);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_jurusan' => 'required|string|max:100',
            'kode_jurusan' => 'required|string|max:10',
        ]);

        $jurusan = Jurusan::create($request->all());

        return $this->createdResponse($jurusan, 'Jurusan berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(Jurusan $jurusan)
    {
        return $this->successResponse($jurusan);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jurusan $jurusan)
    {
        $request->validate([
            'nama_jurusan' => 'sometimes|string|max:100',
            'kode_jurusan' => 'sometimes|string|max:10',
        ]);

        $jurusan->update($request->all());

        return $this->successResponse($jurusan, 'Jurusan berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jurusan $jurusan)
    {
        $jurusan->delete();

        return $this->deletedResponse('Jurusan berhasil dihapus');
    }
}
