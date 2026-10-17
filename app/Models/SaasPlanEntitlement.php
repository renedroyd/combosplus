<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class SaasPlanEntitlement extends Model
{
    use CentralConnection;

    protected $table = 'saas_plan_entitlements';

    protected $fillable = ['plan_id', 'key', 'value', 'description'];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SaasPlan::class, 'plan_id');
    }

    public function numericLimit(): ?int
    {
        if (strtolower($this->value) === 'unlimited') {
            return null;
        }

        return is_numeric($this->value) ? max(0, (int) $this->value) : null;
    }

    public function booleanValue(): ?bool
    {
        return match (strtolower($this->value)) {
            'true', '1', 'yes', 'enabled' => true,
            'false', '0', 'no', 'disabled' => false,
            default => null,
        };
    }
}
