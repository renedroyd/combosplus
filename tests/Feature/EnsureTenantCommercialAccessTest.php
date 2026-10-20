<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureTenantCommercialAccess;
use App\Models\Tenant;
use App\Services\SaasTenantAccessService;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class EnsureTenantCommercialAccessTest extends TestCase
{
    public function test_restricted_tenant_can_read_but_cannot_submit_commercial_mutations(): void
    {
        $tenant = $this->restrictedTenant();

        try {
            $tenant->run(function (): void {
                $middleware = app(EnsureTenantCommercialAccess::class);

                $readResponse = $middleware->handle(
                    Request::create('/cart', 'GET'),
                    fn () => response('readable'),
                );

                $this->assertSame(200, $readResponse->getStatusCode());

                $this->assertBlocked(
                    fn () => $middleware->handle(
                        Request::create('/cart/add/1', 'POST'),
                        fn () => response('should be blocked'),
                    ),
                );
            });
        } finally {
            $tenant->delete();
        }
    }

    public function test_restricted_tenant_can_still_post_to_login(): void
    {
        $tenant = $this->restrictedTenant();

        try {
            $tenant->run(function (): void {
                $response = app(EnsureTenantCommercialAccess::class)->handle(
                    Request::create('/login', 'POST'),
                    fn () => response('login allowed'),
                );

                $this->assertSame(200, $response->getStatusCode());
            });
        } finally {
            $tenant->delete();
        }
    }

    public function test_restricted_tenant_cannot_execute_blocked_livewire_actions(): void
    {
        $tenant = $this->restrictedTenant();

        try {
            $tenant->run(function (): void {
                $request = Request::create('/livewire/update', 'POST', [
                    'components' => [[
                        'snapshot' => json_encode([
                            'memo' => ['name' => 'app-filament-resources-products-create-product'],
                        ]),
                        'calls' => [['method' => 'callMountedAction', 'params' => []]],
                    ]],
                ]);

                $this->assertBlocked(
                    fn () => app(EnsureTenantCommercialAccess::class)->handle(
                        $request,
                        fn () => response('should be blocked'),
                    ),
                );
            });
        } finally {
            $tenant->delete();
        }
    }

    private function restrictedTenant(): Tenant
    {
        $tenant = Tenant::create([
            'id' => 'commercial-middleware-'.bin2hex(random_bytes(5)),
        ]);

        $this->mock(SaasTenantAccessService::class, function ($mock) use ($tenant): void {
            $mock->shouldReceive('canUseCommercialFeatures')
                ->with((string) $tenant->getTenantKey())
                ->andReturnFalse();
        });

        return $tenant;
    }

    private function assertBlocked(callable $callback): void
    {
        try {
            $callback();
            $this->fail('The restricted commercial operation should be blocked.');
        } catch (HttpException $exception) {
            $this->assertSame(402, $exception->getStatusCode());
        }
    }
}
