<?php

namespace App\Filament\Widgets;

use App\Enums\CatalogStatus;
use App\Enums\TenantRole;
use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Products\ProductResource;
use App\Models\PlatformUser;
use App\Models\TenantMembership;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class StoreOnboarding extends Widget
{
    protected string $view = 'filament.widgets.store-onboarding';

    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
        $user = Auth::guard('admin')->user() ?? Auth::user();

        if (! $user instanceof PlatformUser) {
            return ['steps' => [], 'progress' => 0, 'catalogStatus' => null];
        }

        $membership = TenantMembership::query()
            ->where('user_id', $user->getAuthIdentifier())
            ->where('status', 'active')
            ->where('role', TenantRole::Owner)
            ->latest()
            ->first();

        if (! $membership) {
            return ['steps' => [], 'progress' => 0, 'catalogStatus' => null];
        }

        $tenant = $membership->tenant;
        $hasProfile = filled($tenant?->name) && filled($tenant?->slug);
        $productCount = $tenant ? $tenant->run(fn () => \App\Models\Product::query()->count()) : 0;
        $visibleProductCount = $tenant ? $tenant->run(fn () => \App\Models\Product::query()->where('is_visible', true)->count()) : 0;
        $categoryCount = $tenant ? $tenant->run(fn () => \App\Models\Category::query()->count()) : 0;
        $catalogStatus = $tenant?->catalog_status ?? CatalogStatus::Draft->value;

        $steps = [
            [
                'title' => 'Completa tu tienda',
                'description' => 'Nombre y URL pública listos.',
                'done' => $hasProfile,
                'url' => $hasProfile ? route('marketplace.store', ['tenant' => $tenant->slug]) : '/admin',
            ],
            [
                'title' => 'Crea una categoría',
                'description' => 'Organiza tu catálogo.',
                'done' => $categoryCount > 0,
                'url' => CategoryResource::getUrl('create'),
            ],
            [
                'title' => 'Añade tu primer producto',
                'description' => 'Carga al menos un producto.',
                'done' => $productCount > 0,
                'url' => ProductResource::getUrl('create'),
            ],
            [
                'title' => 'Publica tu catálogo',
                'description' => match ($catalogStatus) {
                    CatalogStatus::Published->value => 'Tu catálogo ya está visible en el Marketplace.',
                    CatalogStatus::Unpublished->value => 'Tu catálogo está listo para publicar.',
                    default => 'Completa tu catálogo y publícalo cuando esté listo.',
                },
                'done' => $catalogStatus === CatalogStatus::Published->value,
                'url' => ProductResource::getUrl('index'),
            ],
        ];

        $completed = collect($steps)->where('done', true)->count();

        return [
            'steps' => $steps,
            'progress' => (int) round(($completed / count($steps)) * 100),
            'catalogStatus' => $catalogStatus,
            'catalogStatusLabel' => CatalogStatus::tryFrom($catalogStatus)?->label() ?? CatalogStatus::Draft->label(),
        ];
    }
}
