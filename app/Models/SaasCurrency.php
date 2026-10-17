<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class SaasCurrency extends Model
{
    use CentralConnection;

    protected $table = 'saas_currencies';

    protected $fillable = ['code', 'name', 'symbol', 'decimal_places', 'is_active'];

    protected function casts(): array
    {
        return ['decimal_places' => 'integer', 'is_active' => 'boolean'];
    }

    public function prices(): HasMany
    {
        return $this->hasMany(SaasPlanPrice::class, 'currency_id');
    }
}
