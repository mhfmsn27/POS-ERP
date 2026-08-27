<?php

namespace App\Http\Controllers\Pos;

use App\Helper;
use App\Http\Controllers\Controller;
use App\Models\Admin\Customer;
use App\Models\Admin\Setting;
use App\Models\Admin\Store;
use App\Models\Product\Product;
use App\Models\Product\ProductDiscount;
use App\Models\Product\Stock;
use App\Models\Product\Unit;
use App\Models\Product\Variation;
use App\Models\Transaction\Sell;
use App\Models\Transaction\ShiftRegister;
use App\Models\Transaction\ShiftRegisterTransaction;
use App\Models\Transaction\Transaction;
use App\Observers\WhatsappNotificationObserver;
use Illuminate\Support\Facades\Session;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use charlieuki\ReceiptPrinter\Item as Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Jenssegers\Agent\Agent;

class PosController extends Controller
{
    private $items;
    private $currency = 'Rp';
    public $whatsappNotification;

    // public function __construct(WhatsappNotificationObserver $whatsappNotification)
    // {
    //     $this->whatsappNotification     = $whatsappNotification;
    // }

    public function index()
    {
        return view('pos.index', ['page' => 'POS']);
    }

    public function entryshift()
    {
        return redirect()->route('pos.index');
    }

    public function product()
    {
        $setting = Setting::first();
        $storeId = Session::get('mystore') ?? my_store();

        $data = Product::with([
            'variant' => function ($q) {
                $q->with('media');
            },
            'variant.stock' => function ($q) use ($storeId) {
                if ($storeId) {
                    $q->where("store_id", $storeId);
                }
            }
        ])->where(function ($q) use ($storeId) {
            $q->whereHas('variant.stock', function ($query) use ($storeId) {
                if ($storeId) {
                    $query->where("store_id", $storeId);
                }
                return $query->selectRaw("sum(qty_available) as qty")->havingRaw('qty > ?', [0]);
            });
        })->orderBy("name", "asc");

        $totalRows = $data->count();
        $data = $data->paginate(20);

        $product = array();
        foreach ($data as $p) {
            if ($p->type == 'single') {
                if ($p->single_variant != null) {
                    $variant = $p->single_variant;
                    $getStock = $variant->stock ? $variant->stock->sum('qty_available') : 0;

                    if ($getStock > 0) {
                        $taxrate = 0;
                        $price = $variant->selling_price;

                        if ($variant->tax_type == 'exclusive') {
                            $taxrate = $variant->taxrate / 100 * $variant->selling_price;
                            $price = $taxrate + $price;
                        }

                        $groceryVal = (float)($variant->grocery ?? 0);
                        $grosir_price = number_format($groceryVal > 0 ? $groceryVal : $price);

                        $list = array(
                            'id'          => $variant->id,
                            'name'        => ($variant->product->name ?? '') . ' - ' . $variant->name,
                            'barcode'     => $variant->sku,
                            'price'       => number_format((float)$price),
                            'image'       => asset($variant->gambar->path ?? '/uploads/image.jpg'),
                            'options'     => null,
                            'stock'       => $getStock,
                            'grosir_mode' => $setting ? $setting->grocery_price : 'no',
                            'g_price'     => $grosir_price
                        );
                        array_push($product, $list);
                    }
                }
            } else {
                if ($p->variant) {
                    foreach ($p->variant as $v) {
                        $getStock = $v->stock ? $v->stock->sum('qty_available') : 0;

                        if ($getStock > 0) {
                            $taxrate = 0;
                            $price = $v->selling_price;

                            if ($v->tax_type == 'exclusive') {
                                $taxrate = $v->taxrate / 100 * $v->selling_price;
                                $price = $taxrate + $price;
                            }

                            $groceryVal = (float)($v->grocery ?? 0);
                            $grosir_price = number_format($groceryVal > 0 ? $groceryVal : $price);

                            $list = array(
                                'id'          => $v->id,
                                'name'        => ($v->product->name ?? '') . ' - ' . $v->name,
                                'barcode'     => $v->sku,
                                'price'       => number_format((float)$price),
                                'image'       => asset($v->gambar->path ?? '/uploads/image.jpg'),
                                'options'     => null,
                                'stock'       => $getStock,
                                'grosir_mode' => $setting ? $setting->grocery_price : 'no',
                                'g_price'     => $grosir_price
                            );
                            array_push($product, $list);
                        }
                    }
                }
            }
        }
        return response()->json([
            'products'  => $product,
            'totalrows' => $totalRows,
            'message'   => __('success')
        ]);
    }

