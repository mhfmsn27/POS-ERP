<?php

namespace Poshub\Ecommerce\Models;

use App\Models\Scopes\FilterByMerchant;
use App\Models\Scopes\FilterByStores;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;

    protected $fillable = [
        'image', 
        'title', 
        'subtitle', 
        'button', 
        'button_name', 
        'button_url', 
        'store_id'
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
