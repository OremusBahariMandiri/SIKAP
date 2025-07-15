<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Create custom Blade directives for permission checks
        Blade::directive('hasAccess', function ($expression) {
            return "<?php if(auth()->check() && auth()->user()->hasAccess($expression)): ?>";
        });

        Blade::directive('endhasAccess', function () {
            return "<?php endif; ?>";
        });
    }
}