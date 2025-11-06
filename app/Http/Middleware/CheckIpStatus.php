<?php

namespace App\Http\Middleware;

use App\Services\ActivityHubClient;
use Closure;
use Illuminate\Support\Facades\Log;

class CheckIpStatus
{
    public function handle($request, Closure $next)
    {
        try {
            $ip = $request->ip();
            $activityHub = new ActivityHubClient();

            // Daftarkan IP secara otomatis saat ada request masuk
            $activityHub->registerIp($ip, 'watch', 'Auto-registered during access');

            // Cek status IP
            $ipStatus = $activityHub->checkIpStatus($ip);

            if (!empty($ipStatus) && !empty($ipStatus['data'])) {
                // Jika IP di blacklist, blokir akses
                if (!empty($ipStatus['data']['is_blacklisted'])) {
                    $activityHub->logSecurityEvent('blacklisted_ip_access', 'high', [
                        'ip_address' => $ip
                    ]);
                    abort(403, 'Access denied');
                }

                // Jika IP di watch list, catat aktivitasnya
                if (!empty($ipStatus['data']['is_watched'])) {
                    $activityHub->logSecurityEvent('watched_ip_access', 'medium', [
                        'ip_address' => $ip
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Log::error('Error in CheckIpStatus middleware', [
                'error' => $e->getMessage(),
                'ip' => $request->ip()
            ]);
        }

        return $next($request);
    }
}