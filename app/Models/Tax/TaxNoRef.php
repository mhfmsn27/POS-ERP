<?php

namespace App\Models\Tax;

use App\Models\Admin\Store;
use App\Models\Scopes\FilterByStores;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaxNoRef extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'from_number',
        'to_number',
        'type',
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

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function details()
    {
        return $this->hasMany(TaxNoRefDetail::class, 'tax_no_ref_id');
    }

    public function getStatusDataAttribute()
    {
        $totalNumber    = $this->details()->count();
        $totalUse       = $this->details()->where("transaction_id","!=",null)->count();

        if($totalUse == $totalNumber) {
            return true;
        }

        return false;
    }
}
