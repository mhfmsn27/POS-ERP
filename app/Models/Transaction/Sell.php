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
use Illuminate\Support\Facades\DB;

class Sell extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'transaction_id',
        'store_id',
        'product_id',
        'variation_id',
        'unit_qty',
        'qty',
        'qty_return',
        'unit_price',
        'unit_price_before_disc',
        'item_tax',
        'tax_id',
        'disc_type',
        'disc_amount',
        'unit_id',
        'transaction_received_id',
        'tax_total',
        'purchase_price',
        'goverment_tax',
        'service_tax',
        'discount_subtotal',
        'item_position',
        'item_name',
        'offer_id'
    ];

    protected static function booted()
    {
        parent::boot();
        static::addGlobalScope(new FilterByStores);

        static::creating(function ($model) {
            if (my_store() != null) {
                $model->store_id = my_store();
            }
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id')->withTrashed();
    }

    public function variation()
    {
        return $this->belongsTo(Variation::class, 'variation_id')->withTrashed();
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id')->withTrashed();
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id')->withTrashed();
    }

    public function transaction_shipping()
    {
        return $this->belongsTo(Transaction::class, 'transaction_received_id');
    }

    public function transaction_offer()
    {
        return $this->belongsTo(Transaction::class, 'offer_id');   
    }

    public function sale_account()
    {
        return $this->hasMany(AccountTransaction::class, 'item_id')->where("transaction_id", $this->transaction_id)->orWhere("transaction_id", $this->transaction_received_id);
    }

    public function sale_account_item()
    {
        return $this->hasMany(AccountTransaction::class, 'item_id')->where("transaction_id", $this->transaction_id);
    }

    public function sell_purchase()
    {
        return $this->hasMany(SellPurchase::class, 'sell_id')->orderBy("id", "desc");
    }

    public function getTotalModalPriceAttribute()
    {
        $modal = $this->purchase_price * ($this->qty - $this->qty_return);
        return $modal;
    }

    public function getQtyIntoUnitAttribute()
    {
        $value = $this->unit->value ?? 1;
        $qty = $this->qty / $value;
        return round($qty, 1);
    }

    public function getProfitSalesAttribute()
    {
        $jumlah = DB::table("sells as s")
            ->join('sell_purchases as sp', 's.id', '=', 'sp.sell_id')
            ->join('purchases as pp', 'sp.purchase_id', '=', 'pp.id')
            ->selectRaw("SUM(((s.qty - s.qty_return) * (s.unit_price_before_disc - (IFNULL(s.item_tax,0) / 100 * s.unit_price_before_disc))) - ((s.qty - s.qty_return) * pp.purchase_price) ) AS jumlah, 
        SUM(s.qty * pp.purchase_price) AS modal, SUM(s.qty * s.unit_price) AS harga_jual ")
            ->where("s.id", $this->id)
            ->first();
        return $jumlah->jumlah;
    }

    public function getSubtotalAttribute()
    {
        $subtotal = $this->unit_price * $this->qty;
        $taxTotal = $this->tax_total * $this->qty;
        return (float)($subtotal + $taxTotal);
    }

    public function getSubtotalAfterReturAttribute()
    {
        $subtotal   = $this->unit_price * ($this->qty - $this->qty_return);
        $taxTotal   = $this->tax_total * ($this->qty - $this->qty_return);
        return (float)($subtotal - $taxTotal);
    }

    public function getSubtotalWithoutTaxAttribute()
    {
        $subtotal   = $this->unit_price * ($this->qty - $this->qty_return);
        return (float)$subtotal;
    }

    public function getQtyCanReturnAttribute()
    {
        $total  = $this->qty - $this->qty_return;
        return (int)$total;
    }
}
