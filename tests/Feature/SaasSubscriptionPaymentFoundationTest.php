<?php

namespace Tests\\Feature;

use App\\Models\\SaasSubscriptionPayment;
use App\\Services\\SaasSubscriptionPaymentReviewService;
use Illuminate\\Validation\\ValidationException;
use Tests\\TestCase;

class SaasSubscriptionPaymentFoundationTest extends TestCase
{
    public function test_subscription_payment_uses_central_connection_and_expected_table(): void
    {
        $payment = new SaasSubscriptionPayment();

        $this->assertSame('saas_subscription_payments', $payment->getTable());
        $this->assertSame(config('tenancy.database.central_connection'), $payment->getConnectionName());
        $this->assertTrue($payment->isPending());
    }

    public function test_rejection_requires_a_reason(): void
    {
        $this->expectException(ValidationException::class);

        app(SaasSubscriptionPaymentReviewService::class)->reject(1, 1, '  ');
    }
}