    public function productByName(Request $request)
    {
        $storeId = Session::get('mystore') ?? my_store();

        $data = Variation::with([
            'media',
            'product',
            'stock' => function ($q) use ($storeId) {
                if ($storeId) {
                    $q->where('store_id', $storeId);
                }
            }
        ])->where(function ($q) use ($request) {
            return $q->whereHas('product', function ($query) use ($request) {
                $request->name ? $query->where('name', 'like', '%' . $request->name . '%') : '';
            })->orWhere('name', 'like', '%' . $request->name . '%')->orWhere('sku', 'like', '%' . $request->name . '%');
        });

        $totalRows = $data->count();
        $setting   = Setting::first();
        $data      = $data->paginate(20);

        $product = array();
        foreach ($data as $p) {
            $getStock = $p->stock ? $p->stock->sum('qty_available') : 0;
            if ($getStock > 0) {
                $taxrate = 0;
                $price = $p->selling_price;

                if ($p->tax_type == 'exclusive') {
                    $taxrate = $p->taxrate / 100 * $p->selling_price;
                    $price = $taxrate + $price;
                }

                $grosir_price = number_format($p->grocery);
                if ($p->grocery == 0) {
                    $grosir_price = number_format($price);
                }

                $list = array(
                    'id'          => $p->id,
                    'name'        => ($p->product->name ?? '') . ' - ' . $p->name,
                    'barcode'     => $p->sku,
                    'price'       => number_format($price),
                    'image'       => asset($p->gambar->path ?? '/uploads/image.jpg'),
                    'options'     => null,
                    'stock'       => $getStock,
                    'grosir_mode' => $setting ? $setting->grocery_price : 'no',
                    'g_price'     => $grosir_price
                );
                array_push($product, $list);
            }
        }
        return response()->json([
            'products'  => $product,
            'totalrows' => $totalRows,
            'message'   => __('success')
        ]);
    }

    public function byCategory($id)
    {
        $setting = Setting::first();
        $storeId = Session::get('mystore') ?? my_store();

        if ($id == 'all') {
            return $this->product();
        }

        $data = Variation::with([
            'media',
            'product',
            'stock' => function ($q) use ($storeId) {
                if ($storeId) {
                    $q->where('store_id', $storeId);
                }
            }
        ])->whereHas('product', function ($query) use ($id) {
            $query->where('category_id', $id);
        })->whereHas('stock', function ($query) use ($storeId) {
            if ($storeId) {
                $query->where('store_id', $storeId);
            }
            $query->where('qty_available', '>', 0);
        })->limit(100)->get();

        $product = array();
        foreach ($data as $p) {
            $getStock = $p->stock ? $p->stock->sum('qty_available') : 0;
            if ($getStock > 0) {
                $taxrate = 0;
                $price = $p->selling_price;

                if ($p->tax_type == 'exclusive') {
                    $taxrate = $p->taxrate / 100 * $p->selling_price;
                    $price = $taxrate + $price;
                }

                $grosir_price = number_format($p->grocery);
                if ($p->grocery == 0) {
                    $grosir_price = number_format($price);
                }

                $list = array(
                    'id'          => $p->id,
                    'name'        => ($p->product->name ?? '') . ' - ' . $p->name,
                    'barcode'     => $p->sku,
                    'price'       => number_format($price),
                    'image'       => asset($p->gambar->path ?? '/uploads/image.jpg'),
                    'options'     => null,
                    'stock'       => $getStock,
                    'grosir_mode' => $setting ? $setting->grocery_price : 'no',
                    'g_price'     => $grosir_price
                );
                array_push($product, $list);
            }
        }
        return response()->json([
            'products' => $product,
            'message'  => __('success')
        ]);
    }

