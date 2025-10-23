<?php

return [

    /*
    |--------------------------------------------------------------------------
    | IP Whitelist
    |--------------------------------------------------------------------------
    |
    | Daftar IP yang diizinkan untuk mengakses endpoint sensitif.
    | Bisa menggunakan IP tunggal atau CIDR notation untuk range.
    |
    */

    'ip_whitelist' => [
        // Contoh:
        // '192.168.1.1',
        // '10.0.0.0/8',
        // '172.16.0.0/12',
    ],

    /*
    |--------------------------------------------------------------------------
    | IP Blacklist
    |--------------------------------------------------------------------------
    |
    | Daftar IP yang diblokir permanen dari aplikasi.
    |
    */

    'ip_blacklist' => [
        // Tambahkan IP yang ingin diblokir secara permanen
    ],

    /*
    |--------------------------------------------------------------------------
    | DDoS Protection Settings
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk DDoS protection middleware
    |
    */

    'ddos' => [
        // Maksimal request per menit dari satu IP
        'max_requests_per_minute' => env('DDOS_MAX_REQUESTS', 200),

        // Durasi ban dalam menit
        'ban_duration' => env('DDOS_BAN_DURATION', 60),

        // Enable/disable DDoS protection
        'enabled' => env('DDOS_PROTECTION_ENABLED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Headers
    |--------------------------------------------------------------------------
    |
    | Headers yang akan ditambahkan ke setiap response untuk keamanan
    |
    */

    'headers' => [
        'X-Frame-Options' => 'SAMEORIGIN',
        'X-Content-Type-Options' => 'nosniff',
        'X-XSS-Protection' => '1; mode=block',
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
        'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()',
    ],

    /*
    |--------------------------------------------------------------------------
    | HTTPS Settings
    |--------------------------------------------------------------------------
    |
    | Pengaturan HTTPS dan Strict-Transport-Security
    |
    */

    'https' => [
        'force_https' => env('FORCE_HTTPS', false),
        'hsts_max_age' => env('HSTS_MAX_AGE', 31536000), // 1 tahun
    ],

    /*
    |--------------------------------------------------------------------------
    | Suspicious User Agents
    |--------------------------------------------------------------------------
    |
    | Pattern user agent yang dianggap mencurigakan
    |
    */

    'suspicious_user_agents' => [
        'bot', 'crawl', 'spider', 'scrape', 'harvest',
        'curl', 'wget', 'python-requests', 'java',
        'nikto', 'scanner', 'nmap', 'masscan',
        'sqlmap', 'havij', 'acunetix', 'metasploit'
    ],

    /*
    |--------------------------------------------------------------------------
    | Failed Login Attempts
    |--------------------------------------------------------------------------
    |
    | Pengaturan untuk tracking failed login attempts
    |
    */

    'failed_login' => [
        // Maksimal percobaan login gagal sebelum temporary ban
        'max_attempts' => env('MAX_LOGIN_ATTEMPTS', 5),

        // Durasi temporary ban dalam menit
        'lockout_duration' => env('LOGIN_LOCKOUT_DURATION', 15),

        // Enable/disable failed login tracking
        'enabled' => env('FAILED_LOGIN_TRACKING', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | CORS Settings
    |--------------------------------------------------------------------------
    |
    | Pengaturan Cross-Origin Resource Sharing
    |
    */

    'cors' => [
        'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', '*')),
        'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
        'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],
    ],

];