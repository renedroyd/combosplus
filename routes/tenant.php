<?php

use Illuminate\Support\Facades\Route;

Route::middleware([
    'web',
])->group(function () {
    Route::get('/tenant/health', static function () {
        return response()->json([
            'status' => 'ok',
            'tenant_id' => tenant('id'),
        ]);
    })->name('tenant.health');
});
