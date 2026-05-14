<?php

namespace App\Http\Controllers;

use App\Models\JawabanSiswa;
use App\Models\SiswaUjian;
use App\Models\Ujian;
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

    public function listManual(Ujian $ujian)
    {
        $this->authorize('view', $ujian);
        $siswaUjians = SiswaUjian::where('ujian_id', $ujian->id)
            ->where('status', 'dikirim')
            ->with([
                'user:id,name,email',
                'jawaban' => function ($query) {
                    $query->whereHas('soal', fn($q) =>
                        $q->whereIn('tipe_soal', ['isian', 'essay'])
                    )
                    ->whereNull('nilai_manual_guru')
                    ->with('soal:id,teks_soal,tipe_soal');
                }
            ])
            ->get()
            ->filter(fn($siswaUjian) => $siswaUjian->jawaban->isNotEmpty())
            ->map(function ($siswaUjian) {
                return [
                    'siswa_ujian_id' => $siswaUjian->id,
                    'siswa' => $siswaUjian->siswa,
                    'total_belum' => $siswaUjian->jawaban->count(),
                    'jawabans' => $siswaUjian->jawaban->map(fn($jawaban) => [
                        'jawaban_id' => $jawaban->id,
                        'soal_id' => $jawaban->soal->id,
                        'teks_soal' => $jawaban->soal->teks_soal,
                        'tipe_soal' => $jawaban->soal->tipe_soal,
                        'jawaban_teks' => $jawaban->jawaban_teks,
                    ]),
                ];
            })->values();

        return $this->successResponse([
            'ujian' => [
                'id' => $ujian->id,
                'judul_ujian' => $ujian->judul_ujian,
            ],
            'total_siswa_belum_dinilai' => $siswaUjians->count(),
            'siswa_ujians' => $siswaUjians,
        ]);
    }
    
    public function inputFillInBlank(Request $request, SiswaUjian $siswaUjian)
    {
        $this->authorize('update', $siswaUjian);

        $request->validate([
            'jawaban' => 'required|array',
            'jawaban.*.soal_id' => 'required|exists:soals,id',
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