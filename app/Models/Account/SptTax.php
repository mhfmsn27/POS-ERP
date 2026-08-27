<?php

namespace App\Models\Account;

use App\Models\Scopes\FilterByStores;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SptTax extends Model
{
    use HasFactory;

    protected $guarded          = [];

    protected static function booted()
    {
        parent::boot();
        static::addGlobalScope(new FilterByStores);

        static::creating(function ($model) {
            $model->store_id = my_store();
        });
    }

    public function detail()
    {
        return $this->hasMany(SptTaxDetail::class, 'spt_tax_id');
    }

    public function account_transaction()
    {
        return $this->hasMany(AccountTransaction::class, 'spt_taxes_id');
    }

   
}
