<?php

namespace App\Filament\Tables;

use Filament\Tables\Table;

final class PremiumTableDefaults
{
    public static function apply(Table $table, string $searchPlaceholder = 'Buscar registros...'): Table
    {
        return $table
            ->searchPlaceholder($searchPlaceholder)
            ->searchDebounce('400ms')
            ->paginationPageOptions([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->stackedOnMobile();
    }
}
