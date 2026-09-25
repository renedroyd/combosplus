<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MarketplaceReviewSchemaTest extends TestCase
{
    public function test_marketplace_review_tables_are_available_on_the_central_connection(): void
    {
        $connection = Schema::connection(config('database.default'));

        $this->assertTrue($connection->hasTable('store_reviews'));
        $this->assertTrue($connection->hasTable('product_reviews'));
        $this->assertTrue($connection->hasTable('platform_reviews'));

        $this->assertTrue($connection->hasColumn('store_reviews', 'verified_purchase'));
        $this->assertTrue($connection->hasColumn('product_reviews', 'verified_purchase'));
        $this->assertTrue($connection->hasColumn('store_reviews', 'status'));
        $this->assertTrue($connection->hasColumn('platform_reviews', 'status'));
    }
}
