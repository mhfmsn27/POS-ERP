<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellPurchase extends Model
{
    use HasFactory;


    protected $fillable = [
        'purchase_id',
        'sell_id',
        'qty',
        'purchase_price'
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function sell()
    {
        return $this->belongsTo(Sell::class, 'sell_id');
    }
}
