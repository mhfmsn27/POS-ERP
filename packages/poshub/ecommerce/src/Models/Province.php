<?php

namespace Poshub\Ecommerce\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;

    protected $fillable = [
        'status', 
    ];

    public function city()
    {
        return $this->hasMany(City::class, 'province_id');
    }
}
