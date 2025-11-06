<?php
// file: app/Services/ActivityHubClient.php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        try {
            return Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->post($this->baseUrl . '/api/activities', array_merge($data, [
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]));
        } catch (\Throwable $e) {
            Log::error('Failed to log activity to Activity Hub', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            return false;
        }
    }

    public function trackSession($userId, $userEmail, $userName, $sessionId)
    {
        try {
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
        } catch (\Throwable $e) {
            Log::error('Failed to track session to Activity Hub', [
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'session_id' => $sessionId
            ]);
            return false;
        }
    }

    public function logoutSession($sessionId)
    {
        try {
            return Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->post($this->baseUrl . '/api/sessions/logout', [
                'session_id' => $sessionId,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed to logout session in Activity Hub', [
                'error' => $e->getMessage(),
                'session_id' => $sessionId
            ]);
            return false;
        }
    }

    public function logSecurityEvent($eventType, $severity, $additionalData = [])
    {
        try {
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
        } catch (\Throwable $e) {
            Log::error('Failed to log security event to Activity Hub', [
                'error' => $e->getMessage(),
                'event_type' => $eventType,
                'severity' => $severity,
                'data' => $additionalData
            ]);
            return false;
        }
    }

    public function logDataChange($tableName, $recordId, $action, $oldValues, $newValues)
    {
        try {
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
        } catch (\Throwable $e) {
            Log::error('Failed to log data change to Activity Hub', [
                'error' => $e->getMessage(),
                'table' => $tableName,
                'record_id' => $recordId,
                'action' => $action
            ]);
            return false;
        }
    }

    public function checkIpStatus($ip)
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->get($this->baseUrl . "/api/ip/check/{$ip}");

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('Failed to check IP status in Activity Hub', [
                'error' => $e->getMessage(),
                'ip' => $ip
            ]);
            return null;
        }
    }

    public function getDashboardStats($days = 30)
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
            ])->get($this->baseUrl . "/api/statistics/dashboard", [
                'days' => $days
            ]);

            return $response->json();
        } catch (\Throwable $e) {
            Log::error('Failed to get dashboard stats from Activity Hub', [
                'error' => $e->getMessage(),
                'days' => $days
            ]);
            return null;
        }
    }
}