<?php

namespace App\Models\Product;

use App\Models\Admin\Setting;
use App\Models\Transaction\Purchase;
use App\Models\Transaction\Sell;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Variation extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $guarded  = ['id'];

    public $table    = 'variations';

    protected $casts = [
        'price_inc_tax'     => 'float',
        'selling_price'     => 'float',
        'purchase_price'    => 'float',
        'grocery'           => 'float',
        'margin_grocery'    => 'integer',
    ];

    protected $fillable = [
        'id',
        'product_id',
        'sku',
        'price_inc_tax',
        'purchase_price',
        'name',
        'selling_price',
        'margin',
        'unit_id',
        'rak_id',
        'taxrate',
        'tax_type',
        'barcode',
        'unit_sale',
        'unit_purchase',
        'point',
        'get_point',
        'grocery',
        'purchase_tax',
        'tax_sell',
        'tax_purchase'
    ];

    /**
     * Log Activity
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('variation');
    }

    public function tapActivity(Activity $activity, string $eventName)
    {

        $activity->store_id = my_store();
        
        $productName = $activity->subject->product->name ?? '';
        $variantName = $activity->subject->name ?? '';
        if ($variantName == 'no-name') {
            $variantName = '';
        }
        if ($eventName == 'created') {

            $activity->description = "Penambahan Variant  " . $productName . ' ' . $variantName;
        }

        if ($eventName == 'updated') {
            $activity->description = "Edit Variant " . $productName . ' ' . $variantName;
        }

        if ($eventName == 'deleted') {
            $activity->description = "Hapus Variant  " . $productName . ' ' . $variantName;
        }
    }


    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id')->withTrashed();
    }

    public function check()
    {
        return $this->hasMany(Media::class, 'imageable_id');
    }

    public function stock()
    {
        return $this->hasMany(Stock::class, 'variation_id')->where('store_id', my_store())->where("warehouse_id", null);
    }

    public function stock_by_warehouse($warehouseID)
    {
        return $this->hasMany(Stock::class, 'variation_id')->where("warehouse_id", $warehouseID);
    }

    public function sales()
    {
        return $this->hasMany(Sell::class, 'variation_id');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'variation_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id')->withTrashed();
    }

    public function unitpo()
    {
        return $this->belongsTo(Unit::class, 'unit_purchase')->withTrashed();
    }

    public function unit_sell()
    {
        return $this->belongsTo(Unit::class, 'unit_sale')->withTrashed();
    }

    public function multiprice()
    {
        return $this->hasMany(ProductDiscount::class, 'variation_id')->where("type", "multiprice")->select(["id", "qty_min", "discount_amount", "amount_type"]);
    }

    public function harga_modal()
    {
        return $this->hasOne(PriceVariationStore::class, 'variation_id')->where('store_id', my_store());
    }

    public function stock_in_website()
    {
        return $this->hasMany(Stock::class, 'variation_id')->where('store_id', Session::get('dfstore'));
    }

    public function all_stock()
    {
        return $this->hasMany(Stock::class, 'variation_id');
    }

    public function open_stock()
    {
        return $this->hasOne(Purchase::class, 'variation_id')->whereHas('transaction', function ($q) {
            return $q->where("type", "open_stock");
        });
    }


    public function getStockTotalAttribute()
    {
        $total = $this->stock()->get()->sum('qty_available');
        return $total;
    }


    public function purchases_stock($store)
    {
        $get = $this->purchases()->where('store_id', $store)->get()->sum('quantity');
        return number_format($get);
    }

    public function expire_stock($store)
    {
        $get = 0;
        if ($this->purchases != null) {
            $get = $this->purchases()->where("store_id", $store)->get()->sum("qty_expire");
        }

        return number_format($get);
    }

    public function sell_stock($store)
    {
        $get = $this->sales()->where('store_id', $store)->get()->sum('qty');
        return number_format($get);
    }

    public function return_sell_stock($store)
    {
        $get = $this->sales()->where('store_id', $store)->get()->sum('qty_return');
        return number_format($get);
    }

    public function return_purchase_stock($store)
    {
        $get = $this->purchases()->where('store_id', $store)->get()->sum('qty_return');
        return number_format($get);
    }

    public function transfer_stock_out($store)
    {
        $getdata = Stock::join('stock_transfer_details as st', 'st.stock_id', "=", "stocks.id")
            ->where("stocks.variation_id", $this->id)->where('st.from', $store)->sum('st.transfer_qty');
        return $getdata;
    }

    public function transfer_stock_entry($store)
    {
        $getdata = Stock::join('stock_transfer_details as st', 'st.stock_id', "=", "stocks.id")
            ->where("stocks.variation_id", $this->id)->where('st.to', $store)->sum('st.transfer_qty');
        return $getdata;
    }

    public function getModalPriceAttribute()
    {

        $purchasePrice = $this->purchase_price;

        if ($this->harga_modal) {
            if (Setting::first(['stocking_system_type'])->stocking_system_type == 'averaging') {
                $purchasePrice = $this->harga_modal->price ?? 0;
            }
        }


        return (int)$purchasePrice;
    }

    public function getFullNameAttribute()
    {
        $pName  = $this->product->name ?? '';
        $vName  = $this->name == 'no-name' ? '' : $this->name;

        return $pName . ' ' . $vName;
    }

    public function getFirstStockAttribute()
    {
        if ($this->open_stock) {
            return (int)$this->open_stock->quantity;
        }

        return 0;
    }

    public function getTaxVariationAttribute()
    {
        if ($this->tax_sell == 'yes' && $this->getModalPriceAttribute() > 0) {

            $taxPrice = 11 / 100 * $this->getModalPriceAttribute();
            if ($taxPrice > 0) {
                return (float)$taxPrice;
            }
        }

        return 0;
    }

    public function getFirstPriceAttribute()
    {
        if ($this->open_stock) {
            return (float)$this->open_stock->purchase_price;
        }

        return (float)$this->purchase_price;
    }
}
