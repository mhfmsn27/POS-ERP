<?php

namespace App\Models\Tax;

use App\Models\Transaction\Transaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxNoRefDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'tax_no_ref_id',
        'transaction_id',
        'number'
    ];

    public function tax_ref()
    {
        return $this->belongsTo(TaxNoRef::class, 'tax_no_ref_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class,'transaction_id');
    }
    
}
