<?php

namespace App\Models\Stock;

use App\Models\Product\Product;
use App\Models\Product\Unit;
use App\Models\Product\Variation;
use App\Models\Transaction\Transaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockAdjusmentDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'store_id',
        'product_id',
        'variation_id',
        'ingridient_id', 
        'qty_adjustment',
        'type_adjustment',
        'stock_sistem',
        'actual_stock',
        'unit_price',
        'unit_id',
        'unit_qty',
        'note', 
        'purchase_price'
    ];


    public function transaction()
    {
        return $this->belongsTo(Transaction::class,'transaction_id')->withTrashed();
    }

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id')->withTrashed();
    }

    public function variation()
    {
        return $this->belongsTo(Variation::class,'variation_id')->withTrashed();
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class,'unit_id')->withTrashed();
    }

    public function getQtyIntoUnitAttribute()
    {
        $value = $this->unit->value ?? 1; 
        $qty = $this->qty_adjustment / $value;
        return round($qty,1);
    }

    public function getSubtotalAttribute()
    {
        $total = $this->unit_price * $this->qty_adjustment;
        return $total;
    }
}
