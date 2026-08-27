<?php

namespace Poshub\Ecommerce\Models;

use App\Models\Product\Product;
use App\Models\Product\Variation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerCart extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'variation_id',
        'quantity',
    ];

    public function variation()
    {
        return $this->belongsTo(Variation::class, 'variation_id')->withTrashed();
    }
}
