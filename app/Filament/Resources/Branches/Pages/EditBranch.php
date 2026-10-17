<?php

namespace App\Filament\Resources\Branches\Pages;

use App\Filament\Resources\Branches\StoreBranchResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

if (! class_exists(EditBranch::class, false)) {
    class EditBranch extends EditRecord
    {
        protected static string $resource = StoreBranchResource::class;

        protected function getHeaderActions(): array
        {
            return [
                DeleteAction::make(),
            ];
        }
    }
}
