<?php

namespace App\Http\Middleware;

use App\Services\Tenancy\TenantAccessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantMembership
{
    public function __construct(
        private readonly TenantAccessService $tenantAccess,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        // Filament authenticates sellers through the admin guard, while the tenant
        // storefront uses the default web guard. Prefer the panel guard when it
        // has an authenticated user so the same membership check works in both
        // contexts without weakening tenant isolation.
        $user = $request->user('admin') ?? $request->user();

        if (! $this->tenantAccess->canAccessCurrentTenant($user)) {
            abort(403, 'No tienes acceso a este negocio.');
        }

        return $next($request);
    }
}
