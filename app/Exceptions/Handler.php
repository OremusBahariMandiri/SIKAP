<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Throwable $e)
    {
        // API/AJAX responses use JSON format
        if ($request->expectsJson() || $request->is('api/*')) {
            return $this->renderJsonException($request, $e);
        }

        // Handle specific HTTP exceptions with custom error pages
        if ($e instanceof TooManyRequestsHttpException) {
            return $this->renderTooManyRequests($request, $e);
        }

        if ($e instanceof AccessDeniedHttpException) {
            return $this->renderAccessDenied($request, $e);
        }

        if ($e instanceof NotFoundHttpException) {
            return $this->renderNotFound($request, $e);
        }

        return parent::render($request, $e);
    }

    /**
     * Render JSON exceptions for API/AJAX requests
     */
    protected function renderJsonException(Request $request, Throwable $e)
    {
        $statusCode = $this->getStatusCode($e);

        $response = [
            'error' => true,
            'message' => $this->getExceptionMessage($e, $statusCode),
        ];

        if (config('app.debug')) {
            $response['exception'] = get_class($e);
            $response['trace'] = explode("\n", $e->getTraceAsString());
        }

        return response()->json($response, $statusCode);
    }

    /**
     * Render too many requests error (429)
     */
    protected function renderTooManyRequests(Request $request, TooManyRequestsHttpException $e)
    {
        $retryAfter = $e->getHeaders()['Retry-After'] ?? 60;

        $errorCode = Str::random(8);
        Log::warning('Rate limit exceeded', [
            'ip' => $request->ip(),
            'url' => $request->fullUrl(),
            'error_code' => $errorCode,
            'retry_after' => $retryAfter
        ]);

        return response()->view('errors.429', [
            'retryAfter' => $retryAfter,
            'errorCode' => $errorCode
        ], 429);
    }

    /**
     * Render access denied error (403)
     */
    protected function renderAccessDenied(Request $request, AccessDeniedHttpException $e)
    {
        $errorCode = Str::random(8);
        Log::warning('Access denied', [
            'ip' => $request->ip(),
            'url' => $request->fullUrl(),
            'error_code' => $errorCode
        ]);

        return response()->view('errors.security', [
            'title' => 'Akses Tidak Diizinkan',
            'message' => 'Anda tidak memiliki izin untuk mengakses halaman ini.',
            'details' => [
                'Pastikan Anda memiliki otorisasi yang tepat untuk mengakses sumber daya ini.',
                'Jika Anda yakin ini adalah kesalahan, hubungi administrator sistem.'
            ],
            'errorCode' => $errorCode
        ], 403);
    }

    /**
     * Render not found error (404)
     */
    protected function renderNotFound(Request $request, NotFoundHttpException $e)
    {
        return response()->view('errors.404', [], 404);
    }

    /**
     * Get the status code from the exception
     */
    protected function getStatusCode(Throwable $e): int
    {
        if ($e instanceof HttpException) {
            return $e->getStatusCode();
        }

        return 500;
    }

    /**
     * Get a user-friendly message for the exception
     */
    protected function getExceptionMessage(Throwable $e, int $statusCode): string
    {
        if (config('app.debug')) {
            return $e->getMessage();
        }

        return match ($statusCode) {
            401 => 'Anda harus login untuk mengakses sumber daya ini.',
            403 => 'Anda tidak memiliki izin untuk mengakses sumber daya ini.',
            404 => 'Sumber daya yang diminta tidak ditemukan.',
            429 => 'Terlalu banyak permintaan. Silakan coba lagi nanti.',
            500 => 'Terjadi kesalahan internal pada server.',
            503 => 'Layanan tidak tersedia saat ini. Silakan coba lagi nanti.',
            default => 'Terjadi kesalahan yang tidak terduga.',
        };
    }
}
