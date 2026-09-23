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
        $tenant->domains()->create(['domain' => 'example.test']);

        try {
            $response = $this->withServerVariables([
                'HTTP_HOST' => 'example.test',
            ])->get('/');

            $response->assertOk();
        } finally {
            $tenant->delete();
        }
    }
}
