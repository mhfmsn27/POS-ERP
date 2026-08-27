<?php

namespace App\Models\Product;

use App\Models\Transaction\Transaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryLogStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'variation_id',
        'type', 
        'qty',
        'transaction_id',
        'item_id',
        'unit_id',
        'from',
        'to'
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id')->withTrashed();
    }

    public function variation()
    {
        return $this->belongsTo(Variation::class, 'variation_id')->withTrashed();
    }

    public function getProductNameAttribute()
    {
        $proname = $this->product->name ?? '';
        $varname = $this->variation->name ?? '';

        if ($varname == 'no-name') {
            $varname = '';
        }

        return $proname . ' ' . $varname;
    }

    public function getTypeNameAttribute()
    {
        $type = '';

        if ($this->type == 'purchase') {
            $type = "Pembelian Qty";
        } else if ($this->type == 'sell') {
            $type = "Penjualan Qty";
        } else if ($this->type == 'adjustment') {
            $type = "Stok Opname ( Pengurangan )";
        } else if ($this->type == 'transfer_out') {
            $type = "Transfer Stok Keluar";
        } else if ($this->type == 'transfer_int') {
            $type = "Transfer Stok Masuk";
        } else if ($this->type == 'expire') {
            $type = "Expire Stok";
        } else if ($this->type == 'return') {
            $type = "Return Penjualan Qty";
        } else if ($this->type == 'adjustment_add') {
            $type = "Stok Opname ( Penambahan )";
        }

        return $type;
    }

    public function getOperatorByTypeAttribute()
    {
        $type = 'add';

        if ($this->type == 'received_product') {
            $type = "add";
        } else if ($this->type == 'return_sell') {
            $type = "add";
        } else if ($this->type == 'return_transfer') {
            $type = "min";
        } else if ($this->type == 'return_transfer_received') {
            $type = "add";
        } else if ($this->type == 'adjustment') {
            $type = "min";
        } else if ($this->type == 'adjustment_add') {
            $type = "add";
        } else if ($this->type == 'purchase') {
            $type = "add";
        } else if ($this->type == 'return') {
            $type = "min";
        }  else if ($this->type == 'transfer_int') {
            $type = "add";
        }  else if ($this->type == 'transfer_out') {
            $type = "min";
        }  else if ($this->type == 'sell') {
            $type = "min";
        }  else if ($this->type == 'expire') {
            $type = "min";
        }  else if ($this->type == 'void_sale') {
            $type = "add";
        }  else if ($this->type == 'void_purchase') {
            $type = "min";
        }  else if ($this->type == 'shipping_product') {
            $type = "min";
        }

        return $type;
    }

}
