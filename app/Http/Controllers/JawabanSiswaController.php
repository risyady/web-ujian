<?php

namespace App\Http\Controllers;

use App\Models\JawabanSiswa;
use App\Models\SiswaUjian;
use Illuminate\Http\Request;

class JawabanSiswaController extends Controller
{
    public function save(Request $request, SiswaUjian $siswaUjian) {
        $user = auth()->user();

        if ($siswaUjian->siswa_id !== $user->id) {
            return $this->errorResponse('Unauthorized.', 403);
        }

        if ($siswaUjian->status !== 'pengerjaan') {
            return $this->errorResponse('Ujian sudah selesai', 422);
        }

        if ($siswaUjian->ujianTimeOut()) {
            $this->autoSubmit($siswaUjian);
            return $this->errorResponse('Waktu ujian telah habis, ujian otomatis disubmit', 422);
        }

        $request->validate([
            'soal_id' => 'required|exists:soals,id',
            'id_pilihan_terpilih' => 'nullable|ar',
            'jawaban_teks' => 'nullable|string',
            'pasangan_terpilih' => 'nullable|array',

            'pasangan_terpilih.*.pilihan_id' => 'required_with:pasangan_terpilih|integer',
            'pasangan_terpilih.*.pasangan_id' => 'required_with:pasangan_terpilih|integer',
        ]);

        $jawaban = JawabanSiswa::updateOrCreate(
            [
                'siswa_ujian_id' => $siswaUjian->id,
                'soal_id' => $request->soal_id,
            ],
            [
                'id_pilihan_terpilih' => $request->id_pilihan_terpilih,
                'jawaban_teks'        => $request->jawaban_teks,
                'pasangan_terpilih'   => $request->pasangan_terpilih,
            ]
        );

        return $this->successResponse([
            'jawaban' => $jawaban,
            'sisa_waktu' => $siswaUjian->remainingTimeSecond(),
        ], 'Jawaban berhasil disimpan');
    }

    private function autoSubmit(SiswaUjian $siswaUjian): void {
        if ($siswaUjian->status === 'pengerjaan') {
            app(SiswaUjianController::class)->submitProcess($siswaUjian);
        }
    }
}
