<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Stancl\JobPipeline\JobPipeline;
use Stancl\Tenancy\Events;
use Stancl\Tenancy\Jobs;
use Stancl\Tenancy\Listeners;
use Stancl\Tenancy\Middleware;

class TenancyServiceProvider extends ServiceProvider
{
    public static string $controllerNamespace = '';

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->bootEvents();
        $this->mapTenantRoutes();
        $this->prioritizeTenancyMiddleware();
    }

    protected function bootEvents(): void
    {
        Event::listen(Events\TenantCreated::class, JobPipeline::make([
            Jobs\CreateDatabase::class,
            Jobs\MigrateDatabase::class,
        ])->send(function (Events\TenantCreated $event) {
            return $event->tenant;
        })->shouldBeQueued(false)->toListener());

        Event::listen(Events\TenantDeleted::class, JobPipeline::make([
            Jobs\DeleteDatabase::class,
        ])->send(function (Events\TenantDeleted $event) {
            return $event->tenant;
        })->shouldBeQueued(false)->toListener());

        Event::listen(
            Events\TenancyInitialized::class,
            Listeners\BootstrapTenancy::class
        );

        Event::listen(
            Events\TenancyEnded::class,
            Listeners\RevertToCentralContext::class
        );
    }

    protected function mapTenantRoutes(): void
    {
        $this->app->booted(function (): void {
            if (! file_exists(base_path('routes/tenant.php'))) {
                return;
            }

            Route::namespace(static::$controllerNamespace)
                ->group(base_path('routes/tenant.php'));
        });
    }

    protected function prioritizeTenancyMiddleware(): void
    {
        $tenancyMiddleware = [
            Middleware\PreventAccessFromCentralDomains::class,
            Middleware\InitializeTenancyByDomain::class,
            Middleware\InitializeTenancyBySubdomain::class,
            Middleware\InitializeTenancyByDomainOrSubdomain::class,
            Middleware\InitializeTenancyByPath::class,
            Middleware\InitializeTenancyByRequestData::class,
        ];

        $kernel = $this->app->make(\Illuminate\Contracts\Http\Kernel::class);

        foreach (array_reverse($tenancyMiddleware) as $middleware) {
            $kernel->prependToMiddlewarePriority($middleware);
        }
    }
}
