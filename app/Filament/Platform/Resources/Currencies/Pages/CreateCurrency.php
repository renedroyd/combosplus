<?php

namespace App\Filament\Platform\Resources\Currencies\Pages;

use App\Filament\Platform\Resources\Currencies\CurrencyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCurrency extends CreateRecord
{
    protected static string $resource = CurrencyResource::class;
}
