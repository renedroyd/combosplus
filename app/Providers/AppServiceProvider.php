<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
            // Forzar HTTPS si no estamos en entorno local (o siempre que se use ngrok)
            if (config('app.env') !== 'local' || request()->isSecure()) {
                URL::forceScheme('https');
            }

            if (request()->server('HTTP_X_FORWARDED_PROTO') === 'https') {
                URL::forceScheme('https');
            }
    }
}
