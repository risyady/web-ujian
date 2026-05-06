<?php

namespace App\Http\Controllers;

use App\Models\SiswaUjian;
use App\Models\Ujian;
use Illuminate\Http\Request;

class SiswaUjianController extends Controller
{
    public function redeemCode(Request $request) {
        $request->validate([
            'kode_ujian' => 'required|string'
        ]);

        $user = auth()->user();

        if (!$user->isSiswa()) {
            return $this->errorResponse('Hanya siswa yang bisa masuk ujian', 403);
        }

        $ujian = Ujian::where('kode_ujian', $request->kode_ujian)->first();

        if (!$ujian) {
            return $this->errorResponse('Kode ujian tidak valid', 404);
        }

        if ($ujian->status !== 'ongoing') {
            return $this->errorResponse('Ujian belum dimulai atau telah selesai', 422);
        }

        $hasRedeem = SiswaUjian::where('ujian_id', $ujian->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($hasRedeem) {
            return $this->errorResponse('Kamu sudah mengikuti ujian ini', 422);
        }

        $soals = $ujian->soal()
            ->with('pilihanJawaban')
            ->get()
            ->shuffle()
            ->map(function ($soal) {
                $soal->pilihanJawaban = $soal->pilihanJawaban->shuffle()->values();
                return $soal;
            })
            ->values();

        $siswaUjian = SiswaUjian::create([
            'ujian_id' => $ujian->id,
            'user_id' => $user->id,
            'waktu_mulai' => now(),
            'status' => 'pengerjaan',
            'urutan_soal' => $soals->pluck('id')
        ]);

        return $this->successResponse([
            'siswa_ujian' => $siswaUjian,
            'ujian' => [
                'judul_ujian' => $ujian->judul_ujian,
                'durasi_menit' => $ujian->durasi_menit,
                'waktu_mulai' => $ujian->waktu_mulai,
                'waktu_selesai' => $ujian->waktu_selesai,
            ],
            'soals' => $soals,
        ], 'Berhasil masuk ujian');
    }

    public function submit(SiswaUjian $siswaUjian) {
        $user = auth()->user();

        if ($siswaUjian->user_id !== $user->id) {
            return $this->errorResponse('Unauthorized.', 403);
        }

        if ($siswaUjian->status !== 'pengerjaan') {
            return $this->errorResponse('Ujian sudah disubmit', 422);
        }

        $this->submitProcess($siswaUjian);

        return $this->successResponse($siswaUjian, 'Ujian berhasil disubmit');
    }

    public function submitProcess(SiswaUjian $siswaUjian): void {
        $siswaUjian->update([
            'status' => 'dikirim',
            'waktu_selesai' => now(),
        ]);
    }
}
