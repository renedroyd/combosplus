<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Determine whether assigning $parentId to $categoryId would create a cycle.
     */
    public static function wouldCreateParentCycle(int|string $categoryId, int|string $parentId): bool
    {
        $categoryId = (string) $categoryId;
        $currentId = (string) $parentId;
        $visited = [];

        while ($currentId !== '') {
            if ($currentId === $categoryId || isset($visited[$currentId])) {
                return true;
            }

            $visited[$currentId] = true;
            $nextParentId = static::query()->whereKey($currentId)->value('parent_id');

            if ($nextParentId === null) {
                return false;
            }

            $currentId = (string) $nextParentId;
        }

        return false;
    }
}
