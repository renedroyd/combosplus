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
        $tenant = Tenant::create([
            'id' => 'commercial-middleware-'.bin2hex(random_bytes(5)),
        ]);

        $this->mock(SaasTenantAccessService::class, function ($mock) use ($tenant): void {
            $mock->shouldReceive('canUseCommercialFeatures')
                ->with((string) $tenant->getTenantKey())
                ->andReturnFalse();
        });

        try {
            $tenant->run(function (): void {
                $middleware = app(EnsureTenantCommercialAccess::class);

                $readResponse = $middleware->handle(
                    Request::create('/cart', 'GET'),
                    fn () => response('readable'),
                );

                $this->assertSame(200, $readResponse->getStatusCode());

                try {
                    $middleware->handle(
                        Request::create('/cart/add/1', 'POST'),
                        fn () => response('should be blocked'),
                    );

                    $this->fail('Commercial mutations must be blocked for restricted tenants.');
                } catch (HttpException $exception) {
                    $this->assertSame(402, $exception->getStatusCode());
                }
            });
        } finally {
            $tenant->delete();
        }
    }
}
