<?php

namespace App\Models\Account;

use App\Models\Scopes\FilterByStores;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccountType extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'edit_option',
        'coa_code',
        'with_price',
        'with_modal',
        'store_id',
        'type',
        'default'
    ];

    protected static function booted()
    {
        parent::boot();
        static::addGlobalScope(new FilterByStores);

        static::creating(function ($model) {
            if (my_store()) {
                $model->store_id = my_store();
            }
        });
    }

    public function account()
    {
        return $this->hasMany(Account::class, 'account_type_id');
    }
}
