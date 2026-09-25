<?php

namespace App\Http\Controllers;

use App\Models\PlatformReview;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\StoreReview;
use App\Models\Tenant;
use App\Models\PlatformUser;
use App\Models\TenantMembership;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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

        $storeReviews = StoreReview::approved()->with('tenant')->latest()->take(4)->get();
        $platformReviews = PlatformReview::approved()->with('user')->latest()->take(3)->get();

        return view('marketplace.home', compact('stores', 'products', 'storeReviews', 'platformReviews'));
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

        if ($request->user()) {
        } else {
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
            $tenant = DB::transaction(function () use ($data, $user, &$createdUser): Tenant {
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

                $tenant->forceFill([
                    'name' => $data['store_name'],
                    'slug' => Str::lower($data['slug']),
                    'description' => $data['description'] ?? null,
                    'status' => 'active',
                ])->save();

                TenantMembership::create([
                    'tenant_id' => $tenant->getTenantKey(),
                    'user_id' => $owner->getAuthIdentifier(),
                    'role' => 'owner',
                    'status' => 'active',
                    'is_owner' => true,
                ]);

                return $tenant;
            });
        } catch (\Throwable $e) {
            if ($tenant) {
                $tenant->delete();
            }

            throw $e;
        }

        if (! $user && $createdUser) {
            Auth::login($createdUser);
            $request->session()->regenerate();
        }

        return redirect()->route('tenant.switch', ['tenantId' => $tenant->getTenantKey()])
            ->with('status', 'Tu tienda fue creada. Ahora puedes configurar tu catálogo.');
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

    public function product(Tenant $tenant, string $product)
    {
        abort_unless($tenant->status === 'active', 404);

        $product = $tenant->run(
            fn () => Product::query()->findOrFail($product)
        );

        $reviews = ProductReview::approved()
            ->where('tenant_id', $tenant->getTenantKey())
            ->where('product_id', $product->getKey())
            ->with('user')
            ->latest()
            ->take(12)
            ->get();

        return view('marketplace.product', compact('tenant', 'product', 'reviews'));
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
                    ->where('is_visible', true)
                    ->get()
                    ->each(fn (Product $product) => $product->setAttribute('marketplace_tenant_id', $tenant->getKey()))
            )
        );
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
