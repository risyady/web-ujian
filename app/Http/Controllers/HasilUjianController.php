<?php

namespace App\Http\Controllers;

use App\Models\SiswaUjian;
use App\Models\Ujian;
use Illuminate\Http\Request;

class HasilUjianController extends Controller
{
    public function siswaHistory(Request $request) {
        $user = auth()->user();

        if(!$user->isSiswa()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $history = SiswaUjian::where('siswa_id', $user->id)
            ->with('ujian:id,judul_ujian,tipe_ujian,tanggal_ujian,semester,tahun_ajar')
            ->latest()
            ->paginate(10);

        return $this->successResponse($history);
    }

    public function siswaResult(SiswaUjian $siswaUjian) {
        $this->authorize('view', $siswaUjian);

        if ($siswaUjian->status === 'pengerjaan') {
            return $this->errorResponse('Ujian belum selesai', 422);
        }

        $jawabans = $siswaUjian->jawaban()->with(['soal.pilihanJawaban'])->get()
            ->map(function ($jawaban) {
                $tipe = $jawaban->soal->tipe_soal;
                $isManual = in_array($tipe, ['isian', 'essay']);

                return [
                    'soal' => [
                        'id' => $jawaban->soal->id,
                        'teks_soal' => $jawaban->soal->teks_soal,
                        'tipe_soal' => $tipe,
                        'path_gambar' => $jawaban->soal->path_gambar,
                        'pilihan_jawaban' => $jawaban->soal->pilihanJawaban
                    ],
                    'jawaban_siswa' => [
                        'id_pilihan_terpilih' => $jawaban->id_pilihan_terpilih,
                        'jawaban_teks' => $jawaban->jawaban_teks,
                        'pasangan_terpilih' => $jawaban->pasangan_terpilih
                    ],
                    'nilai' => $isManual
                                ? $jawaban->nilai_manual_guru
                                : $jawaban->nilai_manual_guru,
                    'sudah_dinilai' => $isManual
                                        ? !is_null($jawaban->nilai_manual_guru)
                                        : true
                ];
            });

            $breakdown = $this->hitungBreakdown($siswaUjian);
        
        return $this->successResponse([
            'siswa_ujian' => [
                'id' => $siswaUjian->id,
                'waktu_mulai' => $siswaUjian->waktu_mulai,
                'waktu_selesai' => $siswaUjian->waktu_selesai,
                'nilai_akhir' => $siswaUjian->nilai_akhir,
                'status' => $siswaUjian->status
            ],
            'breakdown' => $breakdown,
            'jawaban' => $jawabans
        ]);
    }

    public function resultEachExam(Ujian $ujian) {
        $this->authorize('view', $ujian);

        $siswaUjians = $ujian->siswaUjian()->with('siswa')->get()
            ->map(function ($siswaUjian) {
                return [
                    'siswa_ujian_id' => $siswaUjian->id,
                    'siswa' => $siswaUjian->siswa,
                    'status' => $siswaUjian->status,
                    'waktu_mulai' => $siswaUjian->waktu_mulai,
                    'waktu_selesai' => $siswaUjian->waktu_selesai,
                    'nilai_akhir' => $siswaUjian->nilai_akhir,
                    'breakdown' => $this->hitungBreakdown($siswaUjian),
                ];
            });
        
        $nilaiList = $siswaUjians->where('status', 'dinilai')->pluck('nilai_akhir')->filter();

        $statistik = $nilaiList->isNotEmpty() ? [
            'rata_rata' => round($nilaiList->avg(), 2),
            'nilai_tertinggi' => $nilaiList->max(),
            'nilai_terendah' => $nilaiList->min(),
            'total_siswa' => $siswaUjians->count(),
            'sudah_dinilai' => $siswaUjians->where('status', 'dinilai')->count(),
            'belum_dinilai' => $siswaUjians->where('status', 'dikirim')->count()
        ] : [
            'rata_rata' => null,
            'nilai_tertinggi' => null,
            'nilai_terendah' => null,
            'total_siswa' => $siswaUjians->count(),
            'sudah_dinilai' => 0,
            'belum_dinilai' => $siswaUjians->count()
        ];

        return $this->successResponse([
            'ujian' => [
                'id' => $ujian->id,
                'judul_ujian' => $ujian->judul_ujian,
                'tipe_ujian' => $ujian->tipe_ujian,
                'tanggal_ujian' => $ujian->tanggal_ujian
            ],
            'statistik' => $statistik,
            'siswa_ujian' => $siswaUjians
        ]);
    }

    public function detailJawabanSiswa(SiswaUjian $siswaUjian) {
        $this->authorize('view', $siswaUjian);

        $jawabans = $siswaUjian->jawaban()->with('soal.pilihanJawaban')->get()
            ->map(function ($jawaban) {
                return [
                    'soal' => [
                        'id' => $jawaban->soal->id,
                        'teks_soal' => $jawaban->soal->teks_soal,
                        'tipe_soal' => $jawaban->soal->tipe_soal,
                        'jalur_gambar' => $jawaban->soal->path_gambar,
                        'pilihan_jawaban' => $jawaban->soal->pilihanJawaban,
                    ],
                    'jawaban_siswa' => [
                        'id_pilihan_terpilih' => $jawaban->id_pilihan_terpilih,
                        'jawaban_teks' => $jawaban->jawaban_teks,
                        'pasangan_terpilih' => $jawaban->pasangan_terpilih,
                        'nilai_manual_guru' => $jawaban->nilai_manual_guru,
                    ],
                    'nilai' => $jawaban->nilai_manual_guru
                ];
            });
            
        return $this->successResponse([
            'siswa'      => $siswaUjian->user,
            'nilai_akhir'=> $siswaUjian->nilai_akhir,
            'breakdown'  => $this->hitungBreakdown($siswaUjian),
            'jawabans'   => $jawabans,
        ]);
    }

    private function hitungBreakdown(SiswaUjian $siswaUjian): array {
        $jawabans = $siswaUjian->jawaban()->with('soal')->get();

        $tipes = ['objektif', 'ganda_kompleks', 'menjodohkan', 'isian', 'essay'];

        $breakdown = [];
        foreach ($tipes as $tipe) {
            $soalType = $jawabans->filter(fn($j) => $j->soal->tipe_soal === $tipe);

            if ($soalType->isEmpty()) continue;

            $isManual      = in_array($tipe, ['isian', 'essay']);
            $sudahDinilai  = $soalType->filter(fn($j) => !is_null($j->nilai_manual_guru))->count();

            $breakdown[$tipe] = [
                'total_soal' => $soalType->count(),
                'sudah_dinilai' => $isManual ? $sudahDinilai : $soalType->count(),
                'rata_nilai'   => $isManual
                                ? ($sudahDinilai > 0
                                    ? round($soalType->whereNotNull('nilai_manual_guru')->avg('nilai_manual_guru'), 2)
                                    : null)
                                : round($soalType->avg('nilai_manual_guru') ?? 0, 2),
            ];
        }

        return $breakdown;
    }
}
