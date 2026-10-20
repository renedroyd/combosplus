<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Services\SaasTenantAccessService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    public function mount(): void
    {
        $tenantId = tenant()?->getTenantKey();

        abort_unless(is_string($tenantId), 404);

        // Return the intended subscription restriction (402), rather than the
        // generic 403 produced when a Resource conflates billing with ability.
        app(SaasTenantAccessService::class)->assertCanUseCommercialFeatures($tenantId);

        parent::mount();
    }

    protected function handleRecordCreation(array $data): Model
    {
        $tenantId = (string) tenant()->getTenantKey();
        $access = app(SaasTenantAccessService::class);

        return $access->withTenantLock($tenantId, function () use ($access, $tenantId, $data): Model {
            $access->assertCanCreateProduct($tenantId);

            return parent::handleRecordCreation($data);
        });
    }
}
