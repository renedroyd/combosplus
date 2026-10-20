<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Order totals are calculated by the checkout/pricing flow. Ignore
        // submitted financial values here, including forged Livewire payloads.
        foreach (['subtotal', 'tax', 'shipping_cost', 'discount', 'total'] as $field) {
            unset($data[$field]);
        }

        return parent::handleRecordUpdate($record, $data);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
