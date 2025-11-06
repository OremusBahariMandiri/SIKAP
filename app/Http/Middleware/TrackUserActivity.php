<?php
// file: app/Http/Middleware/TrackUserActivity.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\ActivityHubClient;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    protected $activityHub;

    public function __construct(ActivityHubClient $activityHub)
    {
        $this->activityHub = $activityHub;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Track activity hanya untuk user yang sudah login
        // dan skip untuk route tertentu seperti API atau assets
        if (auth()->check() &&
            !$request->is('api/*') &&
            !$request->is('_debugbar/*') &&
            !$request->is('js/*') &&
            !$request->is('css/*') &&
            !$request->is('images/*')) {

            $this->activityHub->logActivity([
                'user_id' => auth()->id(),
                'user_email' => auth()->user()->email,
                'user_name' => auth()->user()->name,
                'activity_type' => 'page_visit',
                'activity_name' => 'Visited: ' . $request->path(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'status_code' => $response->getStatusCode(),
            ]);
        }

        return $response;
    }
}