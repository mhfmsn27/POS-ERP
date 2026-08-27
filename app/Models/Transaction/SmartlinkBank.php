<?php

namespace App\Models\Transaction;

use App\Models\Account\Account;
use App\Models\Scopes\FilterByStores;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class SmartlinkBank extends Model
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
        'store_id',
        'type',
        'rekening',
        'account_id'
    ];

    protected static function booted()
    {
        parent::boot();
        static::addGlobalScope(new FilterByStores);

        static::creating(function ($model) {
            $model->store_id    = my_store();
            $model->id          = Uuid::uuid4()->toString();
        });
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
