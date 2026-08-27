<?php

namespace App\Models\Rma;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RmaDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'rma_transactions_id',
        'product_name',
        'complaint',
        'completeness',
        'note',
        'status'
    ];

    public function transaction()
    {
        return $this->belongsTo(RmaTransaction::class, 'rma_transactions_id');
    }

    public function record()
    {
        return $this->hasMany(RmaRecord::class, 'rma_detail_id')->orderBy('created_at', 'desc');
    }
}
