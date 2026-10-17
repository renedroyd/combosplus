<?php

namespace App\Filament\Platform\Resources\Currencies\Pages;

use App\Filament\Platform\Resources\Currencies\CurrencieResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCurrencies extends ListRecords
{
    protected static string $resource = CurrencieResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
