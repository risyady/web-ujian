<?php

namespace App\Http\Middleware;

use App\Models\PengaturanAdmin;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CekIpUjian
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowedIp = PengaturanAdmin::ambil('allowed_api');

        if (!$allowedIp) {
            return $next($request);
        }

        $ipSiswa = trim(explode(',', $request->header('X-Forwarded-For') ?? $request->ip())[0]);
        $allowedIps = array_map('trim', explode(',', $allowedIp));

        if(!in_array($ipSiswa, $allowedIps)) {
            return response()->json([
                'message' => 'Akses ditolak. Ujian hanya bisa diakses dari jaringan sekolah.'
            ], 403);
        }

        return $next($request);
    }
}
