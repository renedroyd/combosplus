<?php

namespace App\Filament\Resources\ZellePayments;

use App\Filament\Resources\ZellePayments\Pages\CreateZellePayment;
use App\Filament\Resources\ZellePayments\Pages\EditZellePayment;
use App\Filament\Resources\ZellePayments\Pages\ListZellePayments;
use App\Filament\Resources\ZellePayments\Schemas\ZellePaymentForm;
use App\Filament\Resources\ZellePayments\Tables\ZellePaymentsTable;
use App\Models\ZellePayment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ZellePaymentResource extends Resource
{
    protected static ?string $model = ZellePayment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDevicePhoneMobile;

    protected static ?string $pluralLabel = 'Pagos Zelle';

    protected static ?string $modelLabel = 'Pago Zelle';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|UnitEnum|null $navigationGroup = 'Tienda';

    public static function form(Schema $schema): Schema
    {
        return ZellePaymentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ZellePaymentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListZellePayments::route('/'),
            'edit' => EditZellePayment::route('/{record}/edit'),
        ];
    }
}
