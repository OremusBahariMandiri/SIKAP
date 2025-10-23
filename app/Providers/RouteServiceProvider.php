<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Exceptions\ThrottleRequestsException;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
        $this->configureRateLimitResponses();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure custom response for rate limit exceptions
     */
    protected function configureRateLimitResponses(): void
    {
        // Register custom response for ThrottleRequestsException
        Response::macro('tooManyRequests', function ($retryAfter = null) {
            return response()->view('errors.429', ['retryAfter' => $retryAfter], 429)
                ->header('Retry-After', $retryAfter ?: 60);
        });

        // Override the default Laravel rate limiting response
        $this->app->bind(
            ThrottleRequestsException::class,
            function ($app, $parameters) {
                $exception = new ThrottleRequestsException(
                    'Too Many Attempts.',
                    null,
                    ['Retry-After' => $parameters['retryAfter'] ?? 60]
                );

                return $exception;
            }
        );
    }

    /**
     * Configure the rate limiters for the application.
     */
    protected function configureRateLimiting(): void
    {
        // Rate Limiter UMUM untuk semua route authenticated
        // 1000 request per menit per user
        RateLimiter::for('general', function (Request $request) {
            return Limit::perMinute(1000)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->view('errors.429', ['retryAfter' => $headers['Retry-After'] ?? 60], 429)
                        ->header('Retry-After', $headers['Retry-After'] ?? 60);
                });
        });

        // Dashboard - akses sering, limit tinggi
        // 200 request per menit
        RateLimiter::for('dashboard', function (Request $request) {
            return Limit::perMinute(200)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->view('errors.429', ['retryAfter' => $headers['Retry-After'] ?? 60], 429)
                        ->header('Retry-After', $headers['Retry-After'] ?? 60);
                });
        });

        // READ operations - akses frequent
        // 300 request per menit
        RateLimiter::for('read', function (Request $request) {
            return Limit::perMinute(300)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->view('errors.429', ['retryAfter' => $headers['Retry-After'] ?? 60], 429)
                        ->header('Retry-After', $headers['Retry-After'] ?? 60);
                });
        });

        // CREATE operations - lebih ketat untuk mencegah spam
        // 30 request per menit
        RateLimiter::for('create', function (Request $request) {
            return [
                Limit::perMinute(30)
                    ->by($request->user()?->id ?: $request->ip())
                    ->response(function (Request $request, array $headers) {
                        return response()->view('errors.429', ['retryAfter' => $headers['Retry-After'] ?? 60], 429)
                            ->header('Retry-After', $headers['Retry-After'] ?? 60);
                    }),
                // Tambahan limit per jam untuk keamanan ekstra
                Limit::perHour(100)
                    ->by($request->user()?->id ?: $request->ip())
                    ->response(function (Request $request, array $headers) {
                        return response()->view('errors.429', ['retryAfter' => $headers['Retry-After'] ?? 60], 429)
                            ->header('Retry-After', $headers['Retry-After'] ?? 60);
                    })
            ];
        });

        // UPDATE operations - sedang
        // 60 request per menit
        RateLimiter::for('update', function (Request $request) {
            return Limit::perMinute(60)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->view('errors.429', ['retryAfter' => $headers['Retry-After'] ?? 60], 429)
                        ->header('Retry-After', $headers['Retry-After'] ?? 60);
                });
        });

        // DELETE operations - sangat ketat
        // 20 request per menit
        RateLimiter::for('delete', function (Request $request) {
            return [
                Limit::perMinute(20)
                    ->by($request->user()?->id ?: $request->ip())
                    ->response(function (Request $request, array $headers) {
                        return response()->view('errors.429', ['retryAfter' => $headers['Retry-After'] ?? 60], 429)
                            ->header('Retry-After', $headers['Retry-After'] ?? 60);
                    }),
                // Limit per jam untuk mencegah penghapusan massal
                Limit::perHour(50)
                    ->by($request->user()?->id ?: $request->ip())
                    ->response(function (Request $request, array $headers) {
                        return response()->view('errors.429', ['retryAfter' => $headers['Retry-After'] ?? 60], 429)
                            ->header('Retry-After', $headers['Retry-After'] ?? 60);
                    })
            ];
        });

        // DOWNLOAD operations - ketat untuk mencegah abuse
        // 50 download per menit, 200 per jam
        RateLimiter::for('download', function (Request $request) {
            return [
                Limit::perMinute(50)
                    ->by($request->user()?->id ?: $request->ip())
                    ->response(function (Request $request, array $headers) {
                        return response()->view('errors.429', ['retryAfter' => $headers['Retry-After'] ?? 60], 429)
                            ->header('Retry-After', $headers['Retry-After'] ?? 60);
                    }),
                Limit::perHour(200)
                    ->by($request->user()?->id ?: $request->ip())
                    ->response(function (Request $request, array $headers) {
                        return response()->view('errors.429', ['retryAfter' => $headers['Retry-After'] ?? 60], 429)
                            ->header('Retry-After', $headers['Retry-After'] ?? 60);
                    })
            ];
        });

        // EXPORT operations - sangat ketat karena resource intensive
        // 10 export per menit, 30 per jam
        RateLimiter::for('export', function (Request $request) {
            return [
                Limit::perMinute(10)
                    ->by($request->user()?->id ?: $request->ip())
                    ->response(function (Request $request, array $headers) {
                        return response()->view('errors.429', ['retryAfter' => $headers['Retry-After'] ?? 60], 429)
                            ->header('Retry-After', $headers['Retry-After'] ?? 60);
                    }),
                Limit::perHour(30)
                    ->by($request->user()?->id ?: $request->ip())
                    ->response(function (Request $request, array $headers) {
                        return response()->view('errors.429', ['retryAfter' => $headers['Retry-After'] ?? 60], 429)
                            ->header('Retry-After', $headers['Retry-After'] ?? 60);
                    })
            ];
        });

        // API endpoints - ketat
        // 100 request per menit
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(100)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'Terlalu banyak permintaan. Silakan coba lagi nanti.',
                        'retry_after' => $headers['Retry-After'] ?? 60
                    ], 429)->header('Retry-After', $headers['Retry-After'] ?? 60);
                });
        });

        // ADMIN operations - ketat untuk operasi sensitif
        // 40 request per menit
        RateLimiter::for('admin', function (Request $request) {
            return Limit::perMinute(40)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->view('errors.429', ['retryAfter' => $headers['Retry-After'] ?? 60], 429)
                        ->header('Retry-After', $headers['Retry-After'] ?? 60);
                });
        });

        // PROFILE & SETTINGS - sedang
        // 50 request per menit
        RateLimiter::for('profile', function (Request $request) {
            return Limit::perMinute(50)
                ->by($request->user()?->id ?: $request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->view('errors.429', ['retryAfter' => $headers['Retry-After'] ?? 60], 429)
                        ->header('Retry-After', $headers['Retry-After'] ?? 60);
                });
        });

        // LOGIN attempts - mencegah brute force tapi tetap user-friendly
        // 10 attempt per menit, 50 per jam
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return [
                Limit::perMinute(10)
                    ->by($email . '|' . $request->ip())
                    ->response(function (Request $request, array $headers) {
                        return response()->view('errors.429', ['retryAfter' => $headers['Retry-After'] ?? 60], 429)
                            ->header('Retry-After', $headers['Retry-After'] ?? 60);
                    }),
                Limit::perHour(50)
                    ->by($email . '|' . $request->ip())
                    ->response(function (Request $request, array $headers) {
                        return response()->view('errors.429', ['retryAfter' => $headers['Retry-After'] ?? 60], 429)
                            ->header('Retry-After', $headers['Retry-After'] ?? 60);
                    })
            ];
        });

        // FORGOT PASSWORD - ketat untuk mencegah abuse
        // 3 request per menit, 10 per jam
        RateLimiter::for('forgot-password', function (Request $request) {
            return [
                Limit::perMinute(3)->by($request->ip())
                    ->response(function (Request $request, array $headers) {
                        return response()->view('errors.429', ['retryAfter' => $headers['Retry-After'] ?? 60], 429)
                            ->header('Retry-After', $headers['Retry-After'] ?? 60);
                    }),
                Limit::perHour(10)->by($request->ip())
                    ->response(function (Request $request, array $headers) {
                        return response()->view('errors.429', ['retryAfter' => $headers['Retry-After'] ?? 60], 429)
                            ->header('Retry-After', $headers['Retry-After'] ?? 60);
                    })
            ];
        });

        // REGISTRATION - ketat untuk mencegah spam account
        // 5 registrasi per jam per IP
        RateLimiter::for('register', function (Request $request) {
            return [
                Limit::perMinute(2)->by($request->ip())
                    ->response(function (Request $request, array $headers) {
                        return response()->view('errors.429', ['retryAfter' => $headers['Retry-After'] ?? 60], 429)
                            ->header('Retry-After', $headers['Retry-After'] ?? 60);
                    }),
                Limit::perHour(5)->by($request->ip())
                    ->response(function (Request $request, array $headers) {
                        return response()->view('errors.429', ['retryAfter' => $headers['Retry-After'] ?? 60], 429)
                            ->header('Retry-After', $headers['Retry-After'] ?? 60);
                    })
            ];
        });

        // Global API rate limit (jika ada public API)
        // 60 request per menit
        RateLimiter::for('global-api', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->json([
                        'message' => 'Terlalu banyak permintaan. Silakan coba lagi nanti.',
                        'retry_after' => $headers['Retry-After'] ?? 60
                    ], 429)->header('Retry-After', $headers['Retry-After'] ?? 60);
                });
        });

        // Rate limiter khusus untuk mencegah DDoS
        // Sangat ketat untuk IP yang tidak terautentikasi
        RateLimiter::for('guest', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip())
                ->response(function (Request $request, array $headers) {
                    return response()->view('errors.429', ['retryAfter' => $headers['Retry-After'] ?? 60], 429)
                        ->header('Retry-After', $headers['Retry-After'] ?? 60);
                });
        });
    }
}