<?php

namespace Tests\Feature;

use App\Models\Remesa;
use App\Models\Tenant;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TenantRemittanceIdentityTest extends TestCase
{
    public function test_remittances_support_platform_identity_without_a_cross_database_foreign_key(): void
    {
        $tenant = Tenant::create(['id' => 'remittance-identity']);

        try {
            $tenant->run(function (): void {
                $this->assertTrue(Schema::hasColumn('remesas', 'platform_user_id'));
                $this->assertTrue(Schema::hasColumn('remesas', 'user_id'));

                $indexes = collect(Schema::getIndexes('remesas'));

                $this->assertTrue(
                    $indexes->contains(fn (array $index): bool =>
                        ($index['columns'] ?? []) === ['platform_user_id']
                        && ($index['unique'] ?? false) === false
                    )
                );

                $remesa = new Remesa(['platform_user_id' => 42]);
                $this->assertSame(42, $remesa->getAttribute('platform_user_id'));
                $this->assertSame('platform_user_id', $remesa->user()->getForeignKeyName());
            });
        } finally {
            $tenant->delete();
        }
    }

    public function test_payment_submission_does_not_mark_a_remittance_as_paid(): void
    {
        $source = file_get_contents(app_path('Http/Controllers/RemesaController.php'));

        $this->assertStringContainsString("'estado' => 'procesando'", $source);
        $this->assertStringNotContainsString("'estado' => 'pagado'", $source);
        $this->assertStringContainsString("'pagado_en' => null", $source);
    }
}
