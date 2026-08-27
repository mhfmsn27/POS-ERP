<?php

namespace App\Models\Product;

use App\Models\Admin\Store;
use App\Models\Admin\Warehouse;
use App\Models\Scopes\FilterByStores;
use App\Models\Stock\StockTransferDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded  = ['id'];
    public $table    = 'stocks';

    protected $fillable = [
        'qty_available',
        'product_id',
        'variation_id',
        'store_id', 
        'warehouse_id'
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

    public function variation()
    {
        return $this->belongsTo(Variation::class, 'variation_id')->withTrashed();
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id')->withTrashed();
    }


    public function transferdetail()
    {
        return $this->hasMany(StockTransferDetail::class, 'stock_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class,'warehouse_id');
    }

    public function qty_transfer_stock($store)
    {
        $total = $this->transferdetail()->where('from', $store)->get()->sum('transfer_qty');
        return $total;
    }
}
