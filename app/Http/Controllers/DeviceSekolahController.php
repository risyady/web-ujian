<?php

namespace App\Http\Controllers;

use App\Models\DeviceSekolah;
use Illuminate\Http\Request;

class DeviceSekolahController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $nama_pc = DeviceSekolah::latest()->paginate(10);
        return $this->successResponse($nama_pc);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_pc' => 'required|string|max:50|unique:device_sekolahs,nama_pc'
        ]);

        $nama_pc = DeviceSekolah::create($request->all());

        return $this->createdResponse($nama_pc, 'identitas PC berhasil dibuat');
    }

    /**
     * Display the specified resource.
     */
    public function show(DeviceSekolah $deviceSekolah)
    {
        return $this->successResponse($deviceSekolah);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DeviceSekolah $deviceSekolah)
    {
        $request->validate([
            'nama_pc' => 'sometimes|string|max:50|unique:device_sekolahs,nama_pc,except,id'
        ]);

        $deviceSekolah->update($request->all());

        return $this->successResponse($deviceSekolah, 'identitas PC berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeviceSekolah $deviceSekolah)
    {
        $deviceSekolah->delete();

        return $this->deletedResponse('identitas PC berhasil dihapus');
    }
}
