<?php

namespace App\Filament\Widgets;

use App\Enums\TenantRole;
use App\Models\TenantMembership;
use App\Models\PlatformUser;
use Filament\Actions\Action;
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
            return ['steps' => [], 'progress' => 0];
        }

        $membership = TenantMembership::query()
            ->where('user_id', $user->getAuthIdentifier())
            ->where('status', 'active')
            ->where('role', TenantRole::Owner)
            ->latest()
            ->first();

        if (! $membership) {
            return ['steps' => [], 'progress' => 0];
        }

        $tenant = $membership->tenant;
        $hasProfile = filled($tenant?->name) && filled($tenant?->slug);
        $productCount = $tenant
            ? $tenant->run(fn () => \App\Models\Product::query()->count())
            : 0;
        $categoryCount = $tenant
            ? $tenant->run(fn () => \App\Models\Category::query()->count())
            : 0;

        $steps = [
            ['title' => 'Completa tu tienda', 'description' => 'Nombre y URL pública listos.', 'done' => $hasProfile, 'url' => route('marketplace.store', ['tenant' => $tenant?->slug])],
            ['title' => 'Crea una categoría', 'description' => 'Organiza tu catálogo.', 'done' => $categoryCount > 0, 'url' => '/admin/categories/create'],
            ['title' => 'Añade tu primer producto', 'description' => 'Publica algo para tus clientes.', 'done' => $productCount > 0, 'url' => '/admin/products/create'],
            ['title' => 'Publica tu catálogo', 'description' => 'Haz visibles tus productos.', 'done' => $productCount > 0, 'url' => '/admin/products'],
        ];

        $completed = collect($steps)->where('done', true)->count();

        return [
            'steps' => $steps,
            'progress' => (int) round(($completed / count($steps)) * 100),
        ];
    }
}
