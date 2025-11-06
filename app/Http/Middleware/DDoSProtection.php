<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;
use App\Services\ActivityHubClient;

class DDoSProtection
{
    protected $activityHub;

    public function __construct(ActivityHubClient $activityHub)
    {
        $this->activityHub = $activityHub;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        // PENTING: Selalu izinkan IP lokal dan pengembangan
        $localIps = ['127.0.0.1', '::1', 'localhost'];
        if (in_array($ip, $localIps) || strpos($ip, '192.168.') === 0) {
            return $next($request);
        }

        $userAgent = $request->userAgent() ?? 'Unknown';

        // Cek apakah IP sudah di-ban
        if ($this->isIpBanned($ip)) {
            Log::warning('Blocked banned IP attempting access', [
                'ip' => $ip,
                'url' => $request->fullUrl(),
                'user_agent' => $userAgent
            ]);

            // Kirim info ke Activity Hub
            try {
                $this->activityHub->logSecurityEvent('blocked_ip', 'medium', [
                    'ip_address' => $ip,
                    'url' => $request->fullUrl(),
                    'user_id' => auth()->id(),
                    'user_email' => auth()->user()->email ?? null,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send to Activity Hub: ' . $e->getMessage());
            }

            // Cek jika request adalah AJAX/API
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Your IP has been temporarily blocked due to suspicious activity.',
                    'retry_after' => $this->getBanTimeRemaining($ip)
                ], 403);
            }

            // Generate error code for tracking
            $errorCode = Str::random(8);
            Log::warning('IP access blocked: ' . $errorCode, [
                'ip' => $ip,
                'error_code' => $errorCode
            ]);

            // Untuk request biasa, tampilkan halaman error
            return response()->view('errors.blocked', [
                'retryAfter' => $this->getBanTimeRemaining($ip),
                'errorCode' => $errorCode
            ], 403);
        }

        // Track request patterns
        $this->trackRequest($ip, $request);

        // Analisa pola serangan - DENGAN THRESHOLD YANG LEBIH TINGGI
        if ($this->detectSuspiciousActivity($ip)) {
            $this->banIp($ip);

            Log::alert('Potential DDoS attack detected - IP banned', [
                'ip' => $ip,
                'requests' => $this->getRequestCount($ip),
                'user_agent' => $userAgent,
                'url' => $request->fullUrl()
            ]);

            // Kirim info ke Activity Hub
            try {
                $this->activityHub->logSecurityEvent('ddos_attempt', 'critical', [
                    'ip_address' => $ip,
                    'url' => $request->fullUrl(),
                    'request_count' => $this->getRequestCount($ip),
                    'user_id' => auth()->id(),
                    'user_email' => auth()->user()->email ?? null,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send to Activity Hub: ' . $e->getMessage());
            }

            // Cek jika request adalah AJAX/API
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Suspicious activity detected. Your IP has been blocked.',
                ], 429);
            }

            // Generate error code untuk tracking
            $errorCode = Str::random(8);
            Log::alert('Suspicious activity blocked: ' . $errorCode, [
                'ip' => $ip,
                'error_code' => $errorCode
            ]);

