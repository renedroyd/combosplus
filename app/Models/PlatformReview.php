<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class PlatformReview extends Model
{
    use CentralConnection;

    protected $fillable = ['user_id', 'rating', 'title', 'comment', 'status'];

    protected function casts(): array { return ['rating' => 'integer']; }

    public function user(): BelongsTo { return $this->belongsTo(PlatformUser::class); }
    public function scopeApproved($query) { return $query->where('status', 'approved'); }
}