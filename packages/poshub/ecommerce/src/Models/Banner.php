<?php

namespace Poshub\Ecommerce\Models;

use App\Models\Scopes\FilterByMerchant;
use App\Models\Scopes\FilterByStores;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $fillable = [
        'image', 
        'title', 
        'position', 
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

    public function getPositionNameAttribute()
    {
        if($this->position == 'home') {
            return 'Home Page';
        }

        if($this->position == 'shop') {
            return 'Shop Page';
        }

        if($this->position == 'blog') {
            return 'Blog Page';
        }

        if($this->position == 'mobile') {
            return 'Mobile Page';
        }
    }
}
