<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class SaasPlan extends Model
{
    use CentralConnection;

    protected $table = 'saas_plans';

    protected $fillable = ['name', 'slug', 'description', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'sort_order' => 'integer'];
    }

    public function prices(): HasMany
    {
        return $this->hasMany(SaasPlanPrice::class, 'plan_id');
    }

    public function entitlements(): HasMany
    {
        return $this->hasMany(SaasPlanEntitlement::class, 'plan_id');
    }
}