    public function getProduct(Request $request, $id)
    {
        $data = Variation::where("id", $id)->orWhere("sku", $id)->first();
        $setting = Setting::first();
        $product = array();

        $getStock = Stock::where('product_id', $data->product_id)->where('store_id', Session::get('mystore'))->where('variation_id', $data->id)->sum('qty_available');
        if ($getStock == 0) {
            return response()->json([
                'products' => $product,
                'message' => 'soldout',
            ]);
        } else {
            $taxrate = 0;
            $price = $data->selling_price;

            if ($request->type == 'grosir') {
                $price = $data->grocery;
                if ($data->grocery == 0) {
                    $price = $data->selling_price;
                }
            }

            if ($data->tax_type == 'exclusive') {
                $taxrate = $data->taxrate / 100 * $data->selling_price;
                $price = $taxrate + $price;
            }

            $unitSell = $data->unit_sell ?? null;
            $getUnit = $data->unit->unit_turunan ?? null;


            $subtotal = $price;

            if ($data->product->unit_type == 'master') {
                if ($unitSell != null) {
                    $valueUnit = $data->unit_sell->value ?? 1;
                    $subtotal = $price * $valueUnit;

                    $listunit = array();
                    if ($getUnit) {
                        foreach ($getUnit as $u) {
                            if ($u->id != $data->unit_sale) {
                                $i['id']    = $u->id;
                                $i['name']  = $u->name;
                                $i['value'] = $u->value;
                                $i['change_price'] = 0;
                                $listunit[] = $i;
                            }
                        }
                    } else {
                        $listunit = array();
                    }
                } else {
                    if ($getUnit == null) {
                        $listunit = array();
                    } else {
                        $listunit = $getUnit;
                    }
                }
            } else {
                foreach ($data->product->satuan as $u) {
                    $i['id']    = $u->id;
                    $i['name']  = $u->name;
                    $i['value'] = $u->value;
                    $i['change_price'] = (float)$u->change_price;
                    $listunit[] = $i;
                }
            }


            $discount = array();
            foreach ($data->multiprice as $m) {
                $item['id'] = $m->id;
                $item['qty']    = $m->qty_min;
                $item['amount'] = (int)$m->discount_amount;
                $item['type']   = $m->amount_type;
                $discount[] = $item;
            }



            $list = array(
                'id'    => $data->id,
                'product_id' => $data->product_id,
                'name'  => substr($data->product->name . ' - ' . $data->name, 0, 16) . "....",
                'price' => number_format($price),
                'fullname' => $data->product->name . ' - ' . $data->name,
                'tprice' => (int)$price,
                'options' => null,
                'stock' => (int)$getStock,
                'subtotal'  => $subtotal,
                'edit_price'    => $setting->price_edit,
                'unit_list'  => $listunit,
                'unit_sale' => $unitSell,
                'multiprice'    => json_encode($discount),
                'get_qty'   => '',
                'get_product'   => '',
            );
            array_push($product, $list);
        }

        return response()->json([
            'product' => $product,
            'message' => __('success')
        ]);
    }

    public function customer()
    {
        $data   = '';
        $getData = Customer::orderBy('id', 'desc')->get();
        foreach ($getData as $c) {
            $data .= '<option value=" ' . $c->id . '"> ' . $c->name . '</option>';
        }
        echo $data;
    }

    public function getHold()
    {
        $data = Transaction::where('type', 'sell')
            ->where('status', 'hold')
            ->get();
        $transaction = array();
        foreach ($data as $d) {
            $list = array(
                'id'    => $d->id,
                'products'  => count($d->sell),
                'invoice' => $d->invoice_no,
                'customer' => $d->customer->name
            );
            array_push($transaction, $list);
        }

        return response()->json([
            'transaction' => $transaction,
            'message' => __('success')
        ]);
    }

    public function deleteHold($id)
    {
        $transaction = Transaction::findOrFail($id);
        foreach ($transaction->sell as $s) {
            $s->delete();
        }
        $transaction->delete();
    }

