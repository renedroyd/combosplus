<?php

namespace App\Filament\Resources\ZellePayments\Pages;

use App\Filament\Resources\ZellePayments\ZellePaymentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditZellePayment extends EditRecord
{
    protected static string $resource = ZellePaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
