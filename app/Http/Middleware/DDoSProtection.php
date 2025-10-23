<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Str;

class DDoSProtection
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        $userAgent = $request->userAgent() ?? 'Unknown';

        // Cek apakah IP sudah di-ban
        if ($this->isIpBanned($ip)) {
            Log::warning('Blocked banned IP attempting access', [
                'ip' => $ip,
                'url' => $request->fullUrl(),
                'user_agent' => $userAgent
            ]);

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

        // Analisa pola serangan
        if ($this->detectSuspiciousActivity($ip)) {
            $this->banIp($ip);

            Log::alert('Potential DDoS attack detected - IP banned', [
                'ip' => $ip,
                'requests' => $this->getRequestCount($ip),
                'user_agent' => $userAgent,
                'url' => $request->fullUrl()
            ]);

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

        // Deteksi bot jahat berdasarkan user agent
        if ($this->isSuspiciousUserAgent($userAgent)) {
            Log::warning('Suspicious user agent detected', [
                'ip' => $ip,
                'user_agent' => $userAgent
            ]);
        }

        return $next($request);
    }

    /**
     * Track request dari IP
     */
    protected function trackRequest(string $ip, Request $request): void
    {
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
     * Deteksi aktivitas mencurigakan
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

        // Threshold: lebih dari 200 request per menit
        if ($requestCount > 200) {
            return true;
        }

        // Deteksi pola request yang sama berulang (potential bot)
        if ($requestCount > 50) {
            $uniqueUrls = count(array_unique(array_column($recentRequests, 'url')));
            // Jika 90% request ke URL yang sama, kemungkinan bot
            if ($uniqueUrls <= ($requestCount * 0.1)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Ban IP address
     */
    protected function banIp(string $ip, int $minutes = 60): void
    {
        $key = "banned_ip:{$ip}";
        Cache::put($key, [
            'banned_at' => now(),
            'expires_at' => now()->addMinutes($minutes),
            'reason' => 'DDoS protection'
        ], $minutes * 60);

        // Tambahkan ke log ban
        $this->logBan($ip);
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
     * Deteksi user agent yang mencurigakan
     */
    protected function isSuspiciousUserAgent(?string $userAgent): bool
    {
        if (!$userAgent) {
            return true;
        }

        $suspiciousPatterns = [
            'bot', 'crawl', 'spider', 'scrape', 'harvest',
            'curl', 'wget', 'python', 'java', 'perl',
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