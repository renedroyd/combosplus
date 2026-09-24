<?php

use App\Http\Controllers\TenantSwitchController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/tenant/switch', [TenantSwitchController::class, 'index'])->name('tenant.switch.index');
    Route::get('/tenant/switch/{tenantId}', [TenantSwitchController::class, 'switch'])->name('tenant.switch');
});
