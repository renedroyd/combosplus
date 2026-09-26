<?php

namespace App\Http\Controllers;

use App\Enums\CatalogStatus;
use App\Models\PlatformReview;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\StoreReview;
use App\Models\Tenant;
use App\Models\PlatformUser;
use App\Models\TenantMembership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MarketplaceController extends Controller
{
    public function home()
    {
        $publishedTenants = $this->publishedTenants();

        $stores = $publishedTenants
            ->sortByDesc(fn (Tenant $tenant) => $this->storeScore($tenant))
            ->take(8)
            ->values();

        $newStores = $publishedTenants
            ->sortByDesc(fn (Tenant $tenant) => $tenant->created_at)
            ->take(6)
            ->values();

        $products = $this->collectProducts($publishedTenants);

        $featuredProducts = $products
            ->sortByDesc(fn (Product $product) => $this->productScore($product))
            ->take(8)
            ->values();

        $recentProducts = $products
            ->sortByDesc(fn (Product $product) => $product->created_at)
            ->take(8)
            ->values();

        $categories = $this->collectCategories($publishedTenants)
            ->sortByDesc('product_count')
            ->take(10)
            ->values();

        $storeReviews = StoreReview::approved()->with('tenant', 'user')->latest()->take(4)->get();
        $platformReviews = PlatformReview::approved()->with('user')->latest()->take(3)->get();

        return view('marketplace.home', compact(
            'stores',
            'newStores',
            'featuredProducts',
            'recentProducts',
            'categories',
            'storeReviews',
            'platformReviews',
        ));
    }

    public function register()
    {
        return view('marketplace.register');
    }

    public function registerStore(Request $request)
    {
        $rules = [
            'store_name' => ['required', 'string', 'max:120'],
            'slug' => ['required', 'string', 'alpha_dash', 'min:3', 'max:80', 'unique:tenants,slug'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];

        if (! $request->user()) {
            $rules += [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],
            ];
        }

        $data = $request->validate($rules);
        $user = $request->user();
        $tenant = null;
        $createdUser = null;

        try {
            // Tenant provisioning creates a database and runs tenant migrations.
            // MySQL DDL implicitly commits, so this lifecycle cannot be wrapped in
            // the central database transaction without leaving it inactive.
            $owner = $user;

            if (! $owner) {
                $owner = PlatformUser::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                ]);
                $createdUser = $owner;
            }

            $tenant = Tenant::create([
                'id' => (string) Str::uuid(),
            ]);

            Tenant::query()
                ->whereKey($tenant->getTenantKey())
                ->update([
                    'name' => $data['store_name'],
                    'slug' => Str::lower($data['slug']),
                    'description' => $data['description'] ?? null,
                    'status' => 'active',
                    'catalog_status' => CatalogStatus::Draft->value,
                ]);

            $tenant->refresh();

            $tenant->domains()->firstOrCreate([
                'domain' => Str::lower($data['slug']) . '.' . config('tenancy.central_domains.0', 'localhost'),
            ]);

            TenantMembership::create([
                'tenant_id' => $tenant->getTenantKey(),
                'user_id' => $owner->getAuthIdentifier(),
                'role' => 'owner',
                'status' => 'active',
                'is_owner' => true,
            ]);
        } catch (\\Throwable $e) {
            if ($tenant) {
                $tenant->delete();
            }

            if ($createdUser && ! TenantMembership::query()->where('user_id', $createdUser->getAuthIdentifier())->exists()) {
                $createdUser->delete();
            }

            throw $e;
        }

        if (! $user && $createdUser) {
            Auth::login($createdUser);
            $request->session()->regenerate();
        }

        return redirect()->route('tenant.switch', ['tenantId' => $tenant->getTenantKey()])
            ->with('status', 'Tu tienda fue creada. Ahora puedes configurar y publicar tu catálogo.');
    }

    public function stores(Request $request)
    {
        $query = trim((string) $request->query('q'));
        $category = trim((string) $request->query('category'));
        $sort = (string) $request->query('sort', 'rating');
        $needle = mb_strtolower($query);
        $categoryNeedle = mb_strtolower($category);

        $publishedTenants = $this->publishedTenants();
        $categories = $this->collectCategories($publishedTenants)
            ->sortByDesc('product_count')
            ->values();

        $stores = $publishedTenants
            ->filter(function (Tenant $tenant) use ($query, $needle, $categoryNeedle): bool {
                $matchesText = $query === ''
                    || str_contains(mb_strtolower((string) $tenant->name), $needle)
                    || str_contains(mb_strtolower((string) $tenant->description), $needle);

                if (! $matchesText || $categoryNeedle === '') {
                    return $matchesText;
                }

                return $tenant->run(
                    fn () => \App\Models\Category::query()
                        ->whereRaw('LOWER(name) = ?', [$categoryNeedle])
                        ->whereHas('products', fn ($products) => $products->where('is_visible', true))
                        ->exists()
                );
            });

        $stores = match ($sort) {
            'newest' => $stores->sortByDesc(fn (Tenant $tenant) => $tenant->created_at),
            'name' => $stores->sortBy(fn (Tenant $tenant) => mb_strtolower((string) $tenant->name)),
            default => $stores->sortByDesc(fn (Tenant $tenant) => $this->storeScore($tenant)),
        };

        return view('marketplace.stores', compact('stores', 'query', 'category', 'sort', 'categories'));
    }

    public function store(Tenant $tenant)
    {
        abort_unless($tenant->status === 'active' && $tenant->catalog_status === CatalogStatus::Published->value, 404);

        $products = $tenant->run(
            fn () => Product::query()
                ->where('is_visible', true)
                ->latest()
                ->take(24)
                ->get()
        );

        $reviews = StoreReview::approved()
            ->where('tenant_id', $tenant->getTenantKey())
            ->with('user')
            ->latest()
            ->take(12)
            ->get();

        return view('marketplace.store', compact('tenant', 'products', 'reviews'));
    }

    public function buy(Request $request, Tenant $tenant, string $product)
    {
        abort_unless(
            $tenant->status === 'active'
                && $tenant->catalog_status === CatalogStatus::Published->value,
            404,
        );

        $productModel = $tenant->run(
            fn () => Product::query()->findOrFail($product)
        );

        abort_unless($productModel->is_visible, 404);

        $domain = $tenant->domains()->orderBy('id')->first();
        abort_unless($domain, 409, 'La tienda no tiene un dominio público configurado.');

        return redirect()->away(
            $request->getScheme() . '://' . $domain->domain . '/productos/' . $productModel->getKey()
        );
    }

    public function product(Tenant $tenant, string $product)
    {
        abort_unless($tenant->status === 'active' && $tenant->catalog_status === CatalogStatus::Published->value, 404);

        $product = $tenant->run(
            fn () => Product::query()->findOrFail($product)
        );

        abort_unless($product->is_visible, 404);

        $reviews = ProductReview::approved()
            ->where('tenant_id', $tenant->getTenantKey())
            ->where('product_id', $product->getKey())
            ->with('user')
            ->latest()
            ->take(12)
            ->get();

        return view('marketplace.product', compact('tenant', 'product', 'reviews'));
    }

    private function publishedTenants()
    {
        return Tenant::query()
            ->where('status', 'active')
            ->where('catalog_status', CatalogStatus::Published->value)
            ->whereNotNull('name')
            ->get();
    }

    private function collectProducts($tenants)
    {
        return $tenants->flatMap(
            fn (Tenant $tenant) => $tenant->run(
                fn () => Product::query()
                    ->where('is_visible', true)
                    ->get()
                    ->each(fn (Product $product) => $product->setAttribute('marketplace_tenant_id', $tenant->getKey()))
            )
        );
    }

    private function collectCategories($tenants)
    {
        return $tenants->flatMap(
            fn (Tenant $tenant) => $tenant->run(
                fn () => \App\Models\Category::query()
                    ->withCount(['products' => fn ($query) => $query->where('is_visible', true)])
                    ->get()
                    ->each(function ($category) use ($tenant): void {
                        $category->setAttribute('marketplace_tenant_id', $tenant->getKey());
                    })
            )
        )->map(fn ($category) => (object) [
            'name' => $category->name,
            'product_count' => (int) $category->products_count,
        ])->groupBy(fn ($category) => mb_strtolower($category->name))
            ->map(fn ($group) => (object) [
                'name' => $group->first()->name,
                'product_count' => $group->sum('product_count'),
            ]);
    }

    private function productScore(Product $product): float
    {
        $rating = (float) ($product->rating ?? 0);
        $reviews = (int) ($product->review_count ?? 0);

        if ($rating <= 0) {
            return 0.0;
        }

        return (($reviews / ($reviews + 5)) * $rating)
            + ((5 / ($reviews + 5)) * 4.0);
    }

    private function storeScore(Tenant $tenant): float
    {
        $rating = (float) ($tenant->rating ?? 0);
        $reviews = (int) ($tenant->review_count ?? 0);
        $priorRating = 4.0;
        $minimumReviews = 5;

        if ($reviews === 0 || $rating <= 0) {
            return 0.0;
        }

        return (($reviews / ($reviews + $minimumReviews)) * $rating)
            + (($minimumReviews / ($reviews + $minimumReviews)) * $priorRating);
    }
}
