<?php

namespace App\Http\Controllers;

use App\Models\JawabanSiswa;
use App\Models\SiswaUjian;
use App\Services\NilaiService;
use Illuminate\Http\Request;

class NilaiController extends Controller
{
    public function __construct(protected NilaiService $nilaiService) {}

    public function show(SiswaUjian $siswaUjian) {
        $this->authorize('view', $siswaUjian);

        $jawaban = $siswaUjian->jawaban()
            ->with('soal.pilihanJawaban')
            ->get();
        
        return $this->successResponse([
            'siswa_ujian' => $siswaUjian->load('siswa'),
            'jawaban' => $jawaban,
        ]);
    }

    public function inputFillInBlank(Request $request, SiswaUjian $siswaUjian)
    {
        $this->authorize('update', $siswaUjian);

        $request->validate([
            'jawaban'            => 'required|array',
            'jawaban.*.soal_id'  => 'required|exists:soals,id',
            'jawaban.*.is_true' => 'required|boolean',
        ]);

        foreach ($request->jawaban as $item) {
            JawabanSiswa::where('siswa_ujian_id', $siswaUjian->id)
                ->where('soal_id', $item['soal_id'])
                ->update([
                    'nilai_manual_guru' => $item['is_true'] ? 100 : 0,
                ]);
        }

        $this->checkAllJawaban($siswaUjian);

        return $this->successResponse(null, 'Nilai isian berhasil disimpan');
    }

    public function inputEssay(Request $request, SiswaUjian $siswaUjian) {
        $this->authorize('update', $siswaUjian);

        $request->validate([
            'jawaban' => 'required|array',
            'jawaban.*.soal_id' => 'required|exists:soals,id',
            'jawaban.*.nilai_manual_guru' => 'required|numeric|min:0|max:100'
        ]);

        foreach ($request->jawaban as $item) {
            JawabanSiswa::where('siswa_ujian_id', $siswaUjian->id)
                ->where('soal_id', $item['soal_id'])
                ->update(['nilai_manual_guru' => $item['nilai_manual_guru']]);
        }

        $this->checkAllJawaban($siswaUjian);

        return $this->successResponse(null, 'Nillai essay berhasil disimpan');
    }

    public function finishedScoring(SiswaUjian $siswaUjian) {
        $this->authorize('update', $siswaUjian);

        if ($siswaUjian->status !== 'dikirim') {
            return $this->errorResponse('Status ujian tidak valid', 422);
        }

        $this->nilaiService->calculateFinalScore($siswaUjian);

        $siswaUjian->update(['status' => 'dinilai']);

        return $this->successResponse(
            $siswaUjian->fresh(),
            'Penilaian selesai'
        );
    }

    private function checkAllJawaban(SiswaUjian $siswaUjian): void {
        if ($siswaUjian->status === 'dikirim' && $siswaUjian->allJawabanScored()) {
            $this->nilaiService->calculateFinalScore($siswaUjian);
            $siswaUjian->update(['status' => 'dinilai']);
        }
    } 
}