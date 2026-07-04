<?php

namespace App\Http\Controllers;

use App\Models\DeviceSekolah;
use App\Models\PengaturanAdmin;
use Illuminate\Http\Request;

class ValidasiAksesUjianController extends Controller
{
    public function validasi(Request $request) {
        $metode = $request->input('metode');
        $idPcClient = $request->input('id_pc');
        $radiusMaksimalMeter = 150;

        if($metode === 'GPS') {
            $latitudeSiswa = $request->input('lat');
            $longitudeSiswa = $request->input('lon');

            if(!$latitudeSiswa || !$longitudeSiswa) {
                return $this->errorResponse('Data GPS tidak lengkap', 400);
            }

            $latitudeSekolah = PengaturanAdmin::where('key', 'latitude')->value('value');
            $longitudeSekolah = PengaturanAdmin::where('key', 'longitude')->value('value');

            if(!$latitudeSekolah || !$longitudeSekolah) {
                return $this->errorResponse('Konfigurasi sekolah belum diatur', 500);
            }

            $jarakSiswaKeLokasiSekolah = $this->hitungJarakHaversine((float) $latitudeSiswa, (float) $longitudeSiswa, (float) $latitudeSekolah, (float) $longitudeSekolah);

            if($jarakSiswaKeLokasiSekolah <= $radiusMaksimalMeter) {
                return $this->successResponse([
                    'metode' => 'GPS',
                    'jarak_meter' => round($jarakSiswaKeLokasiSekolah, 2)
                ], "Akses diizinkan! GPS terverifikasi (Jarak: {$jarakSiswaKeLokasiSekolah}m dari sekolah)");
            }
        }

        if(!empty($idPcClient)) {
            $pcValid = DeviceSekolah::where('nama_pc', strtoupper($idPcClient))->first();

            if($pcValid) {
                return $this->successResponse([
                    'metode' => 'LOCAL_STORAGE',
                    'nama_pc' => $pcValid->nama_pc
                ], "Akses diizinkan! Perangkat terverifikasi ({$pcValid->nama_pc})");
            }
        }

        return $this->errorResponse('Akses ditolak! Anda berada di luar area sekolah dan perangkat tidak terdaftar', 403);
    }

    private function hitungJarakHaversine($lat1, $lon1, $lat2, $lon2) {
        //jari jari bumi dalam meter
        $_earthRadius = 6371000;
        
        $latDelta = deg2rad($lat2-$lat1);
        $lonDelta = deg2rad($lon2-$lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $_earthRadius * $c;
    }
}
