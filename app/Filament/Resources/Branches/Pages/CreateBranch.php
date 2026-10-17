<?php

namespace App\Filament\Resources\Branches\Pages;

use App\Filament\Resources\Branches\StoreBranchResource;
use App\Services\SaasTenantAccessService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateBranch extends CreateRecord
{
    protected static string $resource = StoreBranchResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $tenantId = (string) tenant()->getTenantKey();
        $access = app(SaasTenantAccessService::class);

        return $access->withTenantLock($tenantId, function () use ($access, $tenantId, $data): Model {
            $access->assertCanCreateBranch($tenantId);

            return parent::handleRecordCreation($data);
        });
    }
}
