<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class TenantRouteIntegrityTest extends TestCase
{
    public function test_tenant_routes_reference_existing_controller_methods(): void
    {
        $routes = collect(Route::getRoutes())
            ->filter(fn ($route) => str_starts_with($route->getName() ?? '', 'remesas.'));

        $this->assertNotEmpty($routes);

        foreach ($routes as $route) {
            $action = $route->getActionName();

            if ($action === 'Closure') {
                continue;
            }

            [$controller, $method] = str_contains($action, '@')
                ? explode('@', $action, 2)
                : [null, null];

            $this->assertNotNull($controller, "La ruta {$route->getName()} no tiene un controlador explícito.");
            $this->assertTrue(
                method_exists($controller, $method),
                "La ruta {$route->getName()} referencia {$action}, pero el método no existe."
            );
        }
    }
}
