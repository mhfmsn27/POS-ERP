<?php

namespace App\Observers\Saas;

use App\Models\Account\Account;
use App\Models\Account\AccountType;
use App\Models\Admin\AccountSetting;
use App\Models\Admin\Store;
use Illuminate\Http\Request;

class StoreObserver
{

    public function getData(Request $request)
    {
        return Store::where(function ($q) use ($request) {
            return $request->name ? $q->where('name', 'like', '%' . $request->name . '%') : '';
        })->where(function ($q) use ($request) {
            return $request->merchant ? $q->where('merchant_id', $request->merchant) : '';
        })->where(function ($q) use ($request) {
            if ($request->end_date && $request->start_date) {
                return $q->whereBetween('created_at', [$request->start_date, now()->parse($request->end_date)->addDay()]);
            } else {
                return $request->start_date ? $q->whereDate("created_at", $request->start_date) : "";
            }
        })->orderBy('name', 'asc');
    }

    public function createData(Request $request, $merchantId = null)
    {
        $store = Store::create([
            'country_id'        => 1,
            'currency_id'       => 54,
            'name'              => $request->name,
            'printer_id'        => !empty($request->printer_id) ? $request->printer_id : null,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'zip_code'          => !empty($request->zip_code) ? $request->zip_code : null,
            'address'           => $request->address,
            'accountant_use'    => $request->accountant_use,
            'shift_register'    => !empty($request->shift_register) ? $request->shift_register : 'active',
            'tax_option'        => $request->tax_option == 'yes' ? 'active' : $request->tax_option,
            'merchant_id'       => $merchantId != null ? $merchantId : ((auth()->check() && auth()->user()->merchant_id) ? auth()->user()->merchant_id : 1),
            'warehouse_default_id'  => !empty($request->warehouse_default_id) ? $request->warehouse_default_id : null,
            'tax_one'           => $request->tax_option == 'active' || $request->tax_option == 'yes' && !empty($request->tax_one) ? $request->tax_one : 0,
            'tax_two'           => $request->tax_option == 'active' || $request->tax_option == 'yes' && !empty($request->tax_two) ? $request->tax_two : 0,
            'tax_tree'          => $request->tax_option == 'active' || $request->tax_option == 'yes' && !empty($request->tax_tree) ? (float)preg_replace("/[^0-9\.]/", ".", $request->tax_tree) : 0,
            'currency_position' => 1,
        ]);

        session()->put('mystore', $store->id);

        if ($store->accountant_use == 'yes') {
            $this->generateAccountType($store);
            $this->generateAccounts($store);

            $debtc      = Account::where('store_id',$store->id)->where("coa", "110301")->first(['id']);
            $debts      = Account::where('store_id',$store->id)->where("coa", "210101")->first(['id']);
            $supply     = Account::where('store_id',$store->id)->where("coa", "110401")->first(['id']);
            $sale       = Account::where('store_id',$store->id)->where("coa", "400001")->first(['id']);
            $return     = Account::where('store_id',$store->id)->where("coa", "400003")->first(['id']);
            $discount   = Account::where('store_id',$store->id)->where("coa", "400004")->first(['id']);
            $cost       = Account::where('store_id',$store->id)->where("coa", "5000")->first(['id']);

            $shipping       = Account::where('store_id',$store->id)->where("coa", "110301")->first(['id']);
            $salary         = Account::where('store_id',$store->id)->where("coa", "600001")->first(['id']);
            $kasbon         = Account::where('store_id',$store->id)->where("coa", "110305")->first(['id']);
            $commission     = Account::where('store_id',$store->id)->where("coa", "600006")->first(['id']);
            $taxinput       = Account::where('store_id',$store->id)->where("coa", "110503")->first(['id']);
            $taxoutput      = Account::where('store_id',$store->id)->where("coa", "210201")->first(['id']);

            $bebanOperasional   = Account::where('store_id',$store->id)->where("coa", "6000")->first(['id']);
            $otherBeban         = Account::where('store_id',$store->id)->where("coa", "7200")->first(['id']);
            $otherIncome        = Account::where('store_id',$store->id)->where("coa", "7100")->first(['id']);

            $pph22          = Account::where('store_id',$store->id)->where("coa", "411101")->first(['id']);
            $pph23          = Account::where('store_id',$store->id)->where("coa", "110504")->first(['id']);
            $taxOver        = Account::where('store_id',$store->id)->where("coa", "110605")->first(['id']);
            $taxMinus       = Account::where('store_id',$store->id)->where("coa", "600017")->first(['id']);

            $accountSetting = [
                [
                    'store_id'                      => $store->id,
                    'customer_debt'                 => $debtc->id,
                    'customer_debt_imprest'         => $debtc->id,
                    'supplier_debt'                 => $debts->id,
                    'supplier_debt_imprest'         => $debts->id,

                    'product_supply'                => $supply->id,
                    'product_sale'                  => $sale->id,
                    'product_retur_sale'            => $return->id,
                    'product_discount_sale'         => $discount->id,
                    'product_cost'                  => $cost->id,
                    'product_retur_purchase'        => $sale->id,

                    'cost_shipping_transaction'     => $shipping->id,
                    'salaries'                      => $salary->id,
                    'kasbon'                        => $kasbon->id,
                    'discount_sale'                 => $discount->id,
                    'commission'                    => $commission->id,
                    'tax_input'                     => $taxinput->id,
                    'tax_output'                    => $taxoutput->id,

                    'pph_two_two'                   => $pph22->id,
                    'pph_two_tree'                  => $pph23->id,
                    'tax_over'                      => $taxOver->id,
                    'tax_minus'                     => $taxMinus->id,

                    // other
                    'beban_operasional'             => $bebanOperasional->id,
                    'beban_lainnya'                 => $otherBeban->id,
                    'pendapatan_lainnya'            => $otherIncome->id
                ]
            ];

            AccountSetting::insert($accountSetting);
        }

        return $store;
    }

