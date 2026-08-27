<?php

namespace App\Models\Admin;

use App\Models\Account\Account;
use App\Models\Scopes\FilterByStores;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountSetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'store_id',
        'customer_debt',
        'customer_debt_imprest',
        'supplier_debt',
        'supplier_debt_imprest',
        'product_supply',
        'product_sale',
        'product_retur_sale',
        'product_discount_sale',
        'product_sent',
        'product_cost',
        'product_retur_purchase',
        'product_supplier_debt',
        'cost_shipping_transaction',
        'salaries',
        'kasbon',
        'discount_sale',
        'commission',
        'tax_input',
        'tax_output',
        'tax_gap',
        'pph_two_two',
        'pph_two_tree',
        'beban_operasional',
        'beban_lainnya',
        'pendapatan_lainnya',
        'tax_minus',
        'tax_over'
    ];

    protected static function booted()
    {
        parent::boot();
        static::addGlobalScope(new FilterByStores);

        static::creating(function ($model) {
            $model->store_id = my_store();
        });
    }

    public function customer_debt_account()
    {
        return $this->belongsTo(Account::class, 'customer_debt');
    }

    public function customer_debt_imprest_account()
    {
        return $this->belongsTo(Account::class, 'customer_debt_imprest');
    }

    public function supplier_debt_account()
    {
        return $this->belongsTo(Account::class, 'supplier_debt');
    }

    public function supplier_debt_imprest_account()
    {
        return $this->belongsTo(Account::class, 'supplier_debt_imprest');
    }

    public function product_supply_account()
    {
        return $this->belongsTo(Account::class, 'product_supply');
    }

    public function product_sale_account()
    {
        return $this->belongsTo(Account::class, 'product_sale');
    }

    public function product_retur_sale_account()
    {
        return $this->belongsTo(Account::class, 'product_retur_sale');
    }

    public function product_discount_sale_account()
    {
        return $this->belongsTo(Account::class, 'product_discount_sale');
    }

    public function product_sent_account()
    {
        return $this->belongsTo(Account::class, 'product_sent');
    }

    public function product_cost_account()
    {
        return $this->belongsTo(Account::class, 'product_cost');
    }

    public function product_retur_purchase_account()
    {
        return $this->belongsTo(Account::class, 'product_retur_purchase');
    }

    public function product_supplier_debt_account()
    {
        return $this->belongsTo(Account::class, 'product_supplier_debt');
    }

    public function transaction_shipping_account()
    {
        return $this->belongsTo(Account::class, 'cost_shipping_transaction');
    }

    public function salary_account()
    {
        return $this->belongsTo(Account::class, 'salaries');
    }

    public function kasbon_account()
    {
        return $this->belongsTo(Account::class, 'kasbon');
    }

    public function discount_account()
    {
        return $this->belongsTo(Account::class, 'discount_sale');
    }

    public function commission_account()
    {
        return $this->belongsTo(Account::class, 'commission');
    }

    public function tax_input_account()
    {
        return $this->belongsTo(Account::class, 'tax_input');
    }

    public function tax_output_account()
    {
        return $this->belongsTo(Account::class, 'tax_output');
    }

    public function tax_over_account()
    {
        return $this->belongsTo(Account::class, 'tax_over');
    }

    public function tax_minus_account()
    {
        return $this->belongsTo(Account::class, 'tax_minus');
    }

    public function tax_pph_account()
    {
        return $this->belongsTo(Account::class, 'pph_two_two');
    }

    public function tax_service_account()
    {
        return $this->belongsTo(Account::class, 'pph_two_tree');
    }

    public function beban_operasional_account()
    {
        return $this->belongsTo(Account::class, 'beban_operasional');
    }

    public function beban_lainnya_account()
    {
        return $this->belongsTo(Account::class, 'beban_lainnya');
    }

    public function pendapatan_lainnya_account()
    {
        return $this->belongsTo(Account::class, 'pendapatan_lainnya');
    }
}
