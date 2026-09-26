<?php

use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\TenantSwitchController;
use Illuminate\Support\Facades\Route;

foreach ((array) config('tenancy.central_domains', []) as $centralDomain) {
    Route::domain($centralDomain)->group(function () {
        Route::get('/', [MarketplaceController::class, 'home'])->name('marketplace.home');
        Route::get('/tiendas', [MarketplaceController::class, 'stores'])->name('marketplace.stores');
        Route::get('/productos', [MarketplaceController::class, 'products'])->name('marketplace.products');
        Route::get('/registrar-tienda', [MarketplaceController::class, 'register'])->name('marketplace.register');
        Route::post('/registrar-tienda', [MarketplaceController::class, 'registerStore'])->name('marketplace.register.store');
        Route::get('/tiendas/{tenant:slug}', [MarketplaceController::class, 'store'])->name('marketplace.store');
        Route::get('/tiendas/{tenant:slug}/productos/{product}/comprar', [MarketplaceController::class, 'buy'])
            ->name('marketplace.product.buy');
        Route::get('/tiendas/{tenant:slug}/productos/{product}', [MarketplaceController::class, 'product'])
            ->name('marketplace.product');

        Route::middleware(['auth'])->group(function () {
            Route::get('/tenant/switch', [TenantSwitchController::class, 'index'])->name('tenant.switch.index');
            Route::get('/tenant/switch/{tenantId}', [TenantSwitchController::class, 'switch'])->name('tenant.switch');
        });
    });
}
