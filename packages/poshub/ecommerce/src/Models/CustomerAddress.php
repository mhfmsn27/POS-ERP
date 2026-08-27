<?php

namespace Poshub\Ecommerce\Models;

use App\Models\Admin\Customer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'name',
        'sub_district_id',
        'address',
        'postal_code',
        'phone',
        'default',
        'long',
        'lang'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function subdistrict()
    {
        return $this->belongsTo(SubDistrict::class, 'sub_district_id');
    }
}
