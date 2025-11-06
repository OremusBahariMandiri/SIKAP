<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Services\ActivityHubClient;

class IpWhitelist
{
    protected $activityHub;

    public function __construct(ActivityHubClient $activityHub)
    {
        $this->activityHub = $activityHub;
    }

    /**
     * Handle an incoming request.
     *
     * Middleware ini untuk endpoint yang sangat sensitif
     * seperti admin panel atau API khusus
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowedIps = config('security.ip_whitelist', []);

        // Jika whitelist kosong, skip middleware
        if (empty($allowedIps)) {
            return $next($request);
        }

        $clientIp = $request->ip();

        // Cek apakah IP dalam whitelist
        if (!in_array($clientIp, $allowedIps)) {
            // Cek juga CIDR range jika ada
            if (!$this->isIpInRange($clientIp, $allowedIps)) {
                Log::warning('Unauthorized IP access attempt', [
                    'ip' => $clientIp,
                    'url' => $request->fullUrl(),
                    'user_agent' => $request->userAgent()
                ]);

                // Kirim ke Activity Hub
                try {
                    $this->activityHub->logSecurityEvent('unauthorized_access', 'high', [
                        'ip_address' => $clientIp,
                        'url' => $request->fullUrl(),
                        'user_agent' => $request->userAgent(),
                        'reason' => 'IP not in whitelist',
                        'user_id' => auth()->id(),
                        'user_email' => auth()->user()->email ?? null,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Failed to send to Activity Hub: ' . $e->getMessage());
                }

                // Cek jika request adalah AJAX/API
                if ($request->expectsJson() || $request->is('api/*')) {
                    return response()->json([
                        'message' => 'Access denied. Your IP is not authorized.'
                    ], 403);
                }

                // Generate error code untuk tracking
                $errorCode = Str::random(8);
                Log::warning('IP whitelist block: ' . $errorCode, [
                    'ip' => $clientIp,
                    'error_code' => $errorCode
                ]);

                // Untuk request biasa, tampilkan halaman error
                return response()->view('errors.security', [
                    'title' => 'Akses IP Tidak Diizinkan',
                    'message' => 'Alamat IP Anda tidak berada dalam daftar yang diizinkan untuk mengakses sumber daya ini.',
                    'details' => [
                        'Sumber daya ini hanya dapat diakses dari jaringan tertentu.',
                        'Hubungi administrator sistem untuk mendapatkan akses.'
                    ],
                    'errorCode' => $errorCode
                ], 403);
            }
        }

        return $next($request);
    }

    /**
     * Check if IP is in CIDR range
     */
    protected function isIpInRange(string $ip, array $ranges): bool
    {
        foreach ($ranges as $range) {
            // Jika bukan CIDR notation, skip
            if (strpos($range, '/') === false) {
                continue;
            }

            if ($this->ipInCidr($ip, $range)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if IP is in CIDR range
     */
    protected function ipInCidr(string $ip, string $cidr): bool
    {
        list($subnet, $mask) = explode('/', $cidr);

        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return false;
        }

        $ip = ip2long($ip);
        $subnet = ip2long($subnet);
        $mask = -1 << (32 - $mask);

        $subnet &= $mask;

        return ($ip & $mask) == $subnet;
    }
}