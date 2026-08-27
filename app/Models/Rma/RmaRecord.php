<?php

namespace App\Models\Rma;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RmaRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'rma_transactions_id',
        'rma_detail_id',
        'subject',
        'type',
        'note',
    ];

    public function detail()
    {
        return $this->belongsTo(RmaDetail::class,'rma_detail_id');
    }

    public function getStatusNameAttribute()
    {
        if($this->type == 'note') {
            return 'Catatan';
        }

        if($this->type == 'process') {
            return 'Dalam Proses';
        }

        if($this->type == 'complete') {
            return 'Selesai';
        }

        if($this->type == 'taken') {
            return 'Di Ambil';
        }
    }
}
