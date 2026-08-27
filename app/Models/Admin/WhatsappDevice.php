<?php

namespace App\Models\Admin;

use App\Models\Scopes\FilterByStores;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WhatsappDevice extends Model
{
    use HasFactory;

    protected $guarded          = [];

    protected static function booted()
    {
        parent::boot();
        static::addGlobalScope(new FilterByStores);

        if (auth()->check()) {
            static::creating(function ($model) {
                $model->store_id = my_store();
            });
        }
    }
}
