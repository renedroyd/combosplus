<?php

namespace App\Filament\Platform\Resources\PlanEntitlements\Pages;

use App\Filament\Platform\Resources\PlanEntitlements\PlanEntitlementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPlanEntitlements extends ListRecords
{
    protected static string $resource = PlanEntitlementResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