    public function updateData(Request $request, Store $store, String $image = '')
    {

        $store->update([
            'country_id'        => 1,
            'currency_id'       => 54,
            'name'              => $request->name,
            'printer_id'        => !empty($request->printer_id) ? $request->printer_id : null,
            'email'             => $request->email,
            'phone'             => $request->phone,
            'zip_code'          => !empty($request->zip_code) ? $request->zip_code : null,
            'address'           => $request->address,
            'shift_register'    => !empty($request->shift_register) ? $request->shift_register : 'active',
            'tax_option'        => $request->tax_option == 'yes' ? 'active' : $request->tax_option,
            'warehouse_default_id'  => !empty($request->warehouse_default_id) ? $request->warehouse_default_id : null,
            'tax_one'           => $request->tax_option == 'active' || $request->tax_option == 'yes' && !empty($request->tax_one) ? $request->tax_one : 0,
            'tax_two'           => $request->tax_option == 'active' || $request->tax_option == 'yes' && !empty($request->tax_two) ? $request->tax_two : 0,
            'tax_tree'          => $request->tax_option == 'active' || $request->tax_option == 'yes' && !empty($request->tax_tree) ? (float)preg_replace("/[^0-9\.]/", ".", $request->tax_tree) : 0,
            'currency_position' => 1,
            'logo'              => $image != '' ? $image : $store->logo,
            'footer_text'       => !empty($request->footer_text) ? $request->footer_text : null,
        ]);

        return $store;
    }

