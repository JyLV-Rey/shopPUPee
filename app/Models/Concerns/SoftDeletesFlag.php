<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * @method static Builder active()
 * @method static Builder deleted()
 */
trait SoftDeletesFlag
{
    /**
     * Scope a query to only include non-deleted rows.
     */
    public function scopeActive(Builder $query): void
    {
        $query->whereRaw('NOT is_deleted');
    }

    /**
     * Scope a query to only include deleted rows.
     */
    public function scopeDeleted(Builder $query): void
    {
        $query->whereRaw('is_deleted');
    }
}
