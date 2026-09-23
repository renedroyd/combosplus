<?php

use App\Http\Middleware\EnsureTenantMembership;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/tenant/health', static function () {
        return response()->json([
            'status' => 'ok',
            'tenant_id' => tenant('id'),
        ]);
    })->name('tenant.health');

    Route::middleware(['auth', EnsureTenantMembership::class])->group(function () {
        Route::get('/tenant/secure', static function () {
            return response()->json([
                'status' => 'ok',
                'tenant_id' => tenant('id'),
                'user_id' => auth()->id(),
            ]);
        })->name('tenant.secure');
    });
});
