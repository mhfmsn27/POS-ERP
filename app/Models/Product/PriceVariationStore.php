<?php

namespace App\Models\Product;

use App\Models\Scopes\FilterByStores;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceVariationStore extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'variation_id',
        'store_id',
        'price'
    ];


    protected static function booted()
    {
        parent::boot();
        static::addGlobalScope(new FilterByStores);

        static::creating(function ($model) {
            $model->store_id = my_store();
        });
    }
}
