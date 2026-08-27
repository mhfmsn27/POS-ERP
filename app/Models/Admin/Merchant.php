<?php

namespace App\Models\Admin;

use App\Models\Product\Supplier;
use App\Models\Transaction\TransactionPackage;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Merchant extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded          = [];

    public function store()
    {
        return $this->hasMany(Store::class, 'merchant_id');
    }

    public function user()
    {
        return $this->hasMany(User::class, 'merchant_id');
    }

    public function owner()
    {
        return $this->hasOne(User::class, 'merchant_id')->orderBy("id", "asc");
    }

    public function transaction()
    {
        return $this->hasMany(TransactionPackage::class, 'merchant_id');
    }

    public function supplier()
    {
        return Supplier::whereHas('store', function ($q) {
            return $q->where("merchant_id", $this->id);
        });
    }
}
