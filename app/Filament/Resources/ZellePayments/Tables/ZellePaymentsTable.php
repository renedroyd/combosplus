<?php

namespace App\Filament\Resources\ZellePayments\Tables;

use App\Filament\Tables\PremiumTableDefaults;
use App\Models\ZellePayment;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ZellePaymentsTable
{
    public static function configure(Table $table): Table
    {
        return PremiumTableDefaults::apply($table, 'Buscar pagos Zelle...')
            ->columns([
                TextColumn::make('order.order_number')->label('Pedido #')->searchable(),
                TextColumn::make('order.total')->label('Monto')->money('USD')->sortable(),
                TextColumn::make('reference_number')->label('Referencia')->searchable(),
                ImageColumn::make('proof_path')->label('Comprobante')->disk('public')->imageSize(40),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'confirmed',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending' => 'Pendiente',
                        'confirmed' => 'Confirmado',
                        'rejected' => 'Rechazado',
                    }),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                Action::make('confirm')
                    ->label('Confirmar pago')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (ZellePayment $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (ZellePayment $record) {
                        $record->update(['status' => 'confirmed']);
                        $record->order->update(['payment_status' => 'paid']);

                        Notification::make()
                            ->title('Pago confirmado')
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label('Rechazar pago')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (ZellePayment $record) => $record->status === 'pending')
                    ->schema([
                        Textarea::make('rejection_reason')
                            ->label('Motivo del rechazo')
                            ->required(),
                    ])
                    ->action(function (ZellePayment $record, array $data) {
                        $record->update([
                            'status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                        ]);
                        $record->order->update(['payment_status' => 'failed']);

                        Notification::make()
                            ->title('Pago rechazado')
                            ->danger()
                            ->send();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
