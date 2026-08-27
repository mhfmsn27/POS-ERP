<?php

namespace App\Models\Transaction;

use App\Models\Account\AccountTransaction;
use App\Models\Product\Unit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'sell_id',
        'return_qty',
        'condition',
        'unit_id',
        'unit_qty',
        'price',
        'tax_total',
        'goverment_tax',
        'service_tax'
    ];

    public function account()
    {
        return $this->hasMany(AccountTransaction::class, 'item_id')->where("transaction_id", $this->transaction_id);
    }

    public function sell()
    {
        return $this->belongsTo(Sell::class, 'sell_id')->withTrashed();
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id')->withTrashed();
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id')->withTrashed();
    }

    public function getQtyIntoUnitAttribute()
    {
        $value = $this->unit->value ?? 1;
        $qty = $this->return_qty / $value;
        return round($qty, 1);
    }

    public function getSubtotalAttribute()
    {

        $price      = $this->sell->unit_price ?? 0;
        $subtotal   = $price * $this->return_qty;
        return $subtotal;
    }
}
