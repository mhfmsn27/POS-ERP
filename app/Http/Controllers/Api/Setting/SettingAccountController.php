<?php

namespace App\Http\Controllers\Api\Setting;

use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\AccountSettingCrmRequest;
use App\Http\Requests\Setting\AccountSettingProductRequest;
use App\Http\Requests\Setting\AccountSettingTransactionRequest;
use App\Observers\Setting\SettingAccountObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SettingAccountController extends Controller
{
    protected $settingAccountObserver;

    public function __construct(SettingAccountObserver $settingAccountObserver)
    {
        $this->settingAccountObserver   = $settingAccountObserver;
    }

    public function index()
    {
        $data   = $this->settingAccountObserver->getData();

        return response()->json([
            'detail'   => array(
                'customer_debt'                 => array(
                    "id"                => $data->customer_debt,
                    "name"              => $data->customer_debt_account->name ?? ''
                ),
                'customer_debt_imprest'         => array(
                    "id"                => $data->customer_debt_imprest,
                    "name"              => $data->customer_debt_imprest_account->name ?? ""
                ),
                'supplier_debt'                 => array(
                    "id"                => $data->supplier_debt_account->id ?? '',
                    "name"              => $data->supplier_debt_account->name ?? ''
                ),
                'supplier_debt_imprest'         => array(
                    "id"                => $data->supplier_debt_imprest_account->id ?? "",
                    "name"              => $data->supplier_debt_imprest_account->name ?? ""
                ),
                'product_supply'                => array(
                    "id"                => $data->product_supply_account->id ?? "",
                    "name"              => $data->product_supply_account->name ?? ""
                ),
                'product_sale'                  => array(
                    "id"                => $data->product_sale_account->id ?? "",
                    "name"              => $data->product_sale_account->name ?? ""
                ),
                'product_retur_sale'            => array(
                    "id"                => $data->product_retur_sale_account->id ?? "",
                    "name"              => $data->product_retur_sale_account->name ?? ""
                ),
                'product_discount_sale'         => array(
                    "id"                => $data->product_discount_sale_account->id ?? "",
                    "name"              => $data->product_discount_sale_account->name ?? ""
                ),
                'product_cost'                  => array(
                    "id"                => $data->product_cost_account->id ?? "",
                    "name"              => $data->product_cost_account->name ?? ""
                ),
                'product_retur_purchase'        => array(
                    "id"                => $data->product_retur_purchase_account->id ?? "",
                    "name"              => $data->product_retur_purchase_account->name ?? ""
                ),
                'cost_shipping_transaction'     => array(
                    "id"                => $data->transaction_shipping_account->id ?? "",
                    "name"              => $data->transaction_shipping_account->name ?? ""
                ),
                'salaries'                  => array(
                    "id"                => $data->salary_account->id ?? "",
                    "name"              => $data->salary_account->name ?? ""
                ),
                'kasbon'                    => array(
                    "id"                => $data->kasbon_account->id ?? "",
                    "name"              => $data->kasbon_account->name ?? ""
                ),
                'discount_account'          => array(
                    'id'                => $data->discount_account->id ?? '',
                    'name'              => $data->discount_account->name ?? ''
                ),
                'commission_account'        => array(
                    'id'                => $data->commission_account->id ?? '',
                    'name'              => $data->commission_account->name ?? ''
                ),
                'tax_input_account'         => array(
                    "id"                => $data->tax_input_account->id ?? "",
                    "name"              => $data->tax_input_account->name ?? ""
                ),
                'tax_output_account'        => array(
                    'id'                => $data->tax_output_account->id ?? '',
                    'name'              => $data->tax_output_account->name ?? ''
                ),
                'tax_over_account'           => array(
                    'id'                => $data->tax_over_account->id ?? '',
                    'name'              => $data->tax_over_account->name ?? ''
                ),
                'tax_minus_account'           => array(
                    'id'                => $data->tax_minus_account->id ?? '',
                    'name'              => $data->tax_minus_account->name ?? ''
                ),
                'tax_pph_account'           => array(
                    'id'                => $data->tax_pph_account->id ?? '',
                    'name'              => $data->tax_pph_account->name ?? ''
                ),
                'tax_service_account'           => array(
                    'id'                => $data->tax_service_account->id ?? '',
                    'name'              => $data->tax_service_account->name ?? ''
                ),
                'beban_operasional_account'     => array(
                    'id'                => $data->beban_operasional_account->id ?? '',
                    'name'              => $data->beban_operasional_account->name ?? ''
                ),
                'beban_lainnya_account'           => array(
                    'id'                => $data->beban_lainnya_account->id ?? '',
                    'name'              => $data->beban_lainnya_account->name ?? ''
                ),
                'pendapatan_lainnya_account'       => array(
                    'id'                => $data->pendapatan_lainnya_account->id ?? '',
                    'name'              => $data->pendapatan_lainnya_account->name ?? ''
                )
            ),
            'status'   => true
        ], 200);
    }

    public function updateCrm(AccountSettingCrmRequest $request)
    {

        abort_if(Gate::denies('account_crm'), 403);

        try {

            $this->settingAccountObserver->updateCrmAccount($request);

            return response()->json([
                'message'   => 'Data berhasil di perbaharui',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => false
            ], 422);
        }
    }


    public function updateProduct(AccountSettingProductRequest $request)
    {

        abort_if(Gate::denies('account_product'), 403);

        try {

            $this->settingAccountObserver->updateProductAccount($request);

            return response()->json([
                'message'   => 'Data berhasil di perbaharui',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => false
            ], 422);
        }
    }

    public function updateTransaction(AccountSettingTransactionRequest $request)
    {

        abort_if(Gate::denies('account_transaction'), 403);

        try {

            $this->settingAccountObserver->updateTransactionAccount($request);

            return response()->json([
                'message'   => 'Data berhasil di perbaharui',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => false
            ], 422);
        }
    }

    public function updateTaxrate(Request $request)
    {

        abort_if(Gate::denies('account_tax'), 403);

        try {

            $this->settingAccountObserver->updateTaxAccount($request);

            return response()->json([
                'message'   => 'Data berhasil di perbaharui',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message'   => $e->getMessage(),
                'status'    => false
            ], 422);
        }
    }
}
