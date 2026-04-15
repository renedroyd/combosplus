<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ZellePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'reference_number',
        'proof_path',
        'status',
        'rejection_reason',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}