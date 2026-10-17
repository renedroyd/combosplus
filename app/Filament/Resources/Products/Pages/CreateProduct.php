<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Services\SaasTenantAccessService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

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
