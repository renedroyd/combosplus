<?php

namespace App\\Filament\\Platform\\Resources\\SubscriptionPayments\\Pages;

use App\\Filament\\Platform\\Resources\\SubscriptionPayments\\SubscriptionPaymentResource;
use Filament\\Resources\\Pages\\CreateRecord;

class CreateSubscriptionPayment extends CreateRecord
{
    protected static string $resource = SubscriptionPaymentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $price = \\App\\Models\\SaasPlanPrice::query()->with(['plan', 'currency'])->findOrFail($data['plan_price_id']);
        $data['plan_name'] = $price->plan->name;
        $data['currency_code'] = $price->currency->code;
        $data['billing_interval'] = $price->billing_interval;
        $data['amount'] = $data['amount'] ?? $price->amount;
        $data['status'] = \\App\\Models\\SaasSubscriptionPayment::STATUS_PENDING;
        $data['idempotency_key'] = (string) \\Illuminate\\Support\\Str::uuid();

        return $data;
    }
}
