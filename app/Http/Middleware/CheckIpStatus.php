<?php

namespace App\Http\Middleware;

use App\Services\ActivityHubClient;
use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CheckIpStatus
{
    public function handle($request, Closure $next)
    {
        $ip = $request->ip();
        $applicationId = config('app.id'); // ID aplikasi Anda

        // Gunakan ActivityHubClient untuk memeriksa status IP
        $activityHub = new ActivityHubClient();

        // Gunakan caching untuk mengurangi beban API
        $cacheKey = "ip_status:{$ip}:{$applicationId}";
        $ipStatus = Cache::remember($cacheKey, now()->addMinutes(5), function () use ($activityHub, $ip) {
            // Memanggil API monitoring untuk memeriksa status IP
            return $activityHub->checkIpStatus($ip);
        });

        if (!$ipStatus) {
            // Jika tidak bisa mengambil status, lanjutkan saja
            // Anda bisa memilih untuk memblokir atau membiarkan, tergantung kebutuhan
            Log::warning("Unable to check IP status for {$ip}");
            return $next($request);
        }

        // Cek apakah IP di blacklist berdasarkan respons API
        if ($ipStatus['is_blacklisted'] ?? false) {
            // Catat upaya akses dari IP yang di-blacklist
            $activityHub->logSecurityEvent('blacklisted_ip_access', 'high', [
                'ip_address' => $ip,
                'application_id' => $applicationId
            ]);

            // Blokir akses
            abort(403, 'Access denied');
        }

        // Jika IP di watch list, catat aktivitasnya
        if ($ipStatus['is_watched'] ?? false) {
            $activityHub->logSecurityEvent('watched_ip_access', 'medium', [
                'ip_address' => $ip,
                'application_id' => $applicationId
            ]);
        }

        return $next($request);
    }
}