<?php

namespace App\Http\Controllers;

use App\Services\Tenancy\TenantAccessService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TenantSwitchController
{
    public function __construct(
        private readonly TenantAccessService $tenantAccess,
    ) {
    }

    public function index(Request $request): Response
    {
        $memberships = $this->tenantAccess->membershipsForUser($request->user());

        return response()->json([
            'data' => $memberships->map(fn ($membership) => [
                'tenant_id' => $membership->tenant_id,
                'role' => $membership->role->value,
                'status' => $membership->status,
                'is_owner' => $membership->is_owner,
            ])->values(),
        ]);
    }

    public function switch(Request $request, string $tenantId): RedirectResponse
    {
        $user = $request->user();
        $tenant = $this->tenantAccess->tenantForUser($user, $tenantId);

        abort_unless($tenant, 403, 'No tienes acceso a este negocio.');

        $domain = $tenant->domains()->orderBy('id')->first();

        abort_unless($domain, 409, 'El negocio no tiene un dominio configurado.');

        // Seller onboarding authenticates the new owner through the web guard.
        // Filament uses the admin guard, so mirror the authenticated identity
        // before crossing into the tenant domain. Both guards share the session
        // cookie, but maintain independent authentication keys.
        if ($user && ! Auth::guard('admin')->check()) {
            Auth::guard('admin')->login($user);
        }

        return redirect()->away(
            $request->getScheme() . '://' . $domain->domain . '/tenant/secure'
        );
    }
}
