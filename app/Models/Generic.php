<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Generic extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'indication',
        'side_effects',
    ];

    /**
     * Get the products associated with this generic name.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
