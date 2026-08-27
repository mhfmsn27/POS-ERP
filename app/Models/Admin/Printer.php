<?php

namespace App\Models\Admin;

use App\Models\Scopes\FilterByMerchant;
use App\Models\Scopes\FilterByStores;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Printer extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded          = [];

    protected static function booted()
    {
        parent::boot();
        static::addGlobalScope(function(Builder $builder) {
            $user   = auth()->user();
            $store  = my_store();
            if ($user && $store != null) {
                if($user->role_type == 'user') {
                    $builder->from($builder->getModel()->getTable())->where('' . $builder->getModel()->getTable() . '.merchant_id', $store);
                }
            }
        });

        static::creating(function ($model) {
            if (my_store()) {
                $model->merchant_id = my_store();
            }
        });
    }
}
