<?php

namespace App\Filament\Pages;

use App\Models\PlatformUser;
use App\Models\Tenant;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StoreSettings extends Page
{
    protected static ?string $title = 'Mi tienda';

    protected static ?string $navigationLabel = 'Mi tienda';

    protected static string|\UnitEnum|null $navigationGroup = 'Tienda';

    protected static ?int $navigationSort = 1;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected string $view = 'filament.pages.store-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $tenant = tenant();

        abort_unless($tenant instanceof Tenant, 404);

        $this->form->fill([
            'name' => $tenant->name,
            'slug' => $tenant->slug,
            'description' => $tenant->description,
            'logo' => $tenant->logo,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nombre de la tienda')
                    ->required()
                    ->maxLength(120),
                TextInput::make('slug')
                    ->label('URL pública')
                    ->prefix('tienda.')
                    ->required()
                    ->alphaDash()
                    ->maxLength(80)
                    ->unique(
                        table: config('tenancy.database.central_connection', config('database.default')) . '.tenants',
                        column: 'slug',
                        ignoreRecord: true,
                    ),
                Textarea::make('description')
                    ->label('Descripción')
                    ->rows(4)
                    ->maxLength(500)
                    ->columnSpanFull(),
                FileUpload::make('logo')
                    ->label('Logo')
                    ->image()
                    ->disk('public')
                    ->directory('stores')
                    ->imageEditor()
                    ->maxSize(2048),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Guardar cambios')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $user = auth('admin')->user();

        abort_unless($user instanceof PlatformUser, 403);

        $tenant = tenant();

        abort_unless($tenant instanceof Tenant, 404);

        $data = $this->form->getState();

        validator($data, [
            'name' => ['required', 'string', 'max:120'],
            'slug' => [
                'required',
                'alpha_dash',
                'max:80',
                Rule::unique(config('tenancy.database.central_connection', config('database.default')) . '.tenants', 'slug')
                    ->ignore($tenant->getTenantKey(), 'id'),
            ],
            'description' => ['nullable', 'string', 'max:500'],
            'logo' => ['nullable', 'string'],
        ])->validate();

        DB::connection(config('tenancy.database.central_connection', config('database.default')))
            ->table('tenants')
            ->where('id', $tenant->getTenantKey())
            ->update([
                'name' => $data['name'],
                'slug' => $data['slug'],
                'description' => $data['description'] ?? null,
                'logo' => $data['logo'] ?? null,
                'updated_at' => now(),
            ]);

        $this->form->fill($data);

        $this->notify('success', 'Los datos de tu tienda fueron actualizados.');
    }
}