    public function getbill($id)
    {
        $data = Transaction::findOrFail($id);
        $product = array();
        foreach ($data->sell as $d) {

            $unitSell = $d->unit ?? null;
            $getUnits = Unit::where("id", $d->unit->parent_id ?? null)->first();
            $qtyTotal = $d->qty;
            $tprice = $d->unit_price * $d->qty;
            if ($unitSell != null) {
                $valueUnit = $d->unit->value ?? 1;
                $qtyTotal = $d->qty / $valueUnit;
                $tprice = $d->unit_price * ($qtyTotal * $valueUnit);

                $listunit = array();
                if ($getUnits) {
                    foreach ($getUnits->unit_turunan as $u) {
                        if ($u->id != $d->unit_id) {
                            $i['id']    = $u->id;
                            $i['name']  = $u->name;
                            $i['value'] = $u->value;
                            $listunit[] = $i;
                        }
                    }
                } else {
                    $listunit = array();
                }
            } else {
                if ($getUnits == null) {
                    $listunit = array();
                } else {
                    $listunit = $getUnits->unit_turunan;
                }
            }

            $getStock = Stock::where('product_id', $d->product_id)->where('store_id', Session::get('mystore'))->where('variation_id', $d->variation_id)->sum('qty_available');

            $list = array(
                'id'        => $d->variation_id,
                'bill_id'   => $d->id,
                'product_id' => $d->product_id,
                'qty_product'   => floor($qtyTotal),
                'name'  => substr($d->product->name . ' - ' . $d->variation->name, 0, 16) . "....",
                'price' => number_format($d->unit_price),
                'tprice' => $tprice,
                'unitprice' => $d->unit_price,
                'options' => null,
                'stock' => (int)$getStock,
                'unit_value' => $d->unit_id,
                'unit_list'  => $listunit,
                'unit_sale' => $unitSell
            );
            array_push($product, $list);
        }
        return response()->json([
            'bill' => $product,
            'other' => $data,
            'message' => __('success')
        ]);
    }

    public function deleteBill($id)
    {
        $data = Sell::findOrFail($id);
        return $this->deleteData($data, $id);
    }

    public function printReceipt($id)
    {
        $data = Transaction::findOrFail($id);
        $store = Store::findOrFail(Session::get('mystore'));
        $settings = Setting::first();

        if ($store->printer->type == 'online') {
            if ($settings->rest_api == null) {
                return response()->json([
                    'status'    => 'error',
                    'errors' => "Rest Api Not Found",
                    'message' => 'Silahkan Isi Rest Api Terlebih Dahulu'
                ]);
            }

            if ($store->printer->url == null) {
                return response()->json([
                    'status'    => 'error',
                    'errors' => "Url Not Found",
                    'message' => 'Silahkan Isi Url Printer Terlebih Dahulu'
                ]);
            }

            return response()->json([
                'status'    => 'success',
                'message'   => 'onlineprinter',
                'url'       => $store->printer->url . '?id=' . $id . '&rest_key=' . $settings->rest_api . '&printer_connection=server'
            ]);
        } else {
            $connector = new WindowsPrintConnector($store->printer->name);
            $printer = new Printer($connector);

            $identity = $this->getPrintableHeader(
                'No: ' . $data->ref_no,
                'Jam: ' . $data->created_at
            );

            $subtotal = $this->getPrintableHeader(
                'Subtotal',
                number_format($data->total_before_tax)
            );

            $discount = $this->getPrintableHeader(
                'Diskon',
                number_format($data->discount_amount) . "%"
            );

            $tax = $this->getPrintableHeader(
                'Pajak',
                number_format($data->tax_amount) . "%"
            );

            $shipping = $this->getPrintableHeader(
                'Biaya Antar',
                number_format($data->shipping_charges)
            );

            $other = $this->getPrintableHeader(
                'Biaya Lainnya',
                number_format($data->other_charges)
            );

            $grandtotal = $this->getPrintableHeader(
                'Total',
                number_format($data->final_total)
            );

            $payment = $this->getPrintableHeader(
                'Pembayaran',
                $data->pay_total
            );

            $sell_callback = array();
            foreach ($data->sell as $sell) {
                $total = $sell->unit_price * $sell->qty;
                $list_sell = array(
                    'product_name'        => $sell->product->name . " - (" . $sell->variation->name . ")",
                    'qty' => $sell->qty,
                    'unit_price'  => $sell->unit_price,
                    'subtotal' => $total
                );
                array_push($sell_callback, $list_sell);
            }

            foreach ($sell_callback as $item) {
                $this->addItem(
                    $item['product_name'],
                    $item['qty'],
                    $item['unit_price'],
                    $item['subtotal']
                );
            }

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
            $printer->feed(2);
            $printer->text($data->store->name . "\n");
            $printer->selectPrintMode();
            $printer->text($data->store->address . "\n");
            $printer->feed();
            $printer->selectPrintMode(1);
            $printer->text($identity . "\n");
            $printer->selectPrintMode(1);
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            foreach ($this->items as $item) {
                $printer->text($item);
            }
            $printer->feed();
            $printer->selectPrintMode();
            $printer->text($subtotal . "\n");
            $printer->text($discount . "\n");
            $printer->text($tax . "\n");
            $printer->text($shipping . "\n");
            $printer->text($other . "\n");
            $printer->text($grandtotal . "\n");
            $printer->text($payment . "\n");

            $printer->feed();
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text($store->footer_text . "\n");
            $printer->cut();
            $printer->close();

            return response()->json([
                'message'   => 'offlineprinter',
                'text'      => "Process Printer Berhasil, Pastikan Anda benar-benar terhubung dengan printer"
            ]);
        }
    }

