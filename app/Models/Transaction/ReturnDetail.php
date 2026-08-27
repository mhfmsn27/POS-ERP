<?php

namespace App\Models\Transaction;

use App\Models\Product\Unit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'purchase_id',
        'return_qty',
        'unit_id',
        'unit_qty',
        'price',
        'tax_total'
    ];
    

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id')->withTrashed();
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id')->withTrashed();
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
        $subtotal   = $this->price * $this->return_qty;
        $taxTotal   = $this->tax_total * $this->return_qty;
        return (float)($subtotal + $taxTotal);
    }
}
