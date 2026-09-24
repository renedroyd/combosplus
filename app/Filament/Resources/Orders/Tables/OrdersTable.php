<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('order_number')
                    ->label('No. Orden')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Cliente')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge(),
                TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->money()
                    ->sortable(),
                TextColumn::make('shipping_cost')
                    ->label('Costo entrega')
                    ->money()
                    ->sortable(),
                TextColumn::make('discount')
                    ->label('Descuento')
                    ->money()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('total')
                    ->label('Total')
                    ->money()
                    ->sortable(),
                TextColumn::make('payment_status')
                    ->label('Pagado')
                    ->badge(),
                TextColumn::make('paymentMethod.name')
                    ->label('Metodo de pago')
                    ->badge()
                    ->searchable(),
                TextColumn::make('shipping_address_id')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('billing_address_id')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('placed_at')
                    ->label('Fecha')
                    ->date('d/m/y')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options(fn (): array => static::distinctOptions('status')),
                SelectFilter::make('payment_status')
                    ->label('Pago')
                    ->options(fn (): array => static::distinctOptions('payment_status')),
                SelectFilter::make('payment_method_id')
                    ->label('Metodo de pago')
                    ->relationship('paymentMethod', 'name'),
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

    private static function distinctOptions(string $column): array
    {
        return App\Models\Order::query()
            ->whereNotNull($column)
            ->distinct()
            ->orderBy($column)
            ->pluck($column, $column)
            ->mapWithKeys(fn ($value, $key): array => [(string) $key => ucfirst((string) $value)])
            ->all();
    }
}
