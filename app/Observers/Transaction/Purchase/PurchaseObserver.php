<?php

namespace App\Observers\Transaction\Purchase;

use App\Helper;
use App\Models\Account\Account;
use App\Models\Account\AccountTransaction;
use App\Models\Admin\AccountSetting;
use App\Models\Product\Stock;
use App\Models\Product\Supplier;
use App\Models\Product\Unit;
use App\Models\Product\Variation;
use App\Models\Transaction\Purchase;
use App\Models\Transaction\Transaction;
use App\Models\User;
use App\Observers\Account\LedgerObserver;
use App\Observers\Account\LedgerTransactionObserver;
use App\Observers\Inventory\StockObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseObserver
{

    protected $stockObserver;
    protected $ledgerTransactionObserver;
    protected $receivedProductObserver;
    protected $purchasePaymentObserver;
    protected $ledgerObserver;
    protected $PoObserver;

    public function __construct(StockObserver $stockObserver, LedgerTransactionObserver $ledgerTransactionObserver, ReceivedProductObserver $receivedProductObserver, PurchasePaymentObserver $purchasePaymentObserver, LedgerObserver $ledgerObserver, POObserver $PoObserver)
    {
        $this->stockObserver                    = $stockObserver;
        $this->ledgerTransactionObserver        = $ledgerTransactionObserver;
        $this->receivedProductObserver          = $receivedProductObserver;
        $this->purchasePaymentObserver          = $purchasePaymentObserver;
        $this->ledgerObserver                   = $ledgerObserver;
        $this->PoObserver                       = $PoObserver;
    }

    public function getData(Request $request)
    {
        $query = Transaction::with('supplier')->where(function ($query) use ($request) {
            return $request->supplier ? $query->whereIn('supplier_id', explode(",", $request->supplier)) : '';
        })->where(function ($query) use ($request) {
            return $request->payment ?  $query->whereIn('payment_status', explode(",", $request->payment)) : '';
        })->where(function ($q) use ($request) {
            if ($request->end_date && $request->start_date) {
                return $q->whereBetween('transaction_date', [$request->start_date, now()->parse($request->end_date)->addDay()]);
            } else {
                return $request->start_date ? $q->whereDate("transaction_date", $request->start_date) : "";
            }
        })->where(function ($query) use ($request) {
            return $request->ref ? $query->where('ref_no', 'like', '%' . $request->ref . '%')->orWhere(function ($q) use ($request) {
                $q->whereHas('supplier', function ($q) use ($request) {
                    return $request->ref ? $q->where('name', 'like', '%' . $request->ref . '%') : '';
                });
            }) : '';
        })->where(function ($query) use ($request) {
            return $request->createdby ?  $query->whereIn('created_by', explode(",", $request->createdby)) : '';
        })->where('type', 'purchase');

        if ($request->sort == 'date') {
            $query->orderBy('transaction_date', $request->sortby);
        } else if ($request->sort == 'ref_no') {
            $query->orderBy('ref_no', $request->sortby);
        } else if ($request->sort == 'supplier_ref') {
            $query->orderBy('supplier_ref', $request->sortby);
        } else if ($request->sort == 'supplier.name') {
            $query->orderBy(Supplier::select('name')->whereColumn('suppliers.id', 'transactions.supplier_id'), $request->sortby);
        } else if ($request->sort == 'final_total') {
            $query->orderBy('final_total', $request->sortby);
        } else if ($request->sort == 'created.name') {
            $query->orderBy(User::select('name')->whereColumn('users.id', 'transactions.created_by'), $request->sortby);
        }

        return $query;
    }

    public function getPurchaseProduct(Request $request)
    {
        return Purchase::with("transaction", "product", "variation")->where("type", "inventory")->whereHas('transaction', function ($q) use ($request) {
            if ($request->end_date && $request->start_date) {
                return $q->whereBetween('transaction_date', [$request->start_date, now()->parse($request->end_date)->addDay()]);
            } else {
                return $request->start_date ? $q->whereDate("transaction_date", $request->start_date) : '';
            }
        })->where(function ($q) use ($request) {
            return $q->whereHas('product', function ($query) use ($request) {
                $request->name ? $query->where('name', 'like', '%' . $request->name . '%') : '';
            })->orWhereHas('variation', function ($query) use ($request) {
                $request->name ? $query->where('name', 'like', '%' . $request->name . '%') : '';
            })->orWhereHas('transaction', function ($query) use ($request) {
                return $request->name ? $query->where('ref_no', 'like', '%' . $request->name . '%') : '';
            });
        })->where(function ($q) {
            return $q->whereHas('transaction', function ($query) {
                $query->where('type', 'purchase');
            });
        })->where(function ($q) use ($request) {
            return $q->whereHas('transaction', function ($query) use ($request) {
                $request->supplier ? $query->where('supplier_id', $request->supplier) : '';
            });
        })->where(function ($q) use ($request) {
            return $q->whereHas('transaction', function ($query) use ($request) {
                return $request->createdby ? $query->where('created_by', $request->createdby) : '';
            });
        })->where(function ($q) use ($request) {
            return $q->whereHas('transaction', function ($query) use ($request) {
                return $request->status ? $query->where('status', $request->status) : '';
            });
        })->orderBy("created_at", "desc");
    }

    public function createUpdateInformation(Request $request, $condition, Transaction $transaction = null)
    {
        $generalInformation = $request->general_information;

        if ($condition == 'create') {
            $getTransaction         = Transaction::where("type", "purchase")->whereDate("created_at", date("Y-m-d"))->count() + 1;
            $invoiceNumber          = sprintf("%05d", $getTransaction);
            $refNo                  = Helper::transactionKey('PO', $invoiceNumber);

            $data                   = new Transaction();
            $data->invoice_no       = $invoiceNumber;
            $data->ref_no           = $generalInformation['no_ref'] != null ? $generalInformation['no_ref'] : $refNo;
            $data->supplier_ref     = $generalInformation['supplier_ref'] ? $generalInformation['supplier_ref'] : '';
            $data->transaction_date = $generalInformation['date'] ? Helper::setTimeZoneLocal($generalInformation['date']) . ' ' . date('H:i:s') : date('Y-m-d H:i:s');
        } else {
            $data = Transaction::find($transaction->id);
            $data->ref_no               = $generalInformation['no_ref'] != null ? $generalInformation['no_ref'] : $transaction->ref_no;
            $data->supplier_ref         = $generalInformation['supplier_ref'] ? $generalInformation['supplier_ref'] : '';
            $data->transaction_date     = $generalInformation['date'] ? Helper::setTimeZoneLocal($generalInformation['date']) . ' ' . $transaction->created_at->format('H:i:s') : date('Y-m-d') . ' ' . $transaction->created_at->format('H:i:s');
        }

        $data->due_limit            = $generalInformation['due_limit'];
        $data->due_end              = $generalInformation['due_limit'] > 0 ? now()->addDays($generalInformation['due_limit']) : null;
        $data->type                 = 'purchase';
        $data->warehouse_id         = $generalInformation['warehouse']['id'];
        $data->address              = $generalInformation['address'];
        $data->payment_status       = 'due';
        $data->status               = $request->status;
        $data->address              = $generalInformation['address'];
        $data->supplier_id          = $generalInformation['supplier']['id'];
        $data->created_by           = auth()->user()->id;

        $data->save();

        return $data;
    }

    public function createOrUpdateTransaction(Request $request, Transaction $transaction)
    {

        $shippingCost       = 0;
        $discountPurchase   = 0;
        $listData           = collect($request->items);
        $subtotal           = $listData->sum('subtotal');
        $paymentInformation = $request->payment_information;

        if ($paymentInformation['shipping_alocation'] == 'product' && $paymentInformation['shipping_cost'] > 0) {
            $shippingCost       = $paymentInformation['shipping_cost'] / count($request->items);
        }

        if ($paymentInformation['discount'] > 0) {
            $discountPurchase   = $paymentInformation['discount_type'] == 'percent' && $paymentInformation['discount'] > 0 && $subtotal > 0 ? (($paymentInformation['discount'] / 100) * $subtotal) : $paymentInformation['discount'];
            $discountPurchase   = $discountPurchase + $listData->sum('discount_amount');
            $discountPurchase   = $discountPurchase / count($request->items);
        }

        foreach ($request->items as $item) {

            $otherCost      = $shippingCost;
            $discountCost   = $discountPurchase;
            $unit           = null;
            $quantity       = $item['qty'];

            if ($item['unit']) {
                $unit           = Unit::where("id", $item['unit'])->first();
                if ($unit) {
                    $quantity   = $item['qty'] * $unit->value;
                }
            }

            if ($otherCost > 0) {
                $otherCost  = $otherCost / $quantity;
            }

            if ($discountCost > 0) {
                $discountCost  = $discountCost / $quantity;
            }

            if ($item['item_id'] != null) {

                $purchase = Purchase::find($item['item_id']);
                $oldPrice = $purchase->purchase_price;

                $purchase->update([
                    'transaction_id'                    => $transaction->id,
                    'product_id'                        => $item['product_id'],
                    'variation_id'                      => $item['variation_id'],
                    'unit_qty'                          => $item['qty'],
                    'item_tax'                          => $item['tax'],
                    'tax_total'                         => $item['total_tax'],
                    'quantity'                          => $quantity,
                    'unit_id'                           => $unit != null ? $unit->id : null,
                    'discount_percent'                  => $item['discount_amount'] ?? 0,
                    'purchase_price'                    => $transaction->supplier->tax_default == 'yes' ? $item['unit_price'] - $item['total_tax'] : $item['unit_price'],
                    'purchase_price_inc_tax'            => $transaction->supplier->tax_default == 'yes' ? $item['unit_price'] : $item['purchase_price_inc_tax'],
                    'without_discount'                  => $item['without_discount'],
                    'discount_type'                     => $item['discount_type'],
                    'other_cost'                        => $otherCost - $discountCost,
                    'publish'                           => $transaction->status == 'draft' ? 'draft' : 'publish'
                ]);

                if ($purchase->transaction_received_id != null) {

                    $stocks     = $this->stockObserver->createData($purchase->variation, $transaction->warehouse_id);
                    $history    = $this->stockObserver->historyLogStock($purchase->transaction_received, $purchase->variation_id);
                    $endStock   = $history->from + $purchase->quantity;

                    if ($stocks) {
                        $stocks->update([
                            'qty_available'     => ($stocks->qty_available - $history->qty) + $purchase->quantity
                        ]);
                    }

                    $purchase->variation->update([
                        'purchase_price'    => $purchase->purchase_price,
                        'price_inc_tax'     => $purchase->purchase_price_inc_tax
                    ]);

                    $this->stockObserver->updateHistoryLog($history, $purchase, $purchase->quantity, $endStock);
                    $this->stockObserver->updatePricing($purchase->variation);

                    $this->ledgerTransactionObserver->updateSupplyAccount($purchase->transaction_received, $purchase);
                    $this->receivedProductObserver->subtotalTransactionChange($purchase->transaction_received);
                } else {

                    $stocks     = $this->stockObserver->createData($purchase->variation, $transaction->warehouse_id);
                    $history    = $this->stockObserver->historyLogStock($purchase->transaction, $purchase->variation_id);

                    if ($history) {
                        $endStock   = $history->from + $purchase->quantity;

                        if ($stocks) {
                            $stocks->update([
                                'qty_available'     => ($stocks->qty_available - $history->qty) + $purchase->quantity
                            ]);
                        }

                        $purchase->variation->update([
                            'purchase_price'    => $purchase->purchase_price,
                            'price_inc_tax'     => $purchase->purchase_price_inc_tax
                        ]);

                        $this->stockObserver->updateHistoryLog($history, $purchase, $purchase->quantity, $endStock);
                        $this->stockObserver->updatePricing($purchase->variation);
                        $this->ledgerTransactionObserver->updateSupplyAccount($purchase->transaction, $purchase);
                    } else {
                        $firstStock = $purchase->variation->all_stock->sum('qty_available');
                        $endStock   = $purchase->variation->all_stock->sum('qty_available') + $purchase->quantity;
        
                        if ($stocks) {
                            $stocks->update([
                                'qty_available'     => $stocks->qty_available + $purchase->quantity
                            ]);
                        }
        
                        $purchase->variation->update([
                            'purchase_price'    => $purchase->purchase_price,
                            'price_inc_tax'     => $purchase->purchase_price_inc_tax
                        ]);
        
                        $toRelocate     = $this->receivedProductObserver->changeOrDeletePurchase($purchase, ($purchase->quantity - $purchase->qty_return));
                        $purchaseiD     = $purchase->sell_purchase_first->id ?? null;
        
                        if ($toRelocate['status'] == true) {
                            $this->receivedProductObserver->updateSellPurchases($toRelocate['nextPurchases'], $purchaseiD, $purchase->variation->id);
                        }
        
                        $this->stockObserver->createHistoryStock('purchase', $purchase, $transaction->id, $purchase->quantity, $firstStock, $endStock);
                        $this->stockObserver->updatePricing($purchase->variation);
                        $this->ledgerTransactionObserver->addSupplyAccount($purchase);
                    }
                }

                $toRelocate     = $this->receivedProductObserver->changeOrDeletePurchase($purchase, ($purchase->quantity - $purchase->qty_return));
                $purchaseiD     = $purchase->sell_purchase_first->id ?? null;

                if ($toRelocate['status'] == true) {
                    $this->receivedProductObserver->updateSellPurchases($toRelocate['nextPurchases'], $purchaseiD, $purchase->variation->id);
                }

                $this->receivedProductObserver->changePricingUpdate($purchase, $oldPrice);
                $this->subtotalTransactionChange($transaction);

                if ($purchase->po_id != null) {
                    $this->PoObserver->subtotalTransactionChange($purchase->po);
                }
            } else {
                $purchase = Purchase::create([
                    'transaction_id'                    => $transaction->id,
                    'product_id'                        => $item['product_id'],
                    'variation_id'                      => $item['variation_id'],
                    'unit_qty'                          => $item['qty'],
                    'quantity'                          => $quantity,
                    'unit_id'                           => $unit != null ? $unit->id : null,
                    'discount_percent'                  => $item['discount_amount'] ?? 0,
                    'purchase_price'                    => $transaction->supplier->tax_default == 'yes' ? $item['unit_price'] - $item['total_tax'] : $item['unit_price'],
                    'purchase_price_inc_tax'            => $transaction->supplier->tax_default == 'yes' ? $item['unit_price'] : $item['purchase_price_inc_tax'],
                    'without_discount'                  => $item['without_discount'],
                    'discount_type'                     => $item['discount_type'],
                    'item_tax'                          => $item['tax'],
                    'tax_total'                         => $item['total_tax'],
                    'other_cost'                        => $otherCost - $discountCost,
                    'publish'                           => $transaction->status == 'draft' ? 'draft' : 'publish'
                ]);

                $stocks     = $this->stockObserver->createData($purchase->variation, $transaction->warehouse_id);
                $history    = $this->stockObserver->historyLogStock($purchase->transaction, $purchase->variation_id);

                $firstStock = $purchase->variation->all_stock->sum('qty_available');
                $endStock   = $purchase->variation->all_stock->sum('qty_available') + $purchase->quantity;

                if ($stocks) {
                    $stocks->update([
                        'qty_available'     => $stocks->qty_available + $purchase->quantity
                    ]);
                }

                $purchase->variation->update([
                    'purchase_price'    => $purchase->purchase_price,
                    'price_inc_tax'     => $purchase->purchase_price_inc_tax
                ]);

                $toRelocate     = $this->receivedProductObserver->changeOrDeletePurchase($purchase, ($purchase->quantity - $purchase->qty_return));
                $purchaseiD     = $purchase->sell_purchase_first->id ?? null;

                if ($toRelocate['status'] == true) {
                    $this->receivedProductObserver->updateSellPurchases($toRelocate['nextPurchases'], $purchaseiD, $purchase->variation->id);
                }

                $this->stockObserver->createHistoryStock('purchase', $purchase, $transaction->id, $purchase->quantity, $firstStock, $endStock);
                $this->stockObserver->updatePricing($purchase->variation);
                $this->ledgerTransactionObserver->addSupplyAccount($purchase);

                $this->subtotalTransactionChange($transaction);
            }
        }
    }

    public function deleteItem(Purchase $purchase, Transaction $transaction)
    {

        $variation  = $purchase->variation;
        $quantity   = $purchase->quantity;

        if ($purchase->transaction_received_id == null) {
            $stocks     = $this->stockObserver->createData($variation, $transaction->warehouse_id);
            $this->stockObserver->historyLogStock($transaction, $variation->id)->delete();

            if ($stocks) {
                $stocks->update([
                    'qty_available'     => ($stocks->qty_available - $quantity),
                ]);
            }
        }

        foreach ($purchase->purchase_account_item as $account) {
            if ($purchase->transaction_received_id != null) {
                if ($account->sub_type != 'received_product_from_supplier') {
                    $accountData = $account->account;
                    $transactionNext    = AccountTransaction::where(function ($query) use ($account) {
                        $query->where("operation_date", ">", $account->operation_date)
                            ->orWhere(function ($subQuery) use ($account) {
                                $subQuery->where("operation_date", "=", $account->operation_date)
                                    ->where("id", "<", $account->id);
                            });
                    })
                        ->where("account_id", $account->account_id)
                        ->orderBy("operation_date", 'asc')
                        ->orderBy("id", 'asc')->first();

                    $account->delete();

                    if ($transactionNext) {
                        $this->ledgerTransactionObserver->logAccountUpdate($transactionNext);
                    } else {
                        $this->ledgerObserver->updateCashFlowAccount($accountData);
                    }
                }
            } else {
                $accountData = $account->account;
                $transactionNext    = AccountTransaction::where(function ($query) use ($account) {
                    $query->where("operation_date", ">", $account->operation_date)
                        ->orWhere(function ($subQuery) use ($account) {
                            $subQuery->where("operation_date", "=", $account->operation_date)
                                ->where("id", "<", $account->id);
                        });
                })
                    ->where("account_id", $account->account_id)
                    ->orderBy("operation_date", 'asc')
                    ->orderBy("id", 'asc')->first();

                $account->delete();

                if ($transactionNext) {
                    $this->ledgerTransactionObserver->logAccountUpdate($transactionNext);
                } else {
                    $this->ledgerObserver->updateCashFlowAccount($accountData);
                }
            }
        }


        if ($purchase->transaction_received_id != null) {
            $purchase->update([
                'transaction_id'            => null,
                'other_cost'                => 0,
                'item_tax'                  => 0,
                'tax_total'                 => 0,
                'purchase_price_inc_tax'    => $purchase->purchase_price
            ]);

            if ($purchase->transaction_received) {
                $purchase->transaction_received->update([
                    'status'        => 'received_not_use'
                ]);
            }
        } else {

            $variation      = $purchase->variation;
            $toRelocate     = $this->receivedProductObserver->changeOrDeletePurchase($purchase, 0);
            $purchaseiD     = $purchase->sell_purchase_first->id ?? null;

            if($purchase->po_id != null) {
                $purchase->update([
                    'transaction_id'       => null
                ]); 
                $this->PoObserver->subtotalTransactionChange($purchase->po);
            } else {
                $purchase->forceDelete();
            }
           

            if ($toRelocate['status'] == true) {
                $this->receivedProductObserver->updateSellPurchases($toRelocate['nextPurchases'], $purchaseiD, $variation->id);
            }

            $this->stockObserver->updatePricing($variation);
            $this->subtotalTransactionChange($purchase->transaction);
        }
    }

    public function subtotalTransactionChange(Transaction $transaction)
    {
        $subtotal                           = $transaction->purchase()->selectRaw("sum(purchase_price * quantity) as jumlah")->first();
        $discountTotal                      = $transaction->discount_type == 'percent' && $transaction->discount_amount > 0 && $subtotal->jumlah > 0 ? (($transaction->discount_amount / 100) * $subtotal->jumlah) : $transaction->discount_amount;
        $taxFinal                           = $transaction->purchase()->selectRaw('sum(tax_total * quantity) as jumlah')->first();

        $transaction->update([
            'tax_final'             => $taxFinal->jumlah,
            'discount_final'        => $discountTotal,
            'total_before_tax'      => (float)$subtotal->jumlah,
            'final_total'           => ((float)$subtotal->jumlah + $transaction->shipping_charges + $taxFinal->jumlah) - $discountTotal
        ]);

        $settings = AccountSetting::first(['cost_shipping_transaction', 'tax_input']);

        if ($settings) {
            if ($settings->transaction_shipping_account) {

                $dataAccount = $settings->transaction_shipping_account;

                $accountShippingTransaction = AccountTransaction::where("transaction_id", $transaction->id)->where("sub_type", "purchase_shipping")->first();
                if ($accountShippingTransaction && $transaction->shipping_charges == 0 || $accountShippingTransaction && $transaction->shipping_alocation == 'beban') {
                    $accountShippingTransactionNext = AccountTransaction::where(function ($query) use ($accountShippingTransaction) {
                        $query->where("operation_date", ">", $accountShippingTransaction->operation_date)
                            ->orWhere(function ($subQuery) use ($accountShippingTransaction) {
                                $subQuery->where("operation_date", "=", $accountShippingTransaction->operation_date)
                                    ->where("id", "<", $accountShippingTransaction->id);
                            });
                    })
                        ->where("account_id", $accountShippingTransaction->account_id)
                        ->orderBy("operation_date", 'asc')
                        ->orderBy("id", 'asc')->first();

                    $accountShippingTransaction->forceDelete();

                    if ($accountShippingTransactionNext) {
                        $this->ledgerTransactionObserver->logAccountUpdate($accountShippingTransactionNext);
                    } else {
                        if ($dataAccount) {
                            $this->ledgerObserver->updateCashFlowAccount($dataAccount);
                        }
                    }
                }

                if ($accountShippingTransaction && $transaction->shipping_charges > 0 && $transaction->shipping_alocation == 'beban') {

                    $accountShippingTransaction->update([
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => $transaction->shipping_charges,
                    ]);

                    $this->ledgerTransactionObserver->logAccountUpdate($accountShippingTransaction);
                }

                if (!$accountShippingTransaction && $transaction->shipping_charges > 0 && $transaction->shipping_alocation == 'beban') {

                    $accountShippingTransaction = AccountTransaction::create([
                        'account_id'                    => $dataAccount->id,
                        'transaction_id'                => $transaction->id,
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => $transaction->shipping_charges,
                        'type'                          => 'debit',
                        'sub_type'                      => 'purchase_shipping',
                        'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('sale_faktur', $transaction->created_at),
                        'operation_date'                => $transaction->transaction_date,
                        'name'                          => 'Faktur Pembelian - ' . $transaction->ref_no
                    ]);

                    $this->ledgerObserver->updateCashFlowAccount($dataAccount);
                    $this->ledgerTransactionObserver->logAccountTransaction($accountShippingTransaction);
                }
            }

            if ($settings->tax_input_account) {

                // If Delete Account Tax
                $dataAccount = $settings->tax_input_account;

                $ppnMasukan = AccountTransaction::where("transaction_id", $transaction->id)->where("sub_type", "tax_input")->first();
                if ($ppnMasukan && $taxFinal->jumlah == 0) {
                    $ppnMasukanNext = AccountTransaction::where(function ($query) use ($ppnMasukan) {
                        $query->where("operation_date", ">", $ppnMasukan->operation_date)
                            ->orWhere(function ($subQuery) use ($ppnMasukan) {
                                $subQuery->where("operation_date", "=", $ppnMasukan->operation_date)
                                    ->where("id", "<", $ppnMasukan->id);
                            });
                    })
                        ->where("account_id", $ppnMasukan->account_id)
                        ->orderBy("operation_date", 'asc')
                        ->orderBy("id", 'asc')->first();

                    $ppnMasukan->forceDelete();

                    if ($ppnMasukanNext) {
                        $this->ledgerTransactionObserver->logAccountUpdate($ppnMasukanNext);
                    } else {
                        if ($dataAccount) {
                            $this->ledgerObserver->updateCashFlowAccount($dataAccount);
                        }
                    }
                }

                // If Update Account
                if ($ppnMasukan && $taxFinal->jumlah > 0) {

                    $ppnMasukan->update([
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => $taxFinal->jumlah,
                    ]);

                    $this->ledgerTransactionObserver->logAccountUpdate($ppnMasukan);
                }

                // If Create New
                if (!$ppnMasukan && $taxFinal->jumlah > 0 && (my_store_detail()->tax_option ?? '') == 'active') {

                    $ppnMasukan = AccountTransaction::create([
                        'account_id'                    => $dataAccount->id,
                        'transaction_id'                => $transaction->id,
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => $taxFinal->jumlah,
                        'type'                          => 'debit',
                        'sub_type'                      => 'tax_input',
                        'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('tax_input', $transaction->created_at),
                        'operation_date'                => $transaction->transaction_date,
                        'name'                          => 'PPN Masukan - ' . $transaction->ref_no
                    ]);

                    $this->ledgerObserver->updateCashFlowAccount($dataAccount);
                    $this->ledgerTransactionObserver->logAccountTransaction($ppnMasukan);
                }
            }
        }
    }

    public function updateOtherInformation(Request $request, Transaction $transaction)
    {

        $paymentInformation = $request->payment_information;

        $transaction->update([
            'discount_amount'       => $paymentInformation['discount'],
            'discount_type'         => $paymentInformation['discount_type'],
            'shipping_charges'      => $paymentInformation['shipping_cost'],
            'shipping_alocation'    => $paymentInformation['shipping_alocation'],
            'additional_notes'      => $paymentInformation['note']
        ]);

        $this->subtotalTransactionChange($transaction);
    }

    public function getDataByItem($variationId)
    {
        return Purchase::where("variation_id", $variationId);
    }

    public function deleteTransaction(Transaction $transaction)
    {
        foreach ($transaction->purchase as $purchase) {
            $this->deleteItem($purchase, $transaction);
        }

        if ($transaction->transaction_due != null) {

            foreach ($transaction->transaction_due->faktur as $faktur) {
                $this->purchasePaymentObserver->deleteItem($faktur, $faktur->transaction);
            }

            $transaction->transaction_due->payment()->delete();
            $transaction->transaction_due()->delete();
        }

        foreach ($transaction->account_transaction as $account) {
            $dataAccount = $account->account;
            $nexTransaction = AccountTransaction::where("operation_date", ">", $account->operation_date)->where("account_id", $account->account_id)->first();

            $account->forceDelete();

            if ($nexTransaction) {
                $this->ledgerTransactionObserver->logAccountUpdate($nexTransaction);
            } else {
                if ($dataAccount) {
                    $this->ledgerObserver->updateCashFlowAccount($dataAccount);
                }
            }
        }

        $transaction->forceDelete();
    }

    public function getPurchaseQtyHaving($productId, $variationId)
    {
        return Purchase::select(DB::raw(
            "id, quantity, qty_sold, qty_transfer, qty_adjusted, qty_adjusted_add, qty_expire, product_id, variation_id, store_id,
            SUM(IFNULL(qty_sold, 0) + IFNULL(qty_adjusted, 0) + IFNULL(qty_return, 0) + IFNULL(qty_transfer, 0) + IFNULL(qty_expire, 0)) AS qty_sum,
            SUM(quantity + qty_adjusted_add) as qty_total, SUM((quantity + qty_adjusted_add) - (IFNULL(qty_sold, 0) + IFNULL(qty_adjusted, 0) + IFNULL(qty_return, 0) + IFNULL(qty_transfer, 0) + IFNULL(qty_expire, 0))) as sisa_qty, purchase_price_inc_tax"
        ))
            ->where('product_id', $productId)
            ->where('variation_id', $variationId)
            ->where("publish", "publish")
            ->havingRaw("qty_total > qty_sum")
            ->groupBy("id")
            ->orderBy('id', 'asc')
            ->get();
    }

    public function handlingFirstStock(Variation $variant, Stock $stock, Int $firstStock, String $for = '', $capital = null, $type = 'open_stock')
    {
        $getTransaction   = Transaction::where("type", "purchase")->whereDate("created_at", date("Y-m-d"))->count() + 1;
        $invoiceNumber    = sprintf("%05d", $getTransaction);
        $refNo            = Helper::transactionKey('PO', $invoiceNumber);
        $purchasePrice    = $capital != null ? $capital : $variant->purchase_price;

        $transaction      = Transaction::create([
            'type'              => $type,
            'status'            => 'received',
            'payment_status'    => 'paid',
            'created_by'        => auth()->user()->id,
            'invoice_no'        => $invoiceNumber,
            'ref_no'            => $refNo,
            'transaction_date'  => now(),
            'total_before_tax'  => $purchasePrice * $firstStock,
            'tax_amount'        => 0,
            'open_stock_product_id' => $type == 'open_stock' ? $variant->product_id  : null,
            'final_total'       => $purchasePrice * $firstStock,
        ]);

        $purchase = Purchase::create([
            'transaction_id'    => $transaction->id,
            'variation_id'      => $variant->id,
            'product_id'        => $variant->product_id,
            'quantity'          => $firstStock,
            'purchase_price'    => $purchasePrice,
            'without_discount'  => $purchasePrice,
            'purchase_price_inc_tax'    => $purchasePrice,
            'unit_id'           => $variant->unit_id,
            'unit_qty'          => $firstStock,
            'publish'           => 'publish'
        ]);

        $stock->update([
            'qty_available'     => $stock->qty_available + $firstStock
        ]);

        if ($purchase->product->is_account == 'yes' && $type == 'open_stock') {
            $depositTransaction = $this->ledgerTransactionObserver->createDepositProduct($purchase);

            if ($depositTransaction != null) {
                $accountCapital         = Account::where("default_data", "modal")->first();

                if (!$accountCapital) {
                    throw new \Exception('Gagal Deposit, Silahkan buat Akuntansi untuk menampung Credit Equitas Modal');
                }

                $equitasAccount = AccountTransaction::create([
                    'account_id'                    => $accountCapital->id,
                    'created_by'                    => auth()->user()->id,
                    'transaction_id'                => $transaction->id,
                    'amount'                        => $depositTransaction->amount,
                    'account_transaction_id'        => $depositTransaction->id,
                    'sub_type'                      => $depositTransaction->sub_type,
                    'type'                          => 'credit',
                    'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('deposit_equitas', $depositTransaction->operation_date),
                    'operation_date'                => $depositTransaction->operation_date,
                    'name'                          => $depositTransaction->name
                ]);

                $this->ledgerObserver->updateCashFlowAccount($accountCapital);
                $this->ledgerTransactionObserver->logAccountTransaction($equitasAccount);
            }
        }

        if ($for == '') {
            $this->stockObserver->createHistoryStock('purchase', $purchase, $transaction->id, $purchase->quantity, $stock->variation->all_stock->sum('qty_available'), ($stock->variation->all_stock->sum('qty_available') + $purchase->quantity));
            $this->stockObserver->updatePricing($purchase->variation);
        } else if ($for == 'adjustment') {
            $toRelocate     = $this->receivedProductObserver->changeOrDeletePurchase($purchase, $firstStock);
            $purchaseiD     = $purchase->sell_purchase_first->id ?? null;

            if ($toRelocate['status'] == true) {
                $this->receivedProductObserver->updateSellPurchases($toRelocate['nextPurchases'], $purchaseiD, $variant->id);
            }

            $this->stockObserver->updatePricing($purchase->variation);
        }
    }

    public function handlingUpdateFirstStock(Variation $variant, Stock $stock, Int $firstStock, String $for = '', $capital = null)
    {

        $purchasePrice  = $capital != null ? $capital : $variant->purchase_price;
        $oldStock       = $variant->first_stock;

        $variant->open_stock->transaction->update([
            'created_by'        => auth()->user()->id,
            'total_before_tax'  => $purchasePrice * $firstStock,
            'tax_amount'        => 0,
            'open_stock_product_id' => $variant->product_id,
            'final_total'       => $purchasePrice * $firstStock,
        ]);

        $variant->open_stock->update([
            'variation_id'      => $variant->id,
            'product_id'        => $variant->product_id,
            'quantity'          => $firstStock,
            'purchase_price'    => $purchasePrice,
            'without_discount'  => $purchasePrice,
            'purchase_price_inc_tax'    => $purchasePrice,
            'unit_id'           => $variant->unit_id,
            'unit_qty'          => $firstStock,
        ]);


        $stock->update([
            'qty_available'     => ($stock->qty_available - $oldStock) + $firstStock
        ]);

        $history    = $this->stockObserver->historyLogStock($variant->open_stock->transaction, $variant->id);
        $endStock   = $history->from + $variant->open_stock->quantity;
        $this->stockObserver->updateHistoryLog($history, $variant->open_stock, $variant->open_stock->quantity, $endStock);
        $this->stockObserver->updatePricing($variant);

        if ($variant->product->is_account == 'yes') {

            if ($variant->product->supply_account) {

                $depositTransaction = AccountTransaction::where("transaction_id", $variant->open_stock->transaction_id)
                    ->where("account_id", $variant->product->supply_account->id)
                    ->where("item_id", $variant->open_stock->id)->first();

                if ($depositTransaction) {
                    $depositTransaction->update([
                        'created_by'                    => auth()->user()->id,
                        'amount'                        => $variant->open_stock->subtotal,
                        'type'                          => 'debit',
                    ]);

                    $this->ledgerTransactionObserver->logAccountUpdate($depositTransaction);

                    $accountCapital         = Account::where("default_data", "modal")->first();

                    if (!$accountCapital) {
                        throw new \Exception('Gagal Deposit, Silahkan buat Akuntansi untuk menampung Credit Equitas Modal');
                    }


                    $equitasAccount         = AccountTransaction::where("account_id", $accountCapital->id)->where("account_transaction_id", $depositTransaction->id)->first();

                    if ($equitasAccount) {
                        $equitasAccount->update([
                            'created_by'                    => auth()->user()->id,
                            'amount'                        => $depositTransaction->amount
                        ]);

                        $this->ledgerTransactionObserver->logAccountUpdate($equitasAccount);
                    }
                } else {
                    $depositTransaction = $this->ledgerTransactionObserver->createDepositProduct($variant->open_stock);

                    if ($depositTransaction != null) {
                        $accountCapital         = Account::where("default_data", "modal")->first();

                        if (!$accountCapital) {
                            throw new \Exception('Gagal Deposit, Silahkan buat Akuntansi untuk menampung Credit Equitas Modal');
                        }

                        $equitasAccount = AccountTransaction::create([
                            'account_id'                    => $accountCapital->id,
                            'transaction_id'                => $variant->open_stock->transaction->id ?? '',
                            'created_by'                    => auth()->user()->id,
                            'amount'                        => $depositTransaction->amount,
                            'account_transaction_id'        => $depositTransaction->id,
                            'sub_type'                      => $depositTransaction->sub_type,
                            'type'                          => 'credit',
                            'ref_no'                        => $this->ledgerTransactionObserver->generateRefNo('deposit_equitas', $depositTransaction->operation_date),
                            'operation_date'                => $depositTransaction->operation_date,
                            'name'                          => $depositTransaction->name
                        ]);

                        $this->ledgerObserver->updateCashFlowAccount($accountCapital);
                        $this->ledgerTransactionObserver->logAccountTransaction($equitasAccount);
                    }
                }
            }
        }

        $toRelocate     = $this->receivedProductObserver->changeOrDeletePurchase($variant->open_stock, $firstStock);
        $purchaseiD     = $variant->open_stock->sell_purchase_first->id ?? null;

        if ($toRelocate['status'] == true) {
            $this->receivedProductObserver->updateSellPurchases($toRelocate['nextPurchases'], $purchaseiD, $variant->id);
        }
    }
}
