<?php

namespace App\Observers;

use App\Helper;
use App\Models\Product\Stock;
use App\Models\Product\Unit;
use App\Models\Product\Variation;
use App\Models\Product\VoucherClaim;
use App\Models\Transaction\PaymentMethod;
use App\Models\Transaction\Purchase;
use App\Models\Transaction\Sell;
use App\Models\Transaction\SellPurchase;
use App\Models\Transaction\ShiftRegister;
use App\Models\Transaction\ShiftRegisterTransaction;
use App\Models\Transaction\Transaction;
use App\Models\Transaction\TransactionPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionObserver
{
    public function saveOrUpdate(Request $request)
    {
        DB::beginTransaction();

        try {
            $data = !empty($request->transaction_id) && $request->transaction_id != null
                ? Transaction::where("id", $request->transaction_id)->lockForUpdate()->first()
                : new Transaction();

            // Buat Data Baru Jika Data Sebelumnya tidak ada
            if (!$data->exists) {
                if (class_exists(\App\Services\Transaction\TransactionSequenceService::class)) {
                    $sequenceService = app(\App\Services\Transaction\TransactionSequenceService::class);
                    $seqData = $sequenceService->generateNextReference('SL', 'sell', my_store());
                    $data->invoice_no = $seqData['invoice_no'];
                    $data->ref_no     = $seqData['ref_no'];
                } else {
                    // Fallback
                    $getTransaction = Transaction::where("type", "sell")
                        ->whereDate("created_at", date("Y-m-d"))
                        ->count() + 1;

                    $invoiceNumber    = sprintf("%05d", $getTransaction);
                    $refNo            = Helper::transactionKey('SL', $invoiceNumber);
                    $data->invoice_no = $invoiceNumber;
                    $data->ref_no     = $refNo;
                }
            }

            // Commit Transaction Jika Tidak Ada Masalah
            DB::commit();

            return $data;
        } catch (\Throwable $e) {
            // Rollback Transaksi
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function saveProductDetail(Transaction $transaction, Object $products, String $type)
    {


        DB::beginTransaction();

        try {

            foreach ($products as $product) {


                $getUnits               = Unit::where("id", $product['product_unit'])->first();
                $unitId                 = null;
                $qtyTotal               = 0;

                $variation              = Variation::find($product['variation_id']);
                $price                  = $variation->selling_price;

                if ($getUnits) {
                    $qtyTotal           = (int)$product['qty'] * $getUnits->value;
                    $unitId             = $getUnits->id;

                    if ($getUnits->type == 'product') {
                        $price      = $getUnits->change_price;
                    }
                } else {
                    $qtyTotal           = (int)$product['qty'];
                }

                $price                  = Helper::fresh_aprice($product['unit_cost']);

                $data = array(
                    'transaction_id'        => $transaction->id, 
                    'variation_id'          => $product['variation_id'],
                    'product_id'            => $product['product_id'],
                    'qty'                   => $qtyTotal,
                    'unit_id'               => $unitId,
                    'unit_price'            => $price,
                    'unit_price_before_disc' => $price,

                );
                if ($product['bill'] != null) {
                    $sell = Sell::find($product['bill']);
                    $sell->update($data);
                } else {
                    $sell = Sell::create($data);
                }

                if ($type == 'final') {
                    $getPurchase = $this->getPurchaseQtyHaving($sell);

                    $totalQty = $sell->qty;
                    foreach ($getPurchase as $p) {
                        $getPO = Purchase::find($p->id);
                        $sellpurchase = new SellPurchase();
                        if ($getPO != null) {
                            $readyQty = $p->quantity - $p->qty_sum;

                            if ($readyQty >= $totalQty) {
                                $fixqty = $totalQty;
                                $totalQty -= $totalQty;
                            } else {
                                $fixqty = $readyQty;
                                $totalQty -= $fixqty;
                            }

                            $getPO->qty_sold      = $getPO->qty_sold + $fixqty;
                            $getPO->save();


                            $sellpurchase->sell_id  = $sell->id;
                            $sellpurchase->purchase_id  = $p->id;
                            $sellpurchase->qty          = $fixqty;
                            $sellpurchase->save();
                        }

                        if ($totalQty <= 0) {
                            break;
                        }
                    }

                    // Pessimistic Lock untuk pengurangan stok aman dari race conditions
                    $stock = Stock::withoutGlobalScopes()
                        ->where('product_id', $sell->product_id)
                        ->where('variation_id', $sell->variation_id)
                        ->where('store_id', $sell->store_id)
                        ->lockForUpdate()
                        ->first();

                    if ($stock) {
                        $stock->qty_available = $stock->qty_available - $sell->qty;
                        $stock->save();
                    } else {
                        Stock::create([
                            'product_id'    => $sell->product_id,
                            'variation_id'  => $sell->variation_id,
                            'store_id'      => $sell->store_id,
                            'qty_available' => 0 - $sell->qty,
                        ]);
                    }
                }
            }


            DB::commit();
        } catch (\Throwable $e) {
            // Rollback Transaksi
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function saveVoucher(Transaction $transaction, Request $request)
    {
        DB::beginTransaction();
        try {
            $voucheruse = VoucherClaim::create([
                'voucher_id'            => $request->voucher_id,
                'transaction_id'        => $transaction->id,
                'type'                  => $request->voucher_type,
                'name'                  => $request->voucher_name,
                'amount'                => Helper::fresh_aprice($request->voucher_amount)
            ]);

            DB::commit();

            return $voucheruse;
        } catch (\Throwable $e) {
            // Rollback Transaksi
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function transactionPayment(Transaction $transaction, Request $request)
    {
        DB::beginTransaction();
        try {
            $payment = TransactionPayment::create([
                'created_by'      => $transaction->created_by,
                'transaction_id'  => $transaction->id,
                'amount'          => $transaction->payment_status == 'paid' ? $transaction->final_total :  Helper::fresh_aprice($request->on_pay),
                'method'          => $request->payment_methode == 'cash' ? 'Cash' : PaymentMethod::find($request->payment_methode)->name,
            ]);

            if ($transaction->store->shift_register == 'active') {

                $getShift = ShiftRegister::whereYear("created_at", date('Y'))
                    ->whereMonth("created_at", date('m'))
                    ->whereDay("created_at", date('d'))
                    ->where("status", "open")
                    ->where("user_id", $transaction->created_by)
                    ->where("store_id", $transaction->store_id)
                    ->first();

                if ($getShift != null) {
                    $shift = new ShiftRegisterTransaction();
                    $shift->shift_register_id = $getShift->id;
                    $shift->amount =  $transaction->payment_status == 'paid' ? $transaction->final_total : $payment->amount;

                    $method = $payment->method;

                    if ($payment->method == 'cash' || $payment->method == 'Cash') {
                        $method = 'cash';
                    }

                    $shift->pay_method = $method;
                    $shift->transaction_type = 'sell';
                    $shift->transaction_id = $transaction->id;
                    $shift->save();
                }
            }


            // Enterprise Loyalty Points Auto-Credit
            if (!empty($transaction->customer_id) && $transaction->customer_id > 0) {
                try {
                    app(\App\Services\Crm\CustomerLoyaltyService::class)->addPointsForSale(
                        (int)$transaction->customer_id,
                        (int)$transaction->store_id,
                        (int)$transaction->id,
                        (float)$transaction->final_total
                    );
                } catch (\Throwable $loyaltyEx) {
                    \Illuminate\Support\Facades\Log::warning("Loyalty points auto-credit error: " . $loyaltyEx->getMessage());
                }
            }

            // Enterprise FEFO Batch Deduction
            try {
                $batchService = app(\App\Services\Inventory\BatchExpiryService::class);
                $sellList = $transaction->sell ?? ($transaction->items ?? []);
                foreach ($sellList as $sellItem) {
                    $vId = is_array($sellItem) ? ($sellItem['variation_id'] ?? null) : ($sellItem->variation_id ?? null);
                    $sQty = is_array($sellItem) ? ($sellItem['qty'] ?? 0) : ($sellItem->qty ?? 0);
                    if (!empty($vId) && (float)$sQty > 0) {
                        $batchService->deductStockFEFO((int)$vId, (int)$transaction->store_id, (float)$sQty);
                    }
                }
            } catch (\Throwable $batchEx) {
                \Illuminate\Support\Facades\Log::warning("FEFO batch auto-deduction error: " . $batchEx->getMessage());
            }

            // Enterprise CRMHUB Omnichannel Digital Receipt
            try {
                app(\App\Services\Crm\OmnichannelReceiptService::class)->sendDigitalReceipt((int)$transaction->id);
            } catch (\Throwable $receiptEx) {
                \Illuminate\Support\Facades\Log::warning("Digital receipt auto-dispatch error: " . $receiptEx->getMessage());
            }

            // Enterprise Kitchen Display System (KDS) Auto-Routing
            try {
                $kdsItems = [];
                $sellList = $transaction->sell ?? ($transaction->items ?? []);
                foreach ($sellList as $item) {
                    $kdsItems[] = [
                        'name'          => is_array($item) ? ($item['name'] ?? 'Menu') : ($item->product->name ?? ($item->item_name ?? 'Menu')),
                        'qty'           => is_array($item) ? ($item['qty'] ?? 1) : ($item->qty ?? 1),
                        'category_name' => is_array($item) ? ($item['category_name'] ?? '') : ($item->product->category->name ?? ''),
                    ];
                }
                if (!empty($kdsItems)) {
                    app(\App\Services\Pos\KitchenOrderService::class)->createTickets(
                        (int)$transaction->store_id,
                        (int)$transaction->id,
                        $kdsItems,
                        $transaction->table_number ?? 'Kasir',
                        $transaction->customer->name ?? 'Tamu'
                    );
                }
            } catch (\Throwable $kdsEx) {
                \Illuminate\Support\Facades\Log::warning("KDS ticket auto-routing warning: " . $kdsEx->getMessage());
            }

            DB::commit();

            return $payment;
        } catch (\Throwable $e) {
            // Rollback Transaksi
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Components Data for get Purchase Available
     */

    public function getPurchaseQtyHaving(Sell $sell)
    {
        return Purchase::select(DB::raw(
            "id, quantity, qty_sold, qty_transfer, qty_adjusted,  qty_expire, product_id, variation_id, store_id,
            SUM(IFNULL(qty_sold, 0) + IFNULL(qty_adjusted, 0) + IFNULL(qty_return, 0) + IFNULL(qty_transfer, 0) + IFNULL(qty_expire, 0)) AS qty_sum,
            SUM(quantity) as qty_total, purchase_price_inc_tax"
        ))
            ->where('product_id', $sell->product_id)
            ->where('variation_id', $sell->variation_id)
            ->where('store_id', $sell->store_id)
            ->havingRaw("qty_total >= qty_sum")
            ->orderBy('id', 'asc')
            ->get();
    }
}
