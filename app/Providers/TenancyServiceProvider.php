<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Stancl\JobPipeline\JobPipeline;
use Stancl\Tenancy\Events\TenantCreated;
use Stancl\Tenancy\Jobs\CreateDatabase;
use Stancl\Tenancy\Jobs\MigrateDatabase;

class TenancyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Event::listen(TenantCreated::class, JobPipeline::make([
            CreateDatabase::class,
            MigrateDatabase::class,
        ])->send(function (TenantCreated $event) {
            return $event->tenant;
        })->toListener());
    }
}