            // Untuk request biasa, tampilkan halaman error
            return response()->view('errors.blocked', [
                'retryAfter' => $this->getBanTimeRemaining($ip),
                'errorCode' => $errorCode
            ], 429);
        }

        // Deteksi bot jahat berdasarkan user agent - HANYA LOG, TIDAK BLOCK
        if ($this->isSuspiciousUserAgent($userAgent)) {
            Log::warning('Suspicious user agent detected', [
                'ip' => $ip,
                'user_agent' => $userAgent
            ]);

            // Kirim info ke Activity Hub
            try {
                $this->activityHub->logSecurityEvent('suspicious_activity', 'medium', [
                    'ip_address' => $ip,
                    'url' => $request->fullUrl(),
                    'user_agent' => $userAgent,
                    'user_id' => auth()->id(),
                    'user_email' => auth()->user()->email ?? null,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send to Activity Hub: ' . $e->getMessage());
            }
        }

        return $next($request);
    }

    /**
     * Track request dari IP - LEBIH SEDIKIT TRACKING
     */
    protected function trackRequest(string $ip, Request $request): void
    {
        // Skip jika permintaan ke asset statis
        if ($this->isStaticResource($request)) {
            return;
        }

        $key = "request_tracking:{$ip}";
        $requests = Cache::get($key, []);

        $requests[] = [
            'timestamp' => now()->timestamp,
            'url' => $request->path(),
            'method' => $request->method(),
        ];

        // Simpan 100 request terakhir dalam 5 menit
        $requests = array_slice($requests, -100);
        Cache::put($key, $requests, 300);
    }

    /**
     * Deteksi aktivitas mencurigakan - THRESHOLD YANG LEBIH TINGGI
     */
    protected function detectSuspiciousActivity(string $ip): bool
    {
        $key = "request_tracking:{$ip}";
        $requests = Cache::get($key, []);

        if (empty($requests)) {
            return false;
        }

        $now = now()->timestamp;

        // Hitung request dalam 1 menit terakhir
        $recentRequests = array_filter($requests, function ($req) use ($now) {
            return ($now - $req['timestamp']) <= 60;
        });

        $requestCount = count($recentRequests);

        // THRESHOLD LEBIH TINGGI: 1000 request per menit (dari 200)
        if ($requestCount > 1000) {
            return true;
        }

        // Deteksi pola request yang sama - THRESHOLD LEBIH TINGGI
        if ($requestCount > 200) { // Dari 50 menjadi 200
            $uniqueUrls = count(array_unique(array_column($recentRequests, 'url')));
            // Jika 95% request ke URL yang sama (dari 90%)
            if ($uniqueUrls <= ($requestCount * 0.05)) { // Dari 0.1 menjadi 0.05
                return true;
            }
        }

        return false;
    }

    /**
     * Cek apakah request ke resource statis
     */
    protected function isStaticResource(Request $request): bool
    {
        $path = $request->path();
        return preg_match('/(\.css|\.js|\.jpg|\.jpeg|\.png|\.gif|\.ico|\.svg|\.woff2?|\.ttf|\.eot|\.map)$/i', $path);
    }

    /**
     * Ban IP address - DURASI LEBIH PENDEK
     */
    protected function banIp(string $ip, int $minutes = 10): void // Dari 60 menit menjadi 10 menit
    {
        $key = "banned_ip:{$ip}";
        Cache::put($key, [
            'banned_at' => now(),
            'expires_at' => now()->addMinutes($minutes),
            'reason' => 'DDoS protection'
        ], $minutes * 60);

        // Tambahkan ke log ban
        $this->logBan($ip);

        // Kirim ke Activity Hub
        try {
            $this->activityHub->logSecurityEvent('blocked_ip', 'high', [
                'ip_address' => $ip,
                'reason' => 'DDoS protection - automated ban',
                'duration_minutes' => $minutes
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send to Activity Hub: ' . $e->getMessage());
        }
    }

    /**
     * Cek apakah IP sudah di-ban
     */
    protected function isIpBanned(string $ip): bool
    {
        return Cache::has("banned_ip:{$ip}");
    }

    /**
     * Get remaining ban time
     */
    protected function getBanTimeRemaining(string $ip): int
    {
        $key = "banned_ip:{$ip}";
        $banData = Cache::get($key);

        if (!$banData) {
            return 0;
        }

        $expiresAt = $banData['expires_at'] ?? now();
        return max(0, now()->diffInSeconds($expiresAt));
    }

    /**
     * Get request count untuk IP
     */
    protected function getRequestCount(string $ip): int
    {
        $key = "request_tracking:{$ip}";
        $requests = Cache::get($key, []);

        $now = now()->timestamp;
        $recentRequests = array_filter($requests, function ($req) use ($now) {
            return ($now - $req['timestamp']) <= 60;
        });

        return count($recentRequests);
    }

    /**
     * Log banned IP
     */
    protected function logBan(string $ip): void
    {
        $banLog = Cache::get('ban_log', []);
        $banLog[] = [
            'ip' => $ip,
            'banned_at' => now()->toDateTimeString(),
            'reason' => 'DDoS protection'
        ];

        // Simpan 1000 log terakhir
        $banLog = array_slice($banLog, -1000);
        Cache::put('ban_log', $banLog, 86400); // 24 jam
    }

    /**
     * Deteksi user agent yang mencurigakan - KURANGI POLA DETEKSI
     */
    protected function isSuspiciousUserAgent(?string $userAgent): bool
    {
        if (!$userAgent) {
            return true;
        }

        // Kurangi pola yang sering salah positif
        $suspiciousPatterns = [
            'nikto', 'scanner', 'nmap', 'masscan',
            'sqlmap', 'havij', 'acunetix'
        ];

        $userAgentLower = strtolower($userAgent);

        foreach ($suspiciousPatterns as $pattern) {
            if (strpos($userAgentLower, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }
}