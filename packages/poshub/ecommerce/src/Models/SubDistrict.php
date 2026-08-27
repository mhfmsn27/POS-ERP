<?php

namespace Poshub\Ecommerce\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubDistrict extends Model
{
    use HasFactory;

    protected $fillable = [
        'status', 
    ];

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }
}
