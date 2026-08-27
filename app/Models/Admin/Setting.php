<?php

namespace App\Models\Admin;

use App\Models\Scopes\FilterByMerchant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $guarded          = [];
    
    protected static function booted()
    {
        parent::boot();
        static::addGlobalScope(new FilterByMerchant);

        if (auth()->check()) {
            if (auth()->user()->merchant_id) {
                static::creating(function ($model) {
                    $model->merchant_id = auth()->user()->merchant_id;
                });
            }
        }
    }
}
