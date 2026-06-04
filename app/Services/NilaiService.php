<?php

namespace App\Services;

use App\Models\SiswaUjian;

class NilaiService {
    public function calculateFinalScore(SiswaUjian $siswaUjian): void {
        $jawabans = $siswaUjian->jawaban()->with('soal.pilihanJawaban')->get();
        $pengaturan = $siswaUjian->ujian->pengaturan;

        $nilaiEachType = [
            'objektif' => [],
            'ganda_kompleks' => [],
            'menjodohkan' => [],
            'isian' => [],
            'essay' => [],
        ];

        foreach ($jawabans as $jawaban) {
            $type = $jawaban->soal->tipe_soal;
            $nilai = match($type) {
                'objektif' => $this->calculateObjective($jawaban),
                'ganda_kompleks' => $this->calculateMultipleResponse($jawaban),
                'menjodohkan' => $this->calculateMatching($jawaban),
                'isian' => $jawaban->nilai_manual_guru ?? 0,
                'essay' => $jawaban->nilai_manual_guru ?? 0,
            };

            if ($nilai !== null) {
                $nilaiEachType[$type][] = $nilai;
            }
        }

        $mean = [];
        foreach ($nilaiEachType as $type => $values) {
            $mean[$type] = count($values) > 0
                ? array_sum($values) / count($values)
                : 0;
        }

        $finalScore = 
            ($mean['objektif'] * ($pengaturan->bobot_objektif / 100)) +
            ($mean['ganda_kompleks'] * ($pengaturan->bobot_ganda_kompleks / 100)) +
            ($mean['menjodohkan'] * ($pengaturan->bobot_menjodohkan / 100)) +
            ($mean['isian'] * ($pengaturan->bobot_isian / 100)) +
            ($mean['essay'] * ($pengaturan->bobot_essay / 100));

        $siswaUjian->update(['nilai_akhir' => round($finalScore, 2)]);
    }

    private function calculateObjective($jawaban): float {
        $pilihan = is_array($jawaban->id_pilihan_terpilih)
            ? $jawaban->id_pilihan_terpilih
            : (json_decode($jawaban->id_pilihan_terpilih, true) ?: []);
        $correct = $jawaban->soal->pilihanJawaban
            ->where('is_true', true)
            ->pluck('id')
            ->toArray();

        return count($pilihan) === 1 && in_array($pilihan[0], $correct) ? 100 : 0;
    }

    private function calculateMultipleResponse($jawaban): float {
        $pilihan = is_array($jawaban->id_pilihan_terpilih)
            ? $jawaban->id_pilihan_terpilih
            : (json_decode($jawaban->id_pilihan_terpilih, true) ?: []);
        
        $total = 0;
        foreach ($pilihan as $id) {
            $pilihanJawaban = $jawaban->soal->pilihanJawaban->firstWhere('id', $id);
            if ($pilihanJawaban && $pilihanJawaban->is_true) {
                $total += $pilihanJawaban->persentase_nilai;
            }
        }

        return max(0, $total);
    }

    private function calculateMatching($jawaban): float {
        $pasangan = is_array($jawaban->pasangan_terpilih)
            ? $jawaban->pasangan_terpilih
            : (json_decode($jawaban->pasangan_terpilih, true) ?: []);

        $leftCount = $jawaban->soal->pilihanJawaban->whereNotNull('teks_pilihan')->count();
        if ($leftCount === 0) return 0;

        $correctTotal = 0;
        foreach ($pasangan as $p) {
            if (!isset($p['pilihan_id'], $p['pasangan_id'])) continue;

            if ($p['pilihan_id'] === $p['pasangan_id']) {
                $correctTotal++;
            }
        }

        return $correctTotal * 100;
    }

    public function calculateForJawaban($jawaban): ?float {
    return match($jawaban->soal->tipe_soal) {
        'objektif'       => $this->calculateObjective($jawaban),
        'ganda_kompleks' => $this->calculateMultipleResponse($jawaban),
        'menjodohkan'    => $this->calculateMatching($jawaban),
        default          => null,
    };
}
}