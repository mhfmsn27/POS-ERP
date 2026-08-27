<?php

namespace App\Http\Controllers\Api\Transaction;
 
use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\StockOpname\StockOpnameRequest;
use App\Http\Resources\Transaction\StockOpname\StockOpnameDetailResource;
use App\Http\Resources\Transaction\StockOpname\StockOpnameListResource;
use App\Models\Product\Unit;
use App\Models\Product\Variation;
use App\Models\Transaction\SellPurchase;
use App\Models\Transaction\Transaction;
use App\Observers\Account\LedgerTransactionObserver;
use App\Observers\Inventory\StockObserver;
use App\Observers\Transaction\Purchase\PurchaseObserver;
use App\Observers\Transaction\StockOpnameObserver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class StockOpnameController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Stock Opname Controller
    |--------------------------------------------------------------------------
    */

    protected $stockOpnameObserver;
    protected $stockObserver;
    protected $purchaseObserver;
    protected $ledgerTransactionObserver;

    public function __construct(StockOpnameObserver $stockOpnameObserver, StockObserver $stockObserver, PurchaseObserver $purchaseObserver, LedgerTransactionObserver $ledgerTransactionObserver)
    {
        $this->stockOpnameObserver          = $stockOpnameObserver;
        $this->stockObserver                = $stockObserver;
        $this->purchaseObserver             = $purchaseObserver;
        $this->ledgerTransactionObserver    = $ledgerTransactionObserver;
    }

    /*
    |--------------------------------------------------------------------------
    | 1. List Data
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        abort_if(Gate::denies('adjustment_view'), 403);
        $limit          = $request->limit ? $request->limit : 10;
        $data           = $this->stockOpnameObserver->getData($request);

        $totalRows      = $data->count();
        $transactions   = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'transactions'  => StockOpnameListResource::collection($transactions),
        ], 200);
    }


    /*
    |--------------------------------------------------------------------------
    | 2. Create Data
    |--------------------------------------------------------------------------
    */

    public function store(StockOpnameRequest $request)
    {

        abort_if(Gate::denies('adjustment_create'), 403);
        
        try {

            DB::beginTransaction();

            // Get Ref Transaction
            $getTransaksi       = Transaction::where("type", "stock_adjustment")->whereDate("created_at", date("Y-m-d"))->count() + 1;
            $invoiceNumber      = sprintf("%05d", $getTransaksi);
            $refNo              = "SO" . date("Y/m/d") . "/" . $invoiceNumber;

            // Create New Transaction For Stock Opname
            $transaction        = $this->stockOpnameObserver->createTransaction($request, $refNo, $invoiceNumber);

            // Item Stock Opname
            foreach ($request->items as $d) {

                $endStock       = 0;
                $qtyAdjustment  = 0;

                $getUnits       = Unit::find($d['unit']);
                $variation      = Variation::find($d['variation_id']);
                $getFrom        = $this->stockObserver->createData($variation, $transaction->warehouse_id);
                $qtyAdjustment  = $getUnits ? ((int)$d['qty'] * $getUnits->value) : (int)$d['qty'];
                $adjustmentType = $qtyAdjustment > $getFrom->qty_available ? 'add' : 'min';
                $hasilQty       = $adjustmentType == 'add' ? ($qtyAdjustment - $getFrom->qty_available) : ($getFrom->qty_available - $qtyAdjustment);

                // Update Stock
                $firstStock     = $variation->all_stock->sum('qty_available') ?? 0;

                $attribute      = array(
                    'type'          => $adjustmentType,
                    'quantity'      => $getFrom->qty_available,
                    'variation_id'  => $variation->id,
                    'product_id'    => $variation->product_id,
                    'hasil_qty'     => $hasilQty,
                    'unit_price'    => $variation->modal_price,
                    'purchase_price'    => $d['purchase_price']
                );

                if ($adjustmentType != 'add') {
                    if ($hasilQty > $getFrom->qty_available) {
                        $minGet                 = $hasilQty - $getFrom->qty_available;
                        $adjustQty              = $hasilQty - $minGet;
                        $qtyAdjustment          = $adjustQty;
                        $getFrom->qty_available = $getFrom->qty_available - $qtyAdjustment;
                    } else {
                        $getFrom->qty_available = $getFrom->qty_available - $hasilQty;
                    }
                }

                $getFrom->save(); 

                $endStock       = $variation->all_stock->sum('qty_available') -  $hasilQty;

                // Create Stock Opname Item
                $adjustment     = $this->stockOpnameObserver->createItems($attribute, (int)$qtyAdjustment, $getUnits, $transaction);

                // Create History Stock
                $this->stockObserver->createHistoryStock($adjustmentType == 'add' ? 'adjustment_add' : 'adjustment', $adjustment, $transaction->id, $adjustment->qty_adjustment, $firstStock, $endStock);

                // Get Purchase Data For Update
                if ($adjustmentType == 'add') {

                    if ($adjustment->product->supply_account) {
                        $this->ledgerTransactionObserver->productStockOpname($adjustment, 'add_stock');
                    }

                    $getPurchase    = $this->purchaseObserver->getDataByItem($adjustment->variation_id, $transaction->store_id)->orderBy("created_at", "desc")->limit(1)->get();
                    $this->purchaseObserver->handlingFirstStock($variation, $getFrom, $adjustment->qty_adjustment, 'adjustment', $adjustment->purchase_price, 'adjustment_add');
                } else {

                    if ($adjustment->product->supply_account) {
                        $this->ledgerTransactionObserver->productStockOpname($adjustment, 'min_stock');
                    }

                    $this->stockObserver->updatePricing($adjustment->variation);

                    $getPurchase    = $this->purchaseObserver->getPurchaseQtyHaving($adjustment->product_id, $adjustment->variation_id, $transaction->store_id);

                    $totalQty = $hasilQty;

                    foreach ($getPurchase as $p) {
                        $readyQty       = $p->qty_total - $p->qty_sum;
                        $allocatedQty   = min($totalQty, $readyQty);

                        if ($allocatedQty > 0) {
                            // For Add Update
                            $p->qty_adjusted += $allocatedQty;
                            $p->save();

                            SellPurchase::create([
                                'unit_id'               => $adjustment->variation->unit_id ?? null,
                                'stock_adjustment_id'   => $adjustment->id,
                                'purchase_id'           => $p->id,
                                'qty'                   => $allocatedQty
                            ]);

                            $totalQty -= $allocatedQty;
                        }

                        if ($totalQty <= 0) {
                            break;
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'message'   => "Transaksi Stok Opname berhasil dilakukan",
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message'   => $e->getMessage() . ' ' . $e->getLine(),
                'line'      => $e->getLine(),
                'file'      => $e->getFile(),
                'status' => false
            ], 400);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 3. Detail Data
    |--------------------------------------------------------------------------
    */

    public function detail(Transaction $transaction)
    {
        abort_if(Gate::denies('adjustment_view'), 403);

        return response()->json([
            'details' => StockOpnameDetailResource::make($transaction)
        ], 200);
    }
}
