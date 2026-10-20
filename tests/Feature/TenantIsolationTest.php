<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tenant;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    public function test_operational_data_is_isolated_between_tenants(): void
    {
        if (config('database.default') !== 'mysql') {
            $this->markTestSkipped('Tenant isolation requires the MySQL CI service.');
        }

        $tenantA = Tenant::create([
            'id' => 'isolation-a-'.bin2hex(random_bytes(5)),
        ]);

        $tenantB = Tenant::create([
            'id' => 'isolation-b-'.bin2hex(random_bytes(5)),
        ]);

        try {
            $tenantA->run(function (): void {
                Product::create([
                    'name' => 'Tenant A Product',
                    'slug' => 'tenant-a-product',
                    'description' => 'Tenant A isolated product',
                    'price' => 10,
                    'quantity' => 5,
                    'sku' => 'TENANT-A-001',
                ]);
            });

            $tenantB->run(function (): void {
                Product::create([
                    'name' => 'Tenant B Product',
                    'slug' => 'tenant-b-product',
                    'description' => 'Tenant B isolated product',
                    'price' => 20,
                    'quantity' => 7,
                    'sku' => 'TENANT-B-001',
                ]);
            });

            $tenantA->run(function (): void {
                $this->assertTrue(Product::where('sku', 'TENANT-A-001')->exists());
                $this->assertFalse(Product::where('sku', 'TENANT-B-001')->exists());
            });

            $tenantB->run(function (): void {
                $this->assertTrue(Product::where('sku', 'TENANT-B-001')->exists());
                $this->assertFalse(Product::where('sku', 'TENANT-A-001')->exists());
            });
        } finally {
            $tenantA->delete();
            $tenantB->delete();
        }
    }

    public function test_category_hierarchy_relations_and_cycle_detection_work_in_tenant_database(): void
    {
        if (config('database.default') !== 'mysql') {
            $this->markTestSkipped('Tenant hierarchy validation requires the MySQL CI service.');
        }

        $tenant = Tenant::create([
            'id' => 'category-hierarchy-'.bin2hex(random_bytes(5)),
        ]);

        try {
            $tenant->run(function (): void {
                $parent = Category::create([
                    'name' => 'Parent category',
                    'slug' => 'parent-category',
                ]);

                $child = Category::create([
                    'name' => 'Child category',
                    'slug' => 'child-category',
                    'parent_id' => $parent->id,
                ]);

                $this->assertSame($parent->id, $child->parent()->firstOrFail()->id);
                $this->assertTrue($parent->children()->whereKey($child->id)->exists());
                $this->assertTrue(Category::wouldCreateParentCycle($parent->id, $child->id));
                $this->assertFalse(Category::wouldCreateParentCycle($child->id, $parent->id));
                $this->assertTrue(Category::wouldCreateParentCycle($child->id, $child->id));
            });
        } finally {
            $tenant->delete();
        }
    }
}
