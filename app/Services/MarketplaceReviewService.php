<?php

namespace App\Services;

use App\Models\PlatformReview;
use App\Models\Product;
use App\Models\ProductReview;
use App\Models\StoreReview;
use App\Models\Tenant;

class MarketplaceReviewService
{
    public function refreshStore(Tenant $tenant): void
    {
        $aggregate = StoreReview::approved()
            ->where('tenant_id', $tenant->getTenantKey())
            ->selectRaw('AVG(rating) as average_rating, COUNT(*) as review_count')
            ->first();

        $tenant->forceFill([
            'rating' => $aggregate?->average_rating,
            'review_count' => (int) ($aggregate?->review_count ?? 0),
        ])->save();
    }

    public function refreshProduct(Tenant $tenant, int $productId): void
    {
        $aggregate = ProductReview::approved()
            ->where('tenant_id', $tenant->getTenantKey())
            ->where('product_id', $productId)
            ->selectRaw('AVG(rating) as average_rating')
            ->first();

        $tenant->run(function () use ($productId, $aggregate): void {
            Product::query()->whereKey($productId)->update([
                'rating' => $aggregate?->average_rating,
            ]);
        });
    }

    public function platformRating(): ?float
    {
        return PlatformReview::approved()->avg('rating');
    }
}