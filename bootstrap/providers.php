<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Em produção, força todas as URLs (inclusive assets do Vite) a usarem HTTPS
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
