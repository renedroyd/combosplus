<?php

namespace App\Filament\Resources\Branches\Pages;

use App\Filament\Resources\Branches\StoreBranchResource;
use App\Models\Branch;
use App\Services\SaasTenantAccessService;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditBranch extends EditRecord
{
    protected static string $resource = StoreBranchResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $tenantId = (string) tenant()->getTenantKey();
        $access = app(SaasTenantAccessService::class);

        return $access->withTenantLock($tenantId, function () use ($record, $data, $tenantId, $access): Model {
            if (! $record->is_active && (bool) ($data['is_active'] ?? false)) {
                $access->assertCanCreateBranch($tenantId);
            }

            return parent::handleRecordUpdate($record, $data);
        });
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
