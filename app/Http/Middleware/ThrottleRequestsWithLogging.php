<?php
// file: app/Http/Middleware/ThrottleRequestsWithLogging.php

namespace App\Http\Middleware;

use App\Services\ActivityHubClient;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ThrottleRequestsWithLogging
{
    protected $limiter;
    protected $activityHub;

    public function __construct(RateLimiter $limiter, ActivityHubClient $activityHub)
    {
        $this->limiter = $limiter;
        $this->activityHub = $activityHub;
    }

    public function handle(Request $request, Closure $next, $maxAttempts = 60, $decayMinutes = 1): Response
    {
        $key = $request->ip();
        $maxAttempts = (int) $maxAttempts;

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            // Log throttle event
            Log::warning('Rate limit exceeded', [
                'ip' => $request->ip(),
                'path' => $request->path(),
                'method' => $request->method(),
                'attempts' => $this->limiter->attempts($key)
            ]);

            // Log throttle event ke Activity Hub dengan try-catch untuk menghindari error
            try {
                $this->activityHub->logSecurityEvent('throttle_limit', 'medium', [
                    'ip_address' => $request->ip(),
                    'endpoint' => $request->path(),
                    'request_count' => $this->limiter->attempts($key),
                    'user_id' => auth()->id(),
                    'user_email' => auth()->user()->email ?? null,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to log throttle event to Activity Hub: ' . $e->getMessage());
            }

            return $this->buildResponse($request, $key);
        }

        $this->limiter->hit($key, $decayMinutes * 60);

        return $next($request);
    }

    protected function buildResponse(Request $request, $key)
    {
        $retryAfter = $this->limiter->availableIn($key);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => false,
                'message' => 'Too many requests',
                'retry_after' => $retryAfter
            ], 429);
        }

        return redirect()->route('security.error', [
            'type' => 'throttle',
            'retryAfter' => $retryAfter
        ])->with('error', 'Terlalu banyak permintaan dalam waktu singkat. Silakan coba lagi setelah ' . ceil($retryAfter / 60) . ' menit.');
    }
}