<?php
// file: app/Listeners/TrackUserLogin.php

namespace App\Listeners;

use App\Services\ActivityHubClient;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Str;

class TrackUserLogin
{
    protected $activityHub;

    public function __construct(ActivityHubClient $activityHub)
    {
        $this->activityHub = $activityHub;
    }

    public function handle(Login $event)
    {
        // Generate session ID unik
        $sessionId = Str::uuid()->toString();

        // Simpan di session
        session(['activity_hub_session_id' => $sessionId]);

        // Ambil IP pengguna
        $ipAddress = request()->ip();

        // Register IP ke sistem monitoring
        $this->activityHub->registerIp(
            $ipAddress,
            'watch',
            'Auto-registered during user login'
        );

        // Track login di ActivityHub
        $this->activityHub->trackSession(
            $event->user->id,
            $event->user->email,
            $event->user->name,
            $sessionId
        );

        // Log aktivitas login
        $this->activityHub->logActivity([
            'user_id' => $event->user->id,
            'user_email' => $event->user->email,
            'user_name' => $event->user->name,
            'activity_type' => 'auth',
            'activity_name' => 'User login',
        ]);
    }
}