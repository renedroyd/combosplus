<?php

namespace Tests\Feature;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_tenant_application_returns_a_successful_response(): void
    {
        $tenant = Tenant::create(['id' => 'example-tenant']);

        try {
            $domain = $tenant->domains()->create(['domain' => 'example.test']);
            $response = $this->get('http://' . $domain->domain . '/tenant/health');

            $response->assertOk()->assertJson([
                'status' => 'ok',
                'tenant_id' => $tenant->id,
            ]);
        } finally {
            tenancy()->end();
            $tenant->delete();
        }
    }
}
