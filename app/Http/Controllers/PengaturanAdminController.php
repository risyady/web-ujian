<?php

namespace App\Http\Controllers;

use App\Models\PengaturanAdmin;
use Illuminate\Http\Request;

class PengaturanAdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', PengaturanAdmin::class);

        return $this->successResponse(PengaturanAdmin::all());
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $this->authorize('update', PengaturanAdmin::first());

        $request->validate([
            'pengaturan' => 'required|array',
            'pengaturan.*.key' => 'required|exists:pengaturan_aplikasis,key',
            'pengaturan.*.value' => 'nullable|string'
        ]);

        foreach ($request->pengaturan as $item) {
            PengaturanAdmin::set($item['key'], $item['value']);
        }

        return $this->successResponse(
            PengaturanAdmin::all(),
            'Pengaturan berhasil diperbarui'
        );
    }
}
