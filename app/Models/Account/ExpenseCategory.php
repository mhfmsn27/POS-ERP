<?php

namespace App\Models\Account;

use App\Models\Scopes\FilterByStores;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $casts = [
        'is_root_parent' => 'boolean'
    ];

    protected $fillable = [
        'name',
        'detail',
    ];

    protected static function booted()
    {
        parent::boot();
        static::addGlobalScope(new FilterByStores);

        static::creating(function ($model) {
            $model->store_id = my_store();
        });
    }

    /**
     * Get category childs
     */
    public function children()
    {
        return $this->hasMany(ExpenseCategory::class, 'parent_id')->where('is_root_parent', false);
    }

    /**
     * Get category parents
     */
    public function parent()
    {
        return $this->belongsTo(ExpenseCategory::class, 'parent_id');
    }

    /**
     * Get Expense Or Cash Int Cash Out
     */
    public function expense()
    {
        return $this->hasMany(Expense::class, 'category_id');
    }
}
