<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SecurityManagement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'security:manage
                            {action : Action to perform (list-banned|unban|clear-logs|stats)}
                            {--ip= : IP address for unban action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage security settings and banned IPs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');

        switch ($action) {
            case 'list-banned':
                $this->listBannedIPs();
                break;

            case 'unban':
                $this->unbanIP();
                break;

            case 'clear-logs':
                $this->clearLogs();
                break;

            case 'stats':
                $this->showStats();
                break;

            default:
                $this->error('Invalid action. Use: list-banned, unban, clear-logs, or stats');
        }

        return 0;
    }

    /**
     * List all banned IPs
     */
    protected function listBannedIPs()
    {
        $banLog = Cache::get('ban_log', []);

        if (empty($banLog)) {
            $this->info('No banned IPs found.');
            return;
        }

        $this->info('Banned IPs:');
        $this->table(
            ['IP Address', 'Banned At', 'Reason'],
            array_map(function ($log) {
                return [
                    $log['ip'],
                    $log['banned_at'],
                    $log['reason']
                ];
            }, $banLog)
        );

        // Cek IP yang masih aktif di-ban
        $activeBans = [];
        foreach ($banLog as $log) {
            if (Cache::has("banned_ip:{$log['ip']}")) {
                $activeBans[] = $log['ip'];
            }
        }

        if (!empty($activeBans)) {
            $this->info("\nCurrently active bans: " . implode(', ', $activeBans));
        }
    }

    /**
     * Unban specific IP
     */
    protected function unbanIP()
    {
        $ip = $this->option('ip');

        if (!$ip) {
            $this->error('Please specify an IP address with --ip option');
            return;
        }

        if (!Cache::has("banned_ip:{$ip}")) {
            $this->warn("IP {$ip} is not currently banned.");
            return;
        }

        Cache::forget("banned_ip:{$ip}");
        $this->info("IP {$ip} has been unbanned successfully.");
    }

    /**
     * Clear security logs
     */
    protected function clearLogs()
    {
        if (!$this->confirm('Are you sure you want to clear all security logs?')) {
            $this->info('Operation cancelled.');
            return;
        }

        Cache::forget('ban_log');

        // Clear semua request tracking
        $keys = Cache::get('tracked_ips', []);
        foreach ($keys as $ip) {
            Cache::forget("request_tracking:{$ip}");
        }
        Cache::forget('tracked_ips');

        $this->info('Security logs cleared successfully.');
    }

    /**
     * Show security statistics
     */
    protected function showStats()
    {
        $banLog = Cache::get('ban_log', []);
        $trackedIps = Cache::get('tracked_ips', []);

        $activeBans = 0;
        foreach ($banLog as $log) {
            if (Cache::has("banned_ip:{$log['ip']}")) {
                $activeBans++;
            }
        }

        $this->info('Security Statistics:');
        $this->line('');
        $this->line("Total Ban Records: " . count($banLog));
        $this->line("Active Bans: {$activeBans}");
        $this->line("Tracked IPs: " . count($trackedIps));
        $this->line('');

        // Show recent bans (last 10)
        if (!empty($banLog)) {
            $recentBans = array_slice($banLog, -10);
            $this->info('Recent Bans (Last 10):');
            $this->table(
                ['IP Address', 'Banned At'],
                array_map(function ($log) {
                    return [$log['ip'], $log['banned_at']];
                }, $recentBans)
            );
        }
    }
}