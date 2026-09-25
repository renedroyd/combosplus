<?php

namespace App\Filament\Pages;

use App\Enums\CatalogStatus;
use App\Models\Category;
use App\Models\PlatformUser;
use App\Models\Product;
use App\Models\Tenant;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CatalogPublication extends Page
{
    protected static ?string $title = 'Publicar catálogo';

    protected static ?string $navigationLabel = 'Publicar catálogo';

    protected static string|\UnitEnum|null $navigationGroup = 'Tienda';

    protected static ?int $navigationSort = 2;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

    protected string $view = 'filament.pages.catalog-publication';

    public function getViewData(): array
    {
        $tenant = $this->currentTenant();

        if (! $tenant) {
            return [
                'status' => CatalogStatus::Draft,
                'categoryCount' => 0,
                'productCount' => 0,
                'visibleProductCount' => 0,
                'profileComplete' => false,
            ];
        }

        $profile = $this->centralTenantProfile($tenant);

        return [
            'status' => CatalogStatus::tryFrom((string) ($profile->catalog_status ?? CatalogStatus::Draft->value)) ?? CatalogStatus::Draft,
            'categoryCount' => $tenant->run(fn () => Category::query()->count()),
            'productCount' => $tenant->run(fn () => Product::query()->count()),
            'visibleProductCount' => $tenant->run(fn () => Product::query()->where('is_visible', true)->count()),
            'profileComplete' => filled($profile->name ?? null) && filled($profile->slug ?? null),
        ];
    }

    public function publish(): void
    {
        $tenant = $this->currentTenant();
        abort_unless($tenant, 404);

        $this->ensureOwner();

        $categoryCount = $tenant->run(fn () => Category::query()->count());
        $productCount = $tenant->run(fn () => Product::query()->count());
        $visibleProductCount = $tenant->run(fn () => Product::query()->where('is_visible', true)->count());

        $profile = $this->centralTenantProfile($tenant);
        $errors = [];
        if (blank($profile->name ?? null) || blank($profile->slug ?? null)) {
            $errors['name'] = 'Completa el nombre y la URL pública de tu tienda antes de publicar.';
        }
        if ($categoryCount < 1) {
            $errors['catalog'] = 'Crea al menos una categoría antes de publicar.';
        }
        if ($productCount < 1) {
            $errors['catalog'] = 'Añade al menos un producto antes de publicar.';
        }
        if ($visibleProductCount < 1) {
            $errors['catalog'] = 'Haz visible al menos un producto antes de publicar.';
        }

        if ($errors) {
            throw ValidationException::withMessages($errors);
        }

        $this->setStatus(CatalogStatus::Published);

        Notification::make()
            ->title('Catálogo publicado')
            ->body('Tu tienda ya puede aparecer en el Marketplace.')
            ->success()
            ->send();
    }

    public function unpublish(): void
    {
        $tenant = $this->currentTenant();
        abort_unless($tenant, 404);

        $this->ensureOwner();
        $this->setStatus(CatalogStatus::Unpublished);

        Notification::make()
            ->title('Catálogo no publicado')
            ->body('Tu tienda dejó de mostrarse públicamente en el Marketplace.')
            ->success()
            ->send();
    }

    private function centralTenantProfile(Tenant $tenant): object
    {
        $connection = config('tenancy.database.central_connection', config('database.default'));

        return DB::connection($connection)
            ->table('tenants')
            ->where('id', $tenant->getTenantKey())
            ->first() ?? (object) [];
    }

    private function setStatus(CatalogStatus $status): void
    {
        $tenant = $this->currentTenant();
        $connection = config('tenancy.database.central_connection', config('database.default'));

        DB::connection($connection)
            ->table('tenants')
            ->where('id', $tenant->getTenantKey())
            ->update([
                'catalog_status' => $status->value,
                'updated_at' => now(),
            ]);
    }

    private function currentTenant(): ?Tenant
    {
        $tenant = tenant();

        return $tenant instanceof Tenant ? $tenant : null;
    }

    private function ensureOwner(): PlatformUser
    {
        $user = auth('admin')->user();

        abort_unless($user instanceof PlatformUser, 403);

        abort_unless(
            $user->memberships()
                ->where('tenant_id', $this->currentTenant()?->getTenantKey())
                ->where('role', 'owner')
                ->where('status', 'active')
                ->exists(),
            403,
        );

        return $user;
    }
}
