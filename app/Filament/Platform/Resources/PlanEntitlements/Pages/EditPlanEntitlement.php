<?php

namespace App\Filament\Platform\Resources\PlanEntitlements\Pages;

use App\Filament\Platform\Resources\PlanEntitlements\PlanEntitlementResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPlanEntitlement extends EditRecord
{
    protected static string $resource = PlanEntitlementResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
