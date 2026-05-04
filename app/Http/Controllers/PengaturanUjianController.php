<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePengaturanUjianRequest;
use App\Models\Ujian;

class PengaturanUjianController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(Ujian $ujian)
    {
        return $this->successResponse($ujian->pengaturan);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePengaturanUjianRequest $request, Ujian $ujian)
    {
        $data = $request->validated();
        $ujian->pengaturan()->update($data);

        return $this->successResponse($ujian->pengaturan->fresh(), 'Pengaturan berhasil diperbarui');
    }
}
