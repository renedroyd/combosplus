<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public function home()
    {
        $stores = $this->activeTenants()
            ->sortByDesc(fn (Tenant $tenant) => $this->storeScore($tenant))
            ->take(8)
            ->values();

        $products = $this->collectProducts($this->activeTenants())
            ->sortByDesc(fn (Product $product) => (float) ($product->rating ?? 0))
            ->take(8)
            ->values();

        return view('marketplace.home', compact('stores', 'products'));
    }

    public function stores(Request $request)
    {
        $query = trim((string) $request->query('q'));
        $needle = mb_strtolower($query);

        $stores = $this->activeTenants()
            ->filter(fn (Tenant $tenant) => $query === ''
                || str_contains(mb_strtolower((string) $tenant->name), $needle))
            ->sortByDesc(fn (Tenant $tenant) => $this->storeScore($tenant))
            ->values();

        return view('marketplace.stores', compact('stores', 'query'));
    }

    public function store(Tenant $tenant)
    {
        abort_unless($tenant->status === 'active', 404);

        $products = $tenant->run(
            fn () => Product::query()
                ->where(function ($query) {
                    $query->whereNull('status')
                        ->orWhereIn('status', ['active', 'published']);
                })
                ->latest()
                ->take(24)
                ->get()
        );

        return view('marketplace.store', compact('tenant', 'products'));
    }

    public function product(Tenant $tenant, string $product)
    {
        abort_unless($tenant->status === 'active', 404);

        $product = $tenant->run(
            fn () => Product::query()->findOrFail($product)
        );

        return view('marketplace.product', compact('tenant', 'product'));
    }

    private function activeTenants()
    {
        return Tenant::query()
            ->where('status', 'active')
            ->whereNotNull('name')
            ->get();
    }

    private function collectProducts($tenants)
    {
        return $tenants->flatMap(
            fn (Tenant $tenant) => $tenant->run(
                fn () => Product::query()
                    ->where(function ($query) {
                        $query->whereNull('status')
                            ->orWhereIn('status', ['active', 'published']);
                    })
                    ->get()
                    ->each(fn (Product $product) => $product->setAttribute('marketplace_tenant_id', $tenant->getKey()))
            )
        );
    }

    private function storeScore(Tenant $tenant): float
    {
        $rating = (float) ($tenant->rating ?? 0);
        $reviews = (int) ($tenant->review_count ?? 0);

        return $rating * min(1, $reviews / 10);
    }
}
