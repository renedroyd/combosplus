<?php

namespace App\Http\Middleware;

use App\Services\SaasTenantAccessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantCommercialAccess
{
    public function __construct(
        private readonly SaasTenantAccessService $access,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $tenantId = tenant()?->getTenantKey();

        if (! is_string($tenantId) || $this->access->canUseCommercialFeatures($tenantId)) {
            return $next($request);
        }

        // Read-only pages, authentication, contact and renewal remain available.
        if ($request->isMethodSafe() || $this->isAllowedNonCommercialRoute($request)) {
            return $next($request);
        }

        if ($request->is('livewire/update')) {
            if (! $this->containsBlockedLivewireAction($request)) {
                return $next($request);
            }

            abort(402, $this->restrictedMessage());
        }

        abort(402, $this->restrictedMessage());
    }

    private function isAllowedNonCommercialRoute(Request $request): bool
    {
        return $request->is('login')
            || $request->is('logout')
            || $request->is('register')
            || $request->is('password/*')
            || $request->is('contacto')
            || $request->is('tenant/health')
            || $request->is('livewire/upload-file');
    }

    private function containsBlockedLivewireAction(Request $request): bool
    {
        $blocked = [
            'create', 'save', 'delete', 'deleteany', 'callmountedaction',
            'calltableaction', 'callmountedtableaction', 'submit', 'process',
            'destroy', 'store', 'execute', 'bulkdelete', 'mountaction',
            'publish', 'unpublish', 'cancel', 'confirm', 'approve', 'reject',
            'toggle', 'updatestatus', 'checkout', 'pay',
        ];

        foreach ((array) $request->input('components', []) as $component) {
            $snapshot = json_decode((string) ($component['snapshot'] ?? ''), true);
            $name = strtolower((string) data_get($snapshot, 'memo.name', ''));
            $isRenewalComponent = str_contains($name, 'subscription-payment');

            foreach ((array) ($component['calls'] ?? []) as $call) {
                $method = strtolower((string) ($call['method'] ?? ''));

                if (in_array($method, $blocked, true) && ! $isRenewalComponent) {
                    return true;
                }
            }
        }

        return false;
    }

    private function restrictedMessage(): string
    {
        return 'La suscripción ha vencido y terminó el período de gracia. Las operaciones comerciales están bloqueadas; puedes consultar el historial y renovar la suscripción.';
    }
}
