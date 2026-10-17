<?php

namespace App\Filament\Resources\Branches\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class BranchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Sucursal')->searchable()->sortable(),
                TextColumn::make('address')->label('Dirección')->limit(45)->placeholder('—'),
                TextColumn::make('phone')->label('Teléfono')->placeholder('—'),
                TextColumn::make('is_active')->label('Estado')->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Activa' : 'Inactiva')
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label('Estado')->trueLabel('Activas')->falseLabel('Inactivas'),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
