<?php

namespace Tests\Feature;

use App\Models\Remesa;
use App\Models\Tenant;
use App\Services\RemittancePaymentService;
use DomainException;
use Mockery;
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

    public function test_payment_submission_transitions_pending_remittance_to_processing_without_marking_it_paid(): void
    {
        $remesa = Mockery::mock(Remesa::class)->makePartial();
        $remesa->setAttribute('estado', 'pendiente');
        $remesa->shouldReceive('save')->once()->andReturnTrue();
        $remesa->shouldReceive('refresh')->once()->andReturnSelf();

        $result = app(RemittancePaymentService::class)->submit($remesa);

        $this->assertSame('procesando', $result->getAttribute('estado'));
        $this->assertNull($result->getAttribute('pagado_en'));
    }

    public function test_payment_submission_rejects_a_remittance_that_is_not_pending(): void
    {
        $remesa = new Remesa(['estado' => 'procesando']);

        $this->expectException(DomainException::class);

        app(RemittancePaymentService::class)->submit($remesa);
    }
}
