<?php

namespace App\Filament\Platform\Resources\PlanPrices\Pages;

use App\Filament\Platform\Resources\PlanPrices\PlanPriceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPlanPrice extends EditRecord
{
    protected static string $resource = PlanPriceResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
