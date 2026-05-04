<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSoalRequest;
use App\Http\Requests\UpdateSoalRequest;
use App\Models\Soal;
use App\Models\Ujian;
use Illuminate\Http\Request;

class SoalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Ujian $ujian)
    {
        $this->authorize('viewAny', [Soal::class, $ujian]);

        $soals = $ujian->soal()->with('pilihanJawaban')->get();

        return $this->successResponse($soals);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSoalRequest $request, Ujian $ujian)
    {
        $this->authorize('create', [Soal::class, $ujian]);

        $data = $request->validated();
        $data['ujian_id'] = $ujian->id;

        $soal = Soal::create($data);

        if(!empty($data['pilihan_jawaban'])) {
            $soal->pilihanJawaban()->createMany($data['pilihan_jawaban']);
        }

        return $this->createdResponse(
            $soal->load('pilihanJawaban'),
            'Soal berhasil dibuat'
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Ujian $ujian, Soal $soal)
    {
        $this->authorize('view', $soal);

        return $this->successResponse($soal->load('pilihanJawaban'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSoalRequest $request, Ujian $ujian, Soal $soal)
    {
        $this->authorize('update', $soal);

        $data = $request->validated();

        $soal->update($data);

        if (isset($data['pilihan_jawaban'])) {
            $soal->pilihanJawaban()->delete();
            $soal->pilihanJawaban()->createMany($data['pilihan_jawaban']);
        }

        return $this->successResponse(
            $soal->load('pilihanJawaban'),
            'Soal berhasil diperbarui'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ujian $ujian, Soal $soal)
    {
        $this->authorize('delete', $soal);

        $soal->delete();

        return $this->successResponse('Soal berhasil dihapus');
    }
}
