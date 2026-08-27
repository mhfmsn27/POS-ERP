<?php

namespace App\Models\Admin;

use App\Models\Product\Stock;
use App\Models\Scopes\FilterByStores;
use App\Models\Transaction\Transaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'address',
        'store_id'
    ];

    protected static function booted()
    {
        parent::boot();
        static::addGlobalScope(new FilterByStores);

        static::creating(function ($model) {
            $model->store_id = my_store();
        });
    }

    public function stock()
    {
        return $this->hasMany(Stock::class, 'warehouse_id');
    }

    public function transaction()
    {
        return $this->hasMany(Transaction::class, 'warehouse_id');
    }
}