    public function generateAccountType(Store $store)
    {
        $data = [
            [
                'name'      => 'Kas & Bank',
                'store_id'   => $store->id,
                'coa_code'  => '1101',
                'with_price'    => 'yes',
                'with_modal'    => 'yes',
                'type'      => 'bank_cash',
                'default'   => null,
            ],
            [
                'name'      => 'Setara Kas',
                'store_id'   => $store->id,
                'coa_code'  => '1102',
                'with_price'    => 'yes',
                'with_modal'    => 'yes',
                'type'          => 'bank_cash',
                'default'   => null,
            ],
            [
                'name'      => 'Piutang Usaha',
                'store_id'   => $store->id,
                'coa_code'  => '1103',
                'with_price'    => 'yes',
                'with_modal'    => 'no',
                'type'      => 'non_bank_cash',
                'default'   => 'piutang',
            ],
            [
                'name'      => 'Persediaan',
                'store_id'   => $store->id,
                'coa_code'  => '1104',
                'with_price'    => 'no',
                'with_modal'    => 'no',
                'type'      => 'non_bank_cash',
                'default'   => 'persediaan',
            ],
            [
                'name'      => 'Asset Lancar Lainnya',
                'store_id'   => $store->id,
                'coa_code'  => '1105',
                'with_price'    => 'yes',
                'with_modal'    => 'yes',
                'type'      => 'non_bank_cash',
                'default'   => 'asset_lancar',
            ],
            [
                'name'      => 'Asset Tetap',
                'store_id'   => $store->id,
                'coa_code'  => '1106',
                'with_price'    => 'no',
                'with_modal'    => 'no',
                'type'      => 'non_bank_cash',
                'default'   => 'asset_tetap',
            ],
            [
                'name'      => 'Akumulasi Penyusutan',
                'store_id'   => $store->id,
                'coa_code'  => '1107',
                'with_price'    => 'no',
                'with_modal'    => 'no',
                'type'      => 'non_bank_cash',
                'default'   => 'penyusutan',
            ],
            [
                'name'      => 'Asset Lainnya',
                'store_id'   => $store->id,
                'coa_code'  => '1108',
                'with_price'    => 'yes',
                'with_modal'    => 'no',
                'type'      => 'non_bank_cash',
                'default'   => 'asset_lainnya',
            ],
            [
                'name'      => 'Utang Usaha',
                'store_id'   => $store->id,
                'coa_code'  => '2101',
                'with_price'    => 'yes',
                'with_modal'    => 'no',
                'type'      => 'non_bank_cash',
                'default'   => 'utang',
            ],
            [
                'name'      => 'Kewajiban Jangka Pendek',
                'store_id'   => $store->id,
                'coa_code'  => '2102',
                'with_price'    => 'yes',
                'with_modal'    => 'yes',
                'type'      => 'non_bank_cash',
                'default'   => 'liabilitas_pendek',
            ],
            [
                'name'      => 'Keajiban Jangka Panjang',
                'store_id'   => $store->id,
                'coa_code'  => '2103',
                'with_price'    => 'yes',
                'with_modal'    => 'yes',
                'type'      => 'non_bank_cash',
                'default'   => 'liabilitas_panjang',
            ],
            [
                'name'      => 'Modal',
                'store_id'   => $store->id,
                'coa_code'  => '3000',
                'with_price'    => 'no',
                'with_modal'    => 'yes',
                'type'      => 'non_bank_cash',
                'default'   => 'modal',
            ],
            [
                'name'      => 'Pendapatan',
                'store_id'   => $store->id,
                'coa_code'  => '4000',
                'with_price'    => 'no',
                'with_modal'    => 'yes',
                'type'      => 'non_bank_cash',
                'default'   => 'pendapatan',
            ],
            [
                'name'      => 'Beban Pokok Penjualan',
                'store_id'   => $store->id,
                'coa_code'  => '5000',
                'with_price'    => 'no',
                'with_modal'    => 'yes',
                'type'      => 'non_bank_cash',
                'default'   => 'beban_penjualan',
            ],
            [
                'name'      => 'Beban',
                'store_id'   => $store->id,
                'coa_code'  => '6000',
                'with_price'    => 'no',
                'with_modal'    => 'yes',
                'type'      => 'non_bank_cash',
                'default'   => 'beban',
            ],
            [
                'name'      => 'Beban Diluar Usaha',
                'store_id'   => $store->id,
                'coa_code'  => '7200',
                'with_price'    => 'no',
                'with_modal'    => 'yes',
                'type'      => 'non_bank_cash',
                'default'   => 'beban_lainnya',
            ],
            [
                'name'      => 'Pendapatan Diluar Usaha',
                'store_id'   => $store->id,
                'coa_code'  => '7100',
                'with_price'    => 'no',
                'with_modal'    => 'yes',
                'type'      => 'non_bank_cash',
                'default'   => 'pendapatan_lainnya',
            ],
            [
                'name'      => 'PPH 22',
                'store_id'   => $store->id,
                'coa_code'  => '4111',
                'with_price'    => 'no',
                'with_modal'    => 'yes',
                'type'      => 'non_bank_cash',
                'default'   => 'beban_lainnya',
            ],
        ];

        AccountType::insert($data);
    }

