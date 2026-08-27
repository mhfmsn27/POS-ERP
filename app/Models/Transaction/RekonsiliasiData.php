<?php

namespace App\Models\Transaction;

use App\Models\Account\Account;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class RekonsiliasiData extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'transaction_account_id',
        'amount',
        'saldo',
        'account_id',
        'date',
        'note',
        'type',
        'status'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->id          = Uuid::uuid4()->toString();
        });
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
