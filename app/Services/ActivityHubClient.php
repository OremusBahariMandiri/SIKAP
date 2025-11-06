<?php
// file: app/Services/ActivityHubClient.php (di aplikasi klien A, B, C)

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ActivityHubClient
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.activity_hub.api_key');
        $this->baseUrl = config('services.activity_hub.url');
    }

    public function logActivity($data)
    {
        return Http::withHeaders([
            'X-API-Key' => $this->apiKey,
        ])->post($this->baseUrl . '/api/activities', array_merge($data, [
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]));
    }

    public function trackSession($userId, $userEmail, $userName, $sessionId)
    {
        return Http::withHeaders([
            'X-API-Key' => $this->apiKey,
        ])->post($this->baseUrl . '/api/sessions/track', [
            'user_id' => $userId,
            'user_email' => $userEmail,
            'user_name' => $userName,
            'session_id' => $sessionId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    public function logoutSession($sessionId)
    {
        return Http::withHeaders([
            'X-API-Key' => $this->apiKey,
        ])->post($this->baseUrl . '/api/sessions/logout', [
            'session_id' => $sessionId,
        ]);
    }

    public function logSecurityEvent($eventType, $severity, $additionalData = [])
    {
        return Http::withHeaders([
            'X-API-Key' => $this->apiKey,
        ])->post($this->baseUrl . '/api/security/log', array_merge([
            'ip_address' => request()->ip(),
            'event_type' => $eventType,
            'severity' => $severity,
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
        ], $additionalData));
    }

    public function logDataChange($tableName, $recordId, $action, $oldValues, $newValues)
    {
        return Http::withHeaders([
            'X-API-Key' => $this->apiKey,
        ])->post($this->baseUrl . '/api/data-changes', [
            'table_name' => $tableName,
            'record_id' => $recordId,
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? null,
        ]);
    }
}