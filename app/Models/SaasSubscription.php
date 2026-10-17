<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class SaasSubscription extends Model
{
    use CentralConnection;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_CANCELED = 'canceled';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_PAST_DUE = 'past_due';

    protected $table = 'saas_subscriptions';

    protected $fillable = [
        'tenant_id',
        'plan_id',
        'plan_price_id',
        'latest_payment_id',
        'status',
        'starts_at',
        'ends_at',
        'next_billing_at',
        'canceled_at',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'next_billing_at' => 'datetime',
            'canceled_at' => 'datetime',
        ];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SaasPlan::class, 'plan_id');
    }

    public function planPrice(): BelongsTo
    {
        return $this->belongsTo(SaasPlanPrice::class, 'plan_price_id');
    }

    public function latestPayment(): BelongsTo
    {
        return $this->belongsTo(SaasSubscriptionPayment::class, 'latest_payment_id');
    }

    public function scopeCurrent(Builder $query, ?Carbon $at = null): Builder
    {
        $at ??= now();

        return $query
            ->where('status', self::STATUS_ACTIVE)
            ->where('starts_at', '<=', $at)
            ->where('ends_at', '>', $at);
    }

    public function isCurrent(?Carbon $at = null): bool
    {
        $at ??= now();

        return $this->status === self::STATUS_ACTIVE
            && $this->starts_at?->lte($at)
            && $this->ends_at?->gt($at);
    }
}
