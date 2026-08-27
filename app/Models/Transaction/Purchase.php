<?php

namespace App\Models\Transaction;

use App\Models\Account\AccountTransaction;
use App\Models\Product\Product;
use App\Models\Product\Unit;
use App\Models\Product\Variation;
use App\Models\Scopes\FilterByStores;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'transaction_received_id',
        'transaction_id',
        'store_id',
        'product_id',
        'variation_id',
        'unit_qty',
        'quantity',
        'discount_percent',
        'purchase_price',
        'without_discount',
        'purchase_price_inc_tax',
        'qty_return',
        'discount_type',
        'item_tax',
        'publish',
        'tax_id',
        'unit_id',
        'expire_date',
        'no_batch',
        'tax_total',
        'other_cost',
        'qty_sold',
        'po_id'

    ];

    protected static function booted()
    {
        parent::boot();
        static::addGlobalScope(new FilterByStores);

        static::creating(function ($model) {
            $model->store_id = my_store();
        });
    }


    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id')->withTrashed();
    }

    public function transaction_received()
    {
        return $this->belongsTo(Transaction::class, 'transaction_received_id')->withTrashed();
    } 

    public function po()
    {
        return $this->belongsTo(Transaction::class, 'po_id')->withTrashed();
    }

    public function variation()
    {
        return $this->belongsTo(Variation::class, 'variation_id')->withTrashed();
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id')->withTrashed();
    }

    public function purchase_account()
    {
        return $this->hasMany(AccountTransaction::class, 'item_id')->where("transaction_id", $this->transaction_id)->orWhere("transaction_id", $this->transaction_received_id);
    }

    public function purchase_account_item()
    {
        return $this->hasMany(AccountTransaction::class, 'item_id')->where("transaction_id", $this->transaction_id);
    }

    public function quantity_remaining($id)
    {
        $data = Purchase::findOrFail($id);
        $quantity = ($data->quantity - $data->qty_sold) - $data->qty_adjusted - $data->qty_return;
        return $quantity;
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id')->withTrashed();
    }

    public function sell_purchase()
    {
        return $this->hasMany(SellPurchase::class, 'purchase_id')->orderBy("id", "desc");
    }

    public function sell_purchase_first()
    {
        return $this->hasOne(SellPurchase::class, 'purchase_id');
    }

    public function getQtyIntoUnitAttribute()
    {
        $value = $this->unit->value ?? 1;
        $qty = $this->quantity / $value;
        return round($qty, 1);
    }

    public function getSubtotalAttribute()
    {
        $subtotal = $this->purchase_price_inc_tax * $this->quantity;
        return (int)$subtotal;
    }

    public function getQtyCanReturnAttribute()
    {
        $total  = $this->quantity - $this->qty_return;
        return (int)$total;
    }

    public function getModalPriceAttribute()
    {
        $total      = $this->purchase_price * $this->quantity;
        return (float)$total;
    }
}
