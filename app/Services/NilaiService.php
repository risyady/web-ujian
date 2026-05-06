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
                'isian' => $this->$jawaban->nilai_manual_guru ?? 0,
                'essay' => $this->$jawaban->nilai_manual_guru ?? 0,
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
        $pilihan = $jawaban->id_pilihan_terpilih ?? [];
        $correct = $jawaban->soal->pilihanJawaban
            ->where('is_true', true)
            ->pluck('id')
            ->toArray();

        return count($pilihan) === 1 && in_array($pilihan[0], $correct) ? 100 : 0;
    }

    private function calculateMultipleResponse($jawaban): float {
        $pilihan = $jawaban->id_pilihan_terpilih ?? [];
        $correct = $jawaban->soal->pilihanJawaban
            ->where('is_true', true)
            ->pluck('id')
            ->toArray();
        
        $total = 0;
        foreach ($pilihan as $id) {
            $pilihanJawaban = $jawaban->soal->pilihanJawaban->find($id);
            if ($pilihanJawaban && $pilihanJawaban->is_true) {
                $total += $pilihanJawaban->persentase_nilai;
            }
        }

        return max(0, $total);
    }

    private function calculateMatching($jawaban): float {
        $pasangan = $jawaban->pasangan_terpilih ?? [];
        $correct = $jawaban->soal->pilihanJawaban
            ->whereNotNull('teks_pilihan')
            ->whereNotNull('teks_pasangan');

        $correctTotal = 0;
        foreach ($pasangan as $p) {
            $match = $correct->where('id', $p['pilihan_id'])
                ->where('teks_pasangan', function($q) use ($p) {
                    return $q->id === $p['pasangan_id'];
                })
                ->isNotEmpty();
            
            if ($match) $correctTotal++;
        }

        return $correct->count() > 0
            ? ($correctTotal / $correct->count()) * 100
            : 0;
    }
}