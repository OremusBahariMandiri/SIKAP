<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class SecurityErrorController extends Controller
{
    /**
     * Display a blocked error page.
     */
    public function blocked(Request $request, int $retryAfter = 3600)
    {
        $errorCode = Str::random(8);

        Log::warning('Security block page displayed', [
            'ip' => $request->ip(),
            'error_code' => $errorCode,
            'user_agent' => $request->userAgent(),
            'referer' => $request->header('referer')
        ]);

        return response()->view('errors.blocked', [
            'retryAfter' => $retryAfter,
            'errorCode' => $errorCode
        ], 403);
    }

    /**
     * Display a generic security error page.
     */
    public function securityError(Request $request, string $title = null, string $message = null, array $details = [])
    {
        $errorCode = Str::random(8);

        Log::warning('Security error page displayed', [
            'ip' => $request->ip(),
            'error_code' => $errorCode,
            'user_agent' => $request->userAgent(),
            'referer' => $request->header('referer')
        ]);

        return response()->view('errors.security', [
            'title' => $title,
            'message' => $message,
            'details' => $details,
            'errorCode' => $errorCode
        ], 403);
    }

    /**
     * Display unauthorized access error.
     */
    public function unauthorized(Request $request)
    {
        return $this->securityError(
            $request,
            'Akses Tidak Diizinkan',
            'Anda tidak memiliki izin untuk mengakses halaman ini.',
            [
                'Pastikan Anda memiliki otorisasi yang tepat untuk mengakses sumber daya ini.',
                'Jika Anda yakin ini adalah kesalahan, hubungi administrator sistem.'
            ]
        );
    }

    /**
     * Display IP whitelist error.
     */
    public function ipNotWhitelisted(Request $request)
    {
        return $this->securityError(
            $request,
            'Akses IP Tidak Diizinkan',
            'Alamat IP Anda tidak berada dalam daftar yang diizinkan untuk mengakses sumber daya ini.',
            [
                'Sumber daya ini hanya dapat diakses dari jaringan tertentu.',
                'Hubungi administrator sistem untuk mendapatkan akses.'
            ]
        );
    }
}