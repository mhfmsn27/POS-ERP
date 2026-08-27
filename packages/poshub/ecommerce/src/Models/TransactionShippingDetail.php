<?php

namespace Poshub\Ecommerce\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionShippingDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'transaction_id',
        'curir_name',
        'curir_code',
        'curir_service',
        'to_subdistrict_id',
        'postal_code',
        'phone',
        'address_detail',
        'resi_no',
        'name'
    ];

    public function subdistrict()
    {
        return $this->belongsTo(SubDistrict::class, 'to_subdistrict_id');
    }
}
