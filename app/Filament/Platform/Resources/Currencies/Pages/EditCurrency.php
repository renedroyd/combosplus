<?php

namespace App\Filament\Platform\Resources\Currencies\Pages;

use App\Filament\Platform\Resources\Currencies\CurrencyResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCurrency extends EditRecord
{
    protected static string $resource = CurrencyResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
