<?php

namespace App\Observers\Setting;

use App\Models\Admin\AccountSetting; 
use Illuminate\Http\Request;

class SettingAccountObserver
{
    public function getData()
    {
        return AccountSetting::first();
    }

    public function updateCrmAccount(Request $request)
    {
        $this->getData()->update([
            'customer_debt'                 => $request->customer_debt["id"],
            'customer_debt_imprest'         => $request->customer_debt_imprest["id"],
            'supplier_debt'                 => $request->supplier_debt["id"],
            'supplier_debt_imprest'         => $request->supplier_debt_imprest["id"],
            'beban_operasional'             => $request->beban_operasional_account['id'],
            'beban_lainnya'                 => $request->beban_lainnya_account['id'],
            'pendapatan_lainnya'            => $request->pendapatan_lainnya_account['id']
        ]);
    }

    public function updateProductAccount(Request $request)
    {
        $this->getData()->update([
            'product_supply'                => $request->product_supply["id"],
            'product_sale'                  => $request->product_sale["id"],
            'product_retur_sale'            => $request->product_retur_sale["id"],
            'product_discount_sale'         => $request->product_discount_sale["id"],
            'product_cost'                  => $request->product_cost["id"],
            'product_retur_purchase'        => $request->product_retur_purchase["id"],
        ]);
    }

    public function updateTransactionAccount(Request $request)
    {
        $this->getData()->update([
            'cost_shipping_transaction'     => $request->cost_shipping_transaction["id"],
            'salaries'                      => $request->salaries["id"],
            'kasbon'                        => $request->kasbon["id"],
            'discount_sale'                 => $request->discount_account['id'],
            'commission'                    => $request->commission_account['id']
        ]);
    }

    public function updateTaxAccount(Request $request)
    {
        $this->getData()->update([
            'tax_input'             => $request->tax_input_account["id"],
            'tax_output'            => $request->tax_output_account["id"],
            'tax_over'              => $request->tax_over_account["id"],
            'tax_minus'             => $request->tax_minus_account['id'],
            'pph_two_two'           => $request->tax_pph_account["id"],
            'pph_two_tree'          => $request->tax_service_account["id"],
        ]);
    }
}
