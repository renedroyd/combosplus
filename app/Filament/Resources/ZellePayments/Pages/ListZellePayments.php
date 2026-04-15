<?php

namespace App\Filament\Resources\ZellePayments\Pages;

use App\Filament\Resources\ZellePayments\ZellePaymentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListZellePayments extends ListRecords
{
    protected static string $resource = ZellePaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //CreateAction::make(),
        ];
    }
}
