<?php

namespace App\Models\Product;

use App\Models\Admin\Store;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductDiscount extends Model
{
    use HasFactory, SoftDeletes;

    public function variation()
    {
        return $this->belongsTo(Variation::class,'variation_id')->withTrashed();
    }

    public function store()
    {
        return $this->belongsTo(Store::class,'store_id')->withTrashed();
    }

    public function getproduct()
    {
        return $this->belongsTo(Variation::class,'get_product')->withTrashed();
    }

    public function voucherclaim()
    {
        return $this->hasMany(VoucherClaim::class,'voucher_id');
    }
}
