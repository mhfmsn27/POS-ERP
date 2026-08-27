<?php

namespace Poshub\Ecommerce\Models;

use App\Models\Scopes\FilterByMerchant;
use App\Models\Scopes\FilterByStores;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmallFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'position',
        'title',
        'subtitle',
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
        if($this->position == 'about') {
            return 'About Page';
        }

        if($this->position == 'footer') {
            return 'Footer Web';
        } 
    }
}
