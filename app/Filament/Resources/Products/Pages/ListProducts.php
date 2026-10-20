<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Services\SaasTenantAccessService;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        $tenantId = tenant()?->getTenantKey();

        if (! is_string($tenantId)
            || ! app(SaasTenantAccessService::class)->canCreateProduct($tenantId)) {
            return [];
        }

        return [
            CreateAction::make(),
        ];
    }
}
