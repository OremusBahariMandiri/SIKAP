<?php
// file: app/Listeners/TrackUserLogout.php

namespace App\Listeners;

use App\Services\ActivityHubClient;
use Illuminate\Auth\Events\Logout;

class TrackUserLogout
{
    protected $activityHub;

    public function __construct(ActivityHubClient $activityHub)
    {
        $this->activityHub = $activityHub;
    }

    public function handle(Logout $event)
    {
        // Log aktivitas logout
        $this->activityHub->logActivity([
            'user_id' => $event->user->id,
            'user_email' => $event->user->email,
            'user_name' => $event->user->name,
            'activity_type' => 'auth',
            'activity_name' => 'User logout',
        ]);

        // Logout session jika ada
        if (session()->has('activity_hub_session_id')) {
            $this->activityHub->logoutSession(session('activity_hub_session_id'));
            session()->forget('activity_hub_session_id');
        }
    }
}