<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class SaasPlanPrice extends Model
{
    use CentralConnection;

    protected $table = 'saas_plan_prices';

    protected $fillable = ['plan_id', 'currency_id', 'billing_interval', 'amount', 'is_active'];

    protected function casts(): array
    {
        return ['amount' => 'decimal:2', 'is_active' => 'boolean'];
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SaasPlan::class, 'plan_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(SaasCurrency::class, 'currency_id');
    }
}
