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
        $user = $request->user();

        if (! $this->tenantAccess->canAccessCurrentTenant($user)) {
            abort(403, 'No tienes acceso a este negocio.');
        }

        return $next($request);
    }
}
