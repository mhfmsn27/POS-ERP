<?php

namespace App\Models\Product;

use App\Models\Account\Account;
use App\Models\Admin\Taxrate;
use App\Models\Scopes\FilterByStores;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $guarded  = ['id'];
    public $table    = 'products';

    protected $fillable = [
        'id',
        'name',
        'sku',
        'barcode_type',
        'category_id',
        'brand_id',
        'alert_quantity',
        'type',
        'image',
        'is_stock',
        'price_type',
        'is_unit',
        'is_variant',
        'description',
        'weight',
        'is_account',
        'supply',
        'sale',
        'retur_sale',
        'discount_sale',
        'sent',
        'cost',
        'retur_purchase',
        'supplier_debt',
        'is_active'
    ];

    protected static function booted()
    {
        parent::boot();
        static::addGlobalScope(new FilterByStores);

        static::creating(function ($model) {
            if (empty($model->store_id) && my_store() != null) {
                $model->store_id = my_store();
            }
        });
    }

     /**
     * Log Activity
     */

     public function getActivitylogOptions(): LogOptions
     {
         return LogOptions::defaults()
             ->useLogName('product');
     }
 
     public function tapActivity(Activity $activity, string $eventName)
     {

        $activity->store_id = my_store();
        
         if ($eventName == 'created') {
             $activity->description = "Penambahan Produk  " . $activity->subject->name ?? '';
         }
 
         if ($eventName == 'updated') {
             $activity->description = "Edit Produk " . $activity->subject->name ?? '';
         }
 
         if ($eventName == 'deleted') {
             $activity->description = "Hapus Produk  " . $activity->subject->name ?? '';
         }
     }
 
     // End Log Activity

    public function variant()
    {
        return $this->hasMany(Variation::class, 'product_id');
    }

    public function single_variant()
    {
        return $this->hasOne(Variation::class, 'product_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id')->withTrashed();
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id')->withTrashed();
    }

    public function tax()
    {
        return $this->belongsTo(Taxrate::class, 'tax_id')->withTrashed();
    }

    public function stock()
    {
        return $this->hasMany(Stock::class, 'product_id')->where('store_id', my_store());
    }

    public function satuan()
    {
        return $this->hasMany(Unit::class, 'product_id')->where("type", "product");
    }

    public function image_default()
    {
        return $this->hasOne(Media::class, 'imageable_id')->where("imageable_type", "App\Models\Product\Product");
    }

    public function allstock()
    {
        return $this->hasMany(Stock::class, 'product_id');
    }

    public function gallery()
    {
        return $this->hasMany(Media::class, 'imageable_id')->where("imageable_type", "App\Models\Product\Product");
    }

    public function supply_account()
    {
        return $this->belongsTo(Account::class, 'supply')->withTrashed();
    }

    public function sale_account()
    {
        return $this->belongsTo(Account::class, 'sale')->withTrashed();
    }

    public function return_sale_account()
    {
        return $this->belongsTo(Account::class, 'retur_sale')->withTrashed();
    }

    public function discount_account()
    {
        return $this->belongsTo(Account::class, 'discount_sale')->withTrashed();
    }

    public function sent_account()
    {
        return $this->belongsTo(Account::class, 'sent')->withTrashed();
    }

    public function cost_account()
    {
        return $this->belongsTo(Account::class, 'cost')->withTrashed();
    }

    public function retur_purchase_account()
    {
        return $this->belongsTo(Account::class, 'retur_purchase')->withTrashed();
    }

    public function supplier_debt_account()
    {
        return $this->belongsTo(Account::class, 'supplier_debt')->withTrashed();
    }

    public function history()
    {
        return $this->hasMany(HistoryLogStock::class,'product_id');
    }

    public function getPriceSellRangeAttribute()
    {
        $min = $this->variant()->get()->min('selling_price');
        $max = $this->variant()->get()->max('selling_price');
        if ($min != $max) {
            return number_format($min) . ' - ' . number_format($max);
        } else {
            return number_format($min);
        }
    }

    public function getPriceGrosirAttribute()
    {
        $min = $this->variant()->get()->min('grocery');
        $max = $this->variant()->get()->max('grocery');

        if ($max == 0) {
            $min = $this->variant()->get()->min('selling_price');
            $max = $this->variant()->get()->max('selling_price');
        }

        if ($min != $max) {
            return number_format($min) . ' - ' . number_format($max);
        } else {
            return number_format($min);
        }
    }

    public function getPricePurchaseRangeAttribute()
    {
        $min = $this->variant()->get()->min('purchase_price');
        $max = $this->variant()->get()->max('purchase_price');
        if ($min != $max) {
            return number_format($min) . ' - ' . number_format($max);
        } else {
            return number_format($min);
        }
    }

    public function getStockTotalAttribute()
    {
        $total = $this->stock()->where(function ($query) {
            return Auth::user()->store_id != 0 ? $query->where('store_id', Auth::user()->store_id) : '';
        })->get()->sum('qty_available');
        return $total;
    }

    public function getDefaultImageAttribute()
    {
        $image = $this->image_default->path ?? null;

        if ($image == null) {
            $image = 'uploads/image-default.jpeg';
        } else {
            if (file_exists($image)) {
                return $image;
            } else {
                return 'uploads/image-default.jpeg';
            }
        }

        return $image;
    }

    public function stock_in_website()
    {
        return $this->hasMany(Stock::class, 'product_id')->where('store_id', Session::get('dfstore'));
    }

    public function general_store()
    {
        return $this->hasMany(Stock::class, 'product_id');
    }

}