    public function getPrintableHeader($left_text, $right_text, $is_double_width = false)
    {
        $cols_width = $is_double_width ? 8 : 16;

        return str_pad($left_text, $cols_width) . str_pad($right_text, $cols_width, ' ', STR_PAD_LEFT);
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    public function addItem($name, $qty, $price, $subtotal)
    {
        $item = new Item($name, $qty, $price, $subtotal);
        $item->setCurrency($this->currency);
        $this->items[] = $item;
    }

    public function printpage($id)
    {
        $data = Transaction::findOrFail($id);
        $store = Store::findOrFail(Session::get('mystore'));
        $settings = Setting::first();
        return view('pos.print', ["page" => "Print Struk Pembayaran"], compact('data', 'store', 'settings'));
    }

    public function printDownload($id)
    {

        $getInvoice = Transaction::where("id", $id)->first();

        $sell_callback = array();
        foreach ($getInvoice->sell as $sell) {
            $total = $sell->unit_price * $sell->qty;
            $list_sell = array(
                'product_name'        => $sell->product->name . " - (" . $sell->variation->name . ")",
                'qty' => $sell->qty,
                'unit_price'  => $sell->unit_price,
                'subtotal' => $total
            );
            array_push($sell_callback, $list_sell);
        }

        $data = response()->json([
            'toko'  => $getInvoice->store->name,
            'alamat'    => $getInvoice->store->address,
            'footer'    => $getInvoice->store->footer_text,
            'transaction_date' => \Timezone::convertToLocal($getInvoice->created_at, 'd M, Y - H:i, '),
            'ref_no' => $getInvoice->ref_no,
            'subtotal'  => number_format($getInvoice->total_before_tax),
            'diskon'    => number_format($getInvoice->discount_amount) . '%',
            'pajak'     => number_format($getInvoice->tax_amount) . "%",
            'shipping'  => number_format($getInvoice->shipping_charges),
            'othercost' => number_format($getInvoice->other_charges),
            'total'     => number_format($getInvoice->final_total),
            'paytotal'  => $getInvoice->pay_total,
            'item'      => $sell_callback
        ]);

        header('Content-disposition: attachment; filename=printer_json.json');
        header('Content-type: application/json');
        echo $data;
    }

    public function getVoucher(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'voucher_code'      => 'required'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'pesan' => $validator->errors(),
                    'status' => 'error'
                ]);
            }
        }

        $getVoucher = ProductDiscount::where("type", "voucher")->where("code", $request->voucher_code)->first();
        if ($getVoucher == null) {
            return response()->json(['status' => false, 'pesan' => 'Maaf, Voucher dengan kode ' . $request->voucher_code . " Tidak ada"]);
        }

        if ($getVoucher->voucher_use == 'limited') {
            if ($getVoucher->voucher_limit_number <= count($getVoucher->voucherclaim)) {
                return response()->json(['status' => false, 'pesan' => "Maaf, Limit Penggunaan Voucher ini sudah mencapai batasnya"]);
            }
        }

        if ($getVoucher->voucher_limit_date != null) {
            if ($getVoucher->voucher_limit_date < date("Y-m-d")) {
                return response()->json(['status' => false, 'pesan' => "Maaf, Voucher ini sudah kadaluarsa sejak tanggal " . $getVoucher->voucher_limit_date . ""]);
            }
        }

        return response()->json([
            'status'            => true,
            'id'                => $getVoucher->id,
            'voucher_name'       => $getVoucher->voucher_name,
            'voucher_type'      => $getVoucher->type_bonus,
            'voucher_amount'    => (int)$getVoucher->discount_amount,
            'voucher_limit_date'    => $getVoucher->voucher_limit_date,
        ]);
    }
}
