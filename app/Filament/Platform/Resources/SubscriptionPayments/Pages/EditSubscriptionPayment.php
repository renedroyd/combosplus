<?php

namespace App\Filament\Platform\Resources\SubscriptionPayments\Pages;

use App\Filament\Platform\Resources\SubscriptionPayments\SubscriptionPaymentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSubscriptionPayment extends EditRecord
{
    protected static string $resource = SubscriptionPaymentResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['status'] = $this->record->status;
        $data['idempotency_key'] = $this->record->idempotency_key;
        $data['reviewed_by_admin_id'] = $this->record->reviewed_by_admin_id;
        $data['reviewed_at'] = $this->record->reviewed_at;
        $data['rejection_reason'] = $this->record->rejection_reason;

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()->visible(fn (): bool => $this->record->isPending())];
    }
}
