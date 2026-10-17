<?php

namespace App\\Models;

use Illuminate\\Database\\Eloquent\\Model;
use Illuminate\\Database\\Eloquent\\Relations\\BelongsTo;
use Illuminate\\Support\\Str;
use Stancl\\Tenancy\\Database\\Concerns\\CentralConnection;

class SaasSubscriptionPayment extends Model
{
    use CentralConnection;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $table = 'saas_subscription_payments';

    protected $fillable = [
        'tenant_id',
        'submitted_by_user_id',
        'plan_price_id',
        'plan_name',
        'currency_code',
        'billing_interval',
        'amount',
        'payment_method',
        'payment_reference',
        'receipt_path',
        'status',
        'idempotency_key',
        'reviewed_by_admin_id',
        'reviewed_at',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'reviewed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $payment): void {
            $payment->idempotency_key ??= (string) Str::uuid();
        });
    }

    public function planPrice(): BelongsTo
    {
        return $this->belongsTo(SaasPlanPrice::class, 'plan_price_id');
    }

    public function submittedBy(): BelongsTo
    {
        return $this->belongsTo(PlatformUser::class, 'submitted_by_user_id');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'reviewed_by_admin_id');
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }
}
