<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class ProductReview extends Model
{
    use CentralConnection;

    protected $fillable = [
        'tenant_id', 'product_id', 'user_id', 'order_id', 'rating', 'title', 'comment',
        'status', 'verified_purchase',
    ];

    protected function casts(): array
    {
        return ['rating' => 'integer', 'product_id' => 'integer', 'verified_purchase' => 'boolean'];
    }

    public function user(): BelongsTo { return $this->belongsTo(PlatformUser::class); }
    public function tenant(): BelongsTo { return $this->belongsTo(Tenant::class); }
    public function scopeApproved($query) { return $query->where('status', 'approved'); }
}