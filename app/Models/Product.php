<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function priceRules()
    {
        return $this->hasMany(PriceRule::class);
    }

    /**
     * Get the generic name associated with the medicine.
     */
    public function generic()
    {
        return $this->belongsTo(Generic::class);
    }
}