    public function generateAccounts(Store $store)
    {

        $type1      = AccountType::where('store_id',$store->id)->where("coa_code", "1101")->first(['id']);
        $type2      = AccountType::where('store_id',$store->id)->where("coa_code", "1102")->first(['id']);
        $type3      = AccountType::where('store_id',$store->id)->where("coa_code", "1103")->first(['id']);
        $type4      = AccountType::where('store_id',$store->id)->where("coa_code", "1104")->first(['id']);
        $type5      = AccountType::where('store_id',$store->id)->where("coa_code", "1105")->first(['id']);
        $type6      = AccountType::where('store_id',$store->id)->where("coa_code", "1106")->first(['id']);
        $type7      = AccountType::where('store_id',$store->id)->where("coa_code", "1107")->first(['id']);

        $type9      = AccountType::where('store_id',$store->id)->where("coa_code", "2101")->first(['id']);
        $type10      = AccountType::where('store_id',$store->id)->where("coa_code", "2102")->first(['id']);
        $type11      = AccountType::where('store_id',$store->id)->where("coa_code", "2103")->first(['id']);
        $type12      = AccountType::where('store_id',$store->id)->where("coa_code", "3000")->first(['id']);
        $type13      = AccountType::where('store_id',$store->id)->where("coa_code", "4000")->first(['id']);
        $type14      = AccountType::where('store_id',$store->id)->where("coa_code", "5000")->first(['id']);
        $type15      = AccountType::where('store_id',$store->id)->where("coa_code", "6000")->first(['id']);
        $type16      = AccountType::where('store_id',$store->id)->where("coa_code", "7200")->first(['id']);
        $type17      = AccountType::where('store_id',$store->id)->where("coa_code", "7100")->first(['id']);

        $type18     = AccountType::where('store_id',$store->id)->where("coa_code", "4111")->first(['id']);


        $data = [

            [
                'name'              => 'PPH 22',
                'coa'               => '4111',
                'store_id'          => $store->id,
                'account_type_id'   => $type18->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'no',
                'parent_id'         => null,
                'edit_option'       => 'no',
                'default_data'      => null,
            ],

            [
                'name'              => 'Kas',
                'coa'               => '1101',
                'store_id'   => $store->id,
                'account_type_id'   => $type1->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'no',
                'parent_id'         => null,
                'edit_option'       => 'no',
                'default_data'      => null,
            ],

            [
                'name'              => 'Setara Kas',
                'coa'               => '1102',
                'store_id'   => $store->id,
                'account_type_id'   => $type2->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'no',
                'parent_id'         => null,
                'edit_option'       => 'no',
                'default_data'      => null,
            ],
            [
                'name'              => 'Piutang Usaha',
                'coa'               => '1103',
                'store_id'   => $store->id,
                'account_type_id'   => $type3->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'no',
                'parent_id'         => null,
                'edit_option'       => 'no',
                'default_data'      => 'null',
            ],


            [
                'name'              => 'Persediaan',
                'coa'               => '1104',
                'store_id'   => $store->id,
                'account_type_id'   => $type4->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'no',
                'parent_id'         => null,
                'edit_option'       => 'no',
                'default_data'      => null,
            ],


            [
                'name'              => 'Aset Lancar Lainnya',
                'coa'               => '1105',
                'store_id'   => $store->id,
                'account_type_id'   => $type5->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'no',
                'parent_id'         => null,
                'edit_option'       => 'no',
                'default_data'      => null,
            ],


            [
                'name'              => 'Aset Tetap',
                'coa'               => '1106',
                'store_id'   => $store->id,
                'account_type_id'   => $type6->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'no',
                'parent_id'         => null,
                'edit_option'       => 'no',
                'default_data'      => null,
            ],


            /**
             *  Akumulasi Penyusutan Aset Tetap
             *  Code Account 1107
             */

            [
                'name'              => 'Akumulasi Penyusutan Aset Tetap',
                'coa'               => '1107',
                'store_id'   => $store->id,
                'account_type_id'   => $type7->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'no',
                'parent_id'         => null,
                'edit_option'       => 'no',
                'default_data'      => null,
            ],



            /**
             *  Hutang Usaha
             *  Code Account 2101
             */

            [
                'name'              => 'Hutang Usaha',
                'coa'               => '2101',
                'store_id'   => $store->id,
                'account_type_id'   => $type9->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'no',
                'parent_id'         => null,
                'edit_option'       => 'no',
                'default_data'      => null,
            ],


            /**
             *  Kewajiban Jangka Pendek
             *  Code Account 2102
             */

            [
                'name'              => 'Kewajiban Jangka Pendek Lainnya',
                'coa'               => '2102',
                'store_id'   => $store->id,
                'account_type_id'   => $type10->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'no',
                'parent_id'         => null,
                'edit_option'       => 'no',
                'default_data'      => null,
            ],



            /**
             *  Kewajiban Jangka Panjang
             *  Code Account 2103
             */

            [
                'name'              => 'Kewajiban Jangka Panjang',
                'coa'               => '2103',
                'store_id'   => $store->id,
                'account_type_id'   => $type11->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'no',
                'parent_id'         => null,
                'edit_option'       => 'no',
                'default_data'      => null,
            ],



            /**
             *  Modal
             *  Code Account 3000
             */

            [
                'name'              => 'Modal',
                'coa'               => '3000',
                'store_id'   => $store->id,
                'account_type_id'   => $type12->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'no',
                'parent_id'         => null,
                'edit_option'       => 'no',
                'default_data'      => null,
            ],



            /**
             *  Pendapatan
             *  Code Account 4000
             */

            [
                'name'              => 'Pendapatan Operasional',
                'coa'               => '4000',
                'store_id'   => $store->id,
                'account_type_id'   => $type13->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'no',
                'parent_id'         => null,
                'edit_option'       => 'no',
                'default_data'      => null,
            ],


            /**
             *  Modal Pokok Penjualan
             *  Code Account 5000
             */

            [
                'name'              => 'Beban Pokok Penjualan',
                'coa'               => '5000',
                'store_id'   => $store->id,
                'account_type_id'   => $type14->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'no',
                'parent_id'         => null,
                'edit_option'       => 'no',
                'default_data'      => null,
            ],




            /**
             *  Beban Operasional
             *  Code Account 6000
             */

            [
                'name'              => 'Beban Operasional',
                'coa'               => '6000',
                'store_id'          => $store->id,
                'account_type_id'   => $type15->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'no',
                'parent_id'         => null,
                'edit_option'       => 'no',
                'default_data'      => 'beban',
            ],


            [
                'name'              => 'Pendapatan Diluar Usaha',
                'coa'               => '7100',
                'store_id'   => $store->id,
                'account_type_id'   => $type17->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'no',
                'parent_id'         => null,
                'edit_option'       => 'no',
                'default_data'      => null,
            ],

            [
                'name'              => 'Beban Diluar Usaha',
                'coa'               => '7200',
                'store_id'   => $store->id,
                'account_type_id'   => $type16->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'no',
                'parent_id'         => null,
                'edit_option'       => 'no',
                'default_data'      => null,
            ],

        ];

        Account::insert($data);

        $parentData = [

            [
                'name'              => 'Kasbon Pegawai',
                'coa'               => '110305',
                'store_id'          => $store->id,
                'account_type_id'   => $type3->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "1103")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Deposit Bank',
                'coa'               => '110201',
                'store_id'          => $store->id,
                'account_type_id'   => $type2->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "1102")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Kas Kecil',
                'coa'               => '110101',
                'store_id'          => $store->id,
                'account_type_id'   => $type1->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "1101")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Bank',
                'coa'               => '110102',
                'store_id'          => $store->id,
                'account_type_id'   => $type1->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "1101")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Piutang Usaha',
                'coa'               => '110301',
                'store_id'   => $store->id,
                'account_type_id'   => $type3->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "1103")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => 'piutang',
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Uang Muka Pembelian',
                'coa'               => '110302',
                'store_id'   => $store->id,
                'account_type_id'   => $type3->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "1103")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Piutang Karyawan',
                'coa'               => '110303',
                'store_id'   => $store->id,
                'account_type_id'   => $type3->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "1103")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Piutang Lain-Lain',
                'coa'               => '110304',
                'store_id'   => $store->id,
                'account_type_id'   => $type3->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "1103")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Persediaan',
                'coa'               => '110401',
                'store_id'   => $store->id,
                'account_type_id'   => $type4->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "1104")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Perlengkapan Kantor',
                'coa'               => '110501',
                'store_id'   => $store->id,
                'account_type_id'   => $type5->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "1105")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Sewa Kantor di Bayar di Muka',
                'coa'               => '110502',
                'store_id'   => $store->id,
                'account_type_id'   => $type5->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "1105")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'PPN Masukan',
                'coa'               => '110503',
                'store_id'   => $store->id,
                'account_type_id'   => $type5->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "1105")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'tax'
            ],
            [
                'name'              => 'PPh 23 Penjualan',
                'coa'               => '110504',
                'store_id'   => $store->id,
                'account_type_id'   => $type5->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "1105")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Tanah',
                'coa'               => '110601',
                'store_id'   => $store->id,
                'account_type_id'   => $type6->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "1106")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Gedung',
                'coa'               => '110602',
                'store_id'   => $store->id,
                'account_type_id'   => $type6->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "1106")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Peralatan Kantor',
                'coa'               => '110603',
                'store_id'   => $store->id,
                'account_type_id'   => $type6->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "1106")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Inventaris Kantor',
                'coa'               => '110604',
                'store_id'   => $store->id,
                'account_type_id'   => $type6->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "1106")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'PPN Lebih Bayar',
                'coa'               => '110605',
                'store_id'          => $store->id,
                'account_type_id'   => $type6->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "1106")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'tax'
            ],
            [
                'name'              => 'Akumulasi Penyusutan Gedung',
                'coa'               => '110701',
                'store_id'   => $store->id,
                'account_type_id'   => $type7->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "1107")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Akumulasi Penyusutan Peralatan Kantor',
                'coa'               => '110702',
                'store_id'   => $store->id,
                'account_type_id'   => $type7->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "1107")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Akumulasi Penyusutan Inventaris Kantor',
                'coa'               => '110703',
                'store_id'   => $store->id,
                'account_type_id'   => $type7->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "1107")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Hutang Usaha',
                'coa'               => '210101',
                'store_id'   => $store->id,
                'account_type_id'   => $type9->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "2101")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Uang Muka Penjualan',
                'coa'               => '210102',
                'store_id'   => $store->id,
                'account_type_id'   => $type9->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "2101")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'PPN Keluaran',
                'coa'               => '210201',
                'store_id'   => $store->id,
                'account_type_id'   => $type10->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "2102")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'tax'
            ],
            [
                'name'              => 'PPh 23 Pembelian',
                'coa'               => '210202',
                'store_id'   => $store->id,
                'account_type_id'   => $type10->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "2102")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Hutang Bank',
                'coa'               => '210301',
                'store_id'   => $store->id,
                'account_type_id'   => $type11->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "2103")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Equitas Saldo Awal',
                'coa'               => '300001',
                'store_id'   => $store->id,
                'account_type_id'   => $type12->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "3000")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => 'modal',
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Laba Ditahan',
                'coa'               => '300002',
                'store_id'   => $store->id,
                'account_type_id'   => $type12->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "3000")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Modal Saham',
                'coa'               => '300003',
                'store_id'   => $store->id,
                'account_type_id'   => $type12->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "3000")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Penjualan',
                'coa'               => '400001',
                'store_id'   => $store->id,
                'account_type_id'   => $type13->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "4000")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => 'modal',
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Pendapatan Jasa',
                'coa'               => '400002',
                'store_id'   => $store->id,
                'account_type_id'   => $type13->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "4000")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Retur Penjualan',
                'coa'               => '400003',
                'store_id'   => $store->id,
                'account_type_id'   => $type13->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "4000")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Diskon Penjualan',
                'coa'               => '400004',
                'store_id'   => $store->id,
                'account_type_id'   => $type13->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "4000")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],

            [
                'name'              => 'Beban Gaji Karyawan',
                'coa'               => '600001',
                'store_id'   => $store->id,
                'account_type_id'   => $type15->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "6000")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Beban Bonus Karyawan',
                'coa'               => '600002',
                'store_id'   => $store->id,
                'account_type_id'   => $type15->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "6000")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Beban Transportasi',
                'coa'               => '600003',
                'store_id'   => $store->id,
                'account_type_id'   => $type15->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "6000")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Beban Konsumsi',
                'coa'               => '600004',
                'store_id'   => $store->id,
                'account_type_id'   => $type15->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "6000")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Beban Iklan',
                'coa'               => '600005',
                'store_id'   => $store->id,
                'account_type_id'   => $type15->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "6000")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Beban Komisi',
                'coa'               => '600006',
                'store_id'   => $store->id,
                'account_type_id'   => $type15->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "6000")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Beban Tunjangan Kesehatan',
                'coa'               => '600007',
                'store_id'   => $store->id,
                'account_type_id'   => $type15->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "6000")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Beban Listrik,Air dan Telepon ',
                'coa'               => '600008',
                'store_id'   => $store->id,
                'account_type_id'   => $type15->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "6000")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Beban Perlengkapan Kantor ',
                'coa'               => '600009',
                'store_id'   => $store->id,
                'account_type_id'   => $type15->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "6000")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Beban Perjalanan Dinas',
                'coa'               => '600010',
                'store_id'   => $store->id,
                'account_type_id'   => $type15->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "6000")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Beban Sewa Gedung',
                'coa'               => '600011',
                'store_id'   => $store->id,
                'account_type_id'   => $type15->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "6000")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Beban Penyusutan Gedung',
                'coa'               => '600012',
                'store_id'   => $store->id,
                'account_type_id'   => $type15->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "6000")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],

            [
                'name'              => 'Beban Penyusutan Peralatan',
                'coa'               => '600013',
                'store_id'   => $store->id,
                'account_type_id'   => $type15->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "6000")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Beban Penyusutan Inventaris Kantor',
                'coa'               => '600014',
                'store_id'   => $store->id,
                'account_type_id'   => $type15->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "6000")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Beban Lain-Lain',
                'coa'               => '600015',
                'store_id'   => $store->id,
                'account_type_id'   => $type15->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "6000")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'PPN Kurang Bayar',
                'coa'               => '600017',
                'store_id'   => $store->id,
                'account_type_id'   => $type10->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "2102")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'tax'
            ],
            [
                'name'              => 'Pendapatan Jasa Giro',
                'coa'               => '710001',
                'store_id'   => $store->id,
                'account_type_id'   => $type17->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "7100")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Pendapatan Bunga Deposito',
                'coa'               => '710002',
                'store_id'   => $store->id,
                'account_type_id'   => $type17->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "7100")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Laba/Rugi Revaluasi Aset',
                'coa'               => '710003',
                'store_id'   => $store->id,
                'account_type_id'   => $type17->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "7100")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Pendapatan Diluar Usaha Lainnya',
                'coa'               => '710004',
                'store_id'   => $store->id,
                'account_type_id'   => $type17->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "7100")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Beban Bunga Pinjaman',
                'coa'               => '720001',
                'store_id'   => $store->id,
                'account_type_id'   => $type16->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "7200")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Beban Administrasi Bank',
                'coa'               => '720002',
                'store_id'   => $store->id,
                'account_type_id'   => $type16->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "7200")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Pajak Jasa Giro',
                'coa'               => '720003',
                'store_id'   => $store->id,
                'account_type_id'   => $type16->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "7200")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Laba/Rugi Terealisasi',
                'coa'               => '720004',
                'store_id'   => $store->id,
                'account_type_id'   => $type16->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "7200")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Laba/Rugi Belum Terealisasi',
                'coa'               => '720005',
                'store_id'   => $store->id,
                'account_type_id'   => $type16->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "7200")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],
            [
                'name'              => 'Beban Diluar Usaha Lainnya',
                'coa'               => '720006',
                'store_id'   => $store->id,
                'account_type_id'   => $type16->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "7200")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],

            [
                'name'              => 'Biaya Ekspedisi',
                'coa'               => '600016',
                'store_id'   => $store->id,
                'account_type_id'   => $type15->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "6000")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],

            [
                'name'              => 'PPH 22 Penjualan',
                'coa'               => '411101',
                'store_id'          => $store->id,
                'account_type_id'   => $type18->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "4111")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],

            [
                'name'              => 'PPH 22 Pembelian',
                'coa'               => '411102',
                'store_id'          => $store->id,
                'account_type_id'   => $type18->id,
                'created_by'        => auth()->user()->id,
                'is_root_parent'    => 'yes',
                'parent_id'         => Account::where("store_id", $store->id)->where("coa", "4111")->first(['id'])->id,
                'edit_option'       => 'yes',
                'default_data'      => null,
                'type_account'      => 'non_tax'
            ],


        ];

        Account::insert($parentData);
    }
}
