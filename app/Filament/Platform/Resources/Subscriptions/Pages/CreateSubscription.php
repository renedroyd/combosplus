<?php

namespace App\Filament\Platform\Resources\Subscriptions\Pages;

use App\Filament\Platform\Resources\Subscriptions\SubscriptionResource;
use App\Services\SaasSubscriptionLifecycleService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateSubscription extends CreateRecord
{
    protected static string $resource = SubscriptionResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(SaasSubscriptionLifecycleService::class)->createManually(
            (string) $data['tenant_id'],
            (int) $data['plan_price_id'],
            auth('platform')->user(),
            (string) $data['creation_reason'],
        );
    }
}
