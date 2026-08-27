<?php

namespace App\Models\Account;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SptTaxDetail extends Model
{
    use HasFactory;

    protected $guarded          = [];

    public function getSptNameAttribute()
    {
        if ($this->transaction_type == 'purchase') {
            return 'PEMBELIAN';
        }

        if ($this->transaction_type == 'purchase_return') {
            return 'RETUR PEMBELIAN';
        }

        if ($this->transaction_type == 'sell') {
            return 'PENJUALAN';
        }

        if ($this->transaction_type == 'sales_return') {
            return 'RETUR PENJUALAN';
        }
    }
}
