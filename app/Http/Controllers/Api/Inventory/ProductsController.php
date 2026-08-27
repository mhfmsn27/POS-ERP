<?php

namespace App\Http\Controllers\Api\Inventory;

use App\Exports\Inventory\ProductsExport;
use App\Exports\Inventory\ProductTaxFormatExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inventory\Products\CreateProductRequest;
use App\Http\Requests\Inventory\Products\ProductAccountRequest;
use App\Http\Requests\Inventory\Products\UpdateProductRequest;
use App\Http\Requests\Inventory\Products\UpdateVariationRequest;
use App\Http\Resources\Inventory\Products\ProductAccountantResource;
use App\Http\Resources\Inventory\Products\ProductDetailResource;
use App\Http\Resources\Inventory\Products\ProductListResource;
use App\Http\Resources\Inventory\Products\ProductMediaResource;
use App\Http\Resources\Inventory\Products\ProductVariationDetailResource;
use App\Http\Resources\Inventory\Products\VariatioDetailResource;
use App\Http\Resources\Setting\WarehouseResource;
use App\Imports\Inventory\ProductsImport;
use App\Models\Admin\AccountSetting;
use App\Models\Admin\Warehouse;
use App\Models\Product\Brand;
use App\Models\Product\Category;
use App\Models\Product\Media;
use App\Models\Product\Product;
use App\Models\Product\Unit;
use App\Models\Product\Variation;
use App\Observers\Inventory\MediaObserver;
use App\Observers\Inventory\ProductObserver;
use App\Observers\Inventory\StockObserver;
use App\Observers\Inventory\VariationObserver;
use App\Observers\Transaction\Purchase\PurchaseObserver;
use App\Process\MasterData\UploadImageProcess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Response;

class ProductsController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Products Controller
    |--------------------------------------------------------------------------
    */

    protected $productObserver;
    protected $uploadImageProcess;
    protected $variationObserver;
    protected $mediaObserver;
    protected $stockObserver;
    protected $purchaseObserver;

    public function __construct(StockObserver $stockObserver, ProductObserver $productObserver, UploadImageProcess $uploadImageProcess, VariationObserver $variationObserver, MediaObserver $mediaObserver, PurchaseObserver $purchaseObserver)
    {
        $this->productObserver      = $productObserver;
        $this->uploadImageProcess   = $uploadImageProcess;
        $this->variationObserver    = $variationObserver;
        $this->mediaObserver        = $mediaObserver;
        $this->stockObserver        = $stockObserver;
        $this->purchaseObserver     = $purchaseObserver;
    }

    /*
    |--------------------------------------------------------------------------
    | 1. Products List
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {

        abort_if(Gate::denies('product_view'), 403);

        $limit = $request->input('limit', 10);
        $data   = $this->productObserver->getData($request);

        $totalRows  = $data->count();
        $products   = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'warehouses'    => WarehouseResource::collection(Warehouse::all()),
            'products'      => ProductListResource::collection($products),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 2. Create Product
    |--------------------------------------------------------------------------
    */

    public function create(CreateProductRequest $request)
    {


        abort_if(Gate::denies('product_create'), 403);

        try {

            DB::beginTransaction();


            $products = $this->productObserver->createData($request); // Create Product Data

            // Looping For All Variations Data Insert 
            foreach ($request->variations as $variation) {
                $this->variationObserver->createData($variation, $products); // Create Variations
            }

            // Looping For All Media Data Insert 
            foreach ($request->media as $gallery) {
                $image = "";
                if (isset($gallery['url'])) {
                    $image = $this->uploadImageProcess->uploadFile($gallery['url'], Hash::make($gallery['name']), 'uploads/products/' . strtolower(preg_replace("/[^A-Za-z0-9]/", "-", $request->info_general['name'])) . '/');
                    $this->mediaObserver->createData($image, $products->id, 'App\Models\Product\Product'); // Create Image Media
                }
            }

            DB::commit();

            return response()->json([
                'message'   => 'Tambah Data berhasil di lakukan',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message'   => $e->getMessage(),
                'line'      => $e->getLine(),
                'file'      => $e->getFile(),
                'status'    => false
            ], 409);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | 4. Product Details
    |--------------------------------------------------------------------------
    */

    public function details(Product $product)
    {
        abort_if(Gate::denies('product_view'), 403);
        return response()->json([
            'details'       => ProductDetailResource::make($product),
            'account'       => ProductAccountantResource::make($product),
            'variations'    => ProductVariationDetailResource::collection($product->variant),
            'gallery'       => ProductMediaResource::collection($product->gallery)
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 5. Update Product
    |--------------------------------------------------------------------------
    */

    public function update(UpdateProductRequest $request, Product $product)
    {

        abort_if(Gate::denies('product_update'), 403);

        if (count($request->media) + Media::where('imageable_type', 'App\Models\Product\Product')->where('imageable_id', $product->id)->count() > 5) {
            return response()->json([
                'message'   => 'Limit batas upload gambar, silahkan hapus beberapa gambar terlebih dahulu',
                'status'    => false
            ], 422);
        }

        try {

            DB::beginTransaction();
            $this->productObserver->updateProduct($request, $product);

            foreach ($request->media as $gallery) {
                $image = "";
                if (isset($gallery['url'])) {
                    $image = $this->uploadImageProcess->uploadFile($gallery['url'], $gallery['name'], 'uploads/' . auth()->user()->business_id . '/products/' . strtolower(preg_replace("/[^A-Za-z0-9]/", "-", $request->name)) . '/');
                    $this->mediaObserver->createData($image, $product->id, 'App\Models\Product\Product');
                }
            }

            DB::commit();

            return response()->json([
                'message'       => 'Edit Data berhasil di lakukan',
                'gallery'       => ProductMediaResource::collection($product->gallery),
                'status'        => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message'   => $e->getMessage(),
                'file'      => $e->getFile(),
                'status'    => false
            ], 409);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | 7. Update Variations
    |--------------------------------------------------------------------------
    */

    public function updateVariations(UpdateVariationRequest $request, Product $product)
    {
        try {

            abort_if(Gate::denies('product_update'), 403);

            DB::beginTransaction();

            $variation = $this->variationObserver->updateData($request, $product);

            DB::commit();

            return response()->json([
                'message'       =>  'Edit Data berhasil di lakukan',
                'variation_id'  => $variation->id,
                'status'        => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message'   => $e->getMessage(),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
                'status'    => false
            ], 409);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | 7. Update Account
    |--------------------------------------------------------------------------
    */

    public function updateAccount(ProductAccountRequest $request, Product $product)
    {

        abort_if(Gate::denies('product_update'), 403);

        try {

            DB::beginTransaction();

            $product->update([
                'supply'            => $request->supply['id'],
                'sale'              => $request->sale['id'],
                'retur_sale'        => $request->return_sale['id'],
                'discount_sale'     => $request->discount['id'],
                'sent'              => $request->sent['id'],
                'cost'              => $request->cost['id'],
                'retur_purchase'    => $request->retur_purchase['id'],
                'supplier_debt'     => $request->supplier_debt['id'],
            ]);

            DB::commit();
            return response()->json([
                'message'       =>  'Edit Data berhasil di lakukan',
                'status'        => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message'   => $e->getMessage(),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
                'status'    => false
            ], 409);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 8. Delete Variations Product
    |--------------------------------------------------------------------------
    */

    public function deleteVariation(Variation $variation)
    {

        abort_if(Gate::denies('product_delete'), 403);

        if ($variation->all_stock->sum('quantity') > 0) {
            return response()->json([
                'message'       => 'Data Variation ini sudah tidak dapat di hapus lagi',
                'status'        => false
            ], 422);
        }

        try {

            DB::beginTransaction();

            $this->variationObserver->deleteData($variation);

            DB::commit();

            return response()->json([
                'message'   =>  'Hapus Data berhasil di lakukan',
                'status'    => true
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message'   => $e->getMessage(),
                'file'      => $e->getFile(),
                'status'    => false
            ], 409);
        }
    }



    /*
    |--------------------------------------------------------------------------
    | 11. Delete Permanently Product
    |--------------------------------------------------------------------------
    */

    public function delete(Product $product)
    {

        abort_if(Gate::denies('product_delete'), 403);

        if ($product->history->sum('qty') > 0) {
            return response()->json([
                'message'       => 'Data Produk ini sudah tidak dapat di hapus lagi',
                'status'        => false
            ], 422);
        }

        try {
            DB::beginTransaction();

            $this->productObserver->deleteData($product);

            DB::commit();

            return response()->json([
                'status'        => true,
                'message'       =>  'Hapus Data berhasil di lakukan',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message'       => $e->getMessage() . ' - Line: ' . $e->getLine(),
                'status'        => false
            ], 409);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 12. Delete Permanently Product
    |--------------------------------------------------------------------------
    */

    public function deleteMedia(Media $media)
    {

        abort_if(Gate::denies('product_delete'), 403);


        try {
            $media->delete();
            return response()->json([
                'status'        => true,
                'message'       => 'Berhasil menghapus media',
            ]);
        } catch (\Exception $e) { 
            return response()->json([
                'message'       => $e->getMessage() . ' - Line: ' . $e->getLine(),
                'status'        => false
            ], 409);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | 13. Delete Bulk Permanently Product
    |--------------------------------------------------------------------------
    */

    public function deleteManyProduct(Request $request)
    {


        abort_if(Gate::denies('product_delete'), 403);

        try {

            DB::beginTransaction();

            foreach ($request->product_selected as $p) {
                $product = Product::where("id", $p['id'])->first(['id']);
                $this->productObserver->deleteData($product);
            }

            DB::commit();

            return response()->json([
                'status'        => true,
                'message'       =>  'Hapus Data berhasil di lakukan',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message'   => $e->getMessage() . ' - Line: ' . $e->getLine(),
                'status'    => false
            ], 409);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 15. Import Product
    |--------------------------------------------------------------------------
    */

    public function import(Request $request)
    {

        abort_if(Gate::denies('product_create'), 403);

        $this->validate($request, [
            'file'  => 'mimes:xlsx'
        ]);


        if ($request->file) {

            $import = Excel::toArray(new ProductsImport(), request()->file('file'));

            if (count($import[0]) > 0) {


                try {

                    DB::beginTransaction();

                    $accountSetting = AccountSetting::first([
                        'product_supply',
                        'product_sale',
                        'product_retur_sale',
                        'product_discount_sale',
                        'product_sent',
                        'product_cost',
                        'product_retur_purchase',
                        'product_supplier_debt',
                    ]);

                    foreach ($import[0] as $d) {

                        if ($d['nama_produk'] != null) {

                            $category = Category::firstOrCreate(
                                [
                                    'name' => strtoupper($d['kategori'])
                                ]
                            );

                            $brand = null;

                            if ($d['brand'] != null) {

                                $brand = Brand::firstOrCreate(
                                    [
                                        'name' => strtoupper($d['brand'])
                                    ]
                                );

                                $item['brand'] = array(
                                    'id' => $brand->id,
                                    'name' => $brand->name,
                                );
                            }


                            $product = Product::create([
                                'name'                  => $d['nama_produk'],
                                'sku'                   => rand(),
                                'barcode_type'          => $d['tipe_barcode'] != null ? $d['tipe_barcode'] : 'ean13',
                                'category_id'           => $category->id,
                                'brand_id'              => $brand != null ? $brand->id : null,
                                'type'                  => $d['nama_variant'] != null ? 'variable' : 'single',
                                'alert_quantity'        => (int)$d['peringatan_qty'],
                                'is_account'            => $d['gunakan_akuntansi'],
                                'supply'                => $d['gunakan_akuntansi'] == 'yes' && $accountSetting ? $accountSetting->product_supply : null,
                                'sale'                  => $d['gunakan_akuntansi'] == 'yes' && $accountSetting ? $accountSetting->product_sale : null,
                                'retur_sale'            => $d['gunakan_akuntansi'] == 'yes' && $accountSetting ? $accountSetting->product_retur_sale : null,
                                'discount_sale'         => $d['gunakan_akuntansi'] == 'yes' && $accountSetting ? $accountSetting->product_discount_sale : null,
                                'sent'                  => $d['gunakan_akuntansi'] == 'yes' && $accountSetting ? $accountSetting->product_sent : null,
                                'cost'                  => $d['gunakan_akuntansi'] == 'yes' && $accountSetting ? $accountSetting->product_cost : null,
                                'retur_purchase'        => $d['gunakan_akuntansi'] == 'yes' && $accountSetting ? $accountSetting->product_retur_purchase : null,
                                'supplier_debt'         => $d['gunakan_akuntansi'] == 'yes' && $accountSetting ? $accountSetting->product_supplier_debt : null,
                                'is_stock'              => $d['barang_persediaan'] == 'yes' ? 'yes' : 'no'
                            ]);

                            //  $item['sku']                = $d['sku'];

                            if ($d['nama_variant'] != null) {


                                $variantName    = explode(",", $d['nama_variant']);
                                $variantSku     = explode(",", $d['sku']);
                                $modalPrice     = explode(",", $d['harga_modal']);
                                $sellPrice      = explode(",", $d['harga_jual']);

                                $taxrate        = explode(",", $d['pajak']);
                                $unitID         = explode(",", $d['unit_dasar']);
                                $unitSale       = explode(",", $d['unit_penjualan']);
                                $unitPurchase   = explode(",", $d['unit_pembelian']);
                                $barcode        = explode(",", $d['barcode_produk']);
                                $stock          = explode(",", $d['stock']);
                                $taxPurchase    = explode(",", $d['pajak_pembelian']);
                                $taxSell        = explode(",", $d['pajak_penjualan']);


                                for ($x = 0; $x < count($variantName); $x++) {

                                    $unit = null;
                                    if (isset($unitID[$x])) {
                                        $unitName   = strtolower(preg_replace("/[^0-9a-zA-Z]/", "", $unitID[$x]));
                                        $unit = Unit::where("name", $unitName)->where("is_root_parent", 0)->first();
                                    }


                                    $unitS = null;
                                    if (isset($unitSale[$x])) {
                                        $unitS = Unit::where("name",  $unitSale[$x])->first();
                                    }

                                    $unitP  = null;
                                    if (isset($unitPurchase[$x])) {
                                        $unitP = Unit::where("name",  $unitPurchase[$x])->first();
                                    }

                                    $priceIncludeTax    =  !empty($sellPrice[$x]) ? (int)$sellPrice[$x] : 0;
                                    $taxType            =  'inclusive';

                                    if (isset($taxrate[$x])) {

                                        if ((int)$taxrate[$x] > 0 && $priceIncludeTax > 0) {
                                            $taxType            =  'exclusive';
                                            $priceTax           = (int)$taxrate[$x] / 100 * (int)$priceIncludeTax;
                                            $priceIncludeTax    = (int)$priceIncludeTax + (int)$priceTax;
                                        }
                                    }


                                    $variation = Variation::create([
                                        'product_id'        => $product->id,
                                        'name'              => $variantName[$x],
                                        'sku'               => !empty($variantSku[$x]) ? $variantSku[$x] : generate_sku($product->id),
                                        'purchase_price'    => !empty($modalPrice[$x]) ? (int)$modalPrice[$x] : 0,
                                        'barcode'           => !empty($barcode[$x]) ? strtolower(preg_replace("/[^0-9a-zA-Z]/", "", (int)$barcode[$x]))   : generate_ean(),
                                        'price_inc_tax'     => (int)$priceIncludeTax,
                                        'selling_price'     => !empty($sellPrice[$x]) ? (int)$sellPrice[$x] : 0,
                                        'tax_type'          => $taxType,
                                        'taxrate'           => !empty($taxrate[$x]) ?  $taxrate[$x] : 0,
                                        'unit_id'           => $unit != null ? $unit->id : null,
                                        'unit_sale'         => $unitS != null ? $unitS->id : null,
                                        'unit_purchase'     => $unitP != null ? $unitP->id : null,
                                        'tax_sell'          => $taxSell == 'yes' ? 'yes' : 'no',
                                        'tax_purchase'      => $taxPurchase == 'yes' ? 'yes' : 'no'
                                    ]);

                                    if (isset($stock[$x])) {
                                        if ((int)$stock[$x] > 0 && $product->is_stock == 'yes') {
                                            $stock_store = $this->stockObserver->createData($variation);
                                            $this->purchaseObserver->handlingFirstStock($variation, $stock_store, (int)$stock[$x]);
                                        }
                                    }
                                }
                            } else {

                                $unit = null;
                                if (isset($d['unit_dasar'])) {
                                    $unitName = strtolower(preg_replace("/[^0-9a-zA-Z]/", "", $d['unit_dasar']));
                                    $unit = Unit::where("name", $unitName)->where("is_root_parent", 0)->first();

                                    if (!$unit) {
                                        $unit = Unit::create([
                                            'name'      => $unitName,
                                            'code'      => $unitName
                                        ]);
                                    }
                                }

                                $unitS = null;
                                if (isset($d['unit_penjualan'])) {
                                    $unitName = strtolower(preg_replace("/[^0-9a-zA-Z]/", "", $d['unit_penjualan']));
                                    $unitS = Unit::where("name",  $unitName)->first();
                                }

                                $unitP = null;
                                if (isset($d['unit_pembelian'])) {
                                    $unitName = strtolower(preg_replace("/[^0-9a-zA-Z]/", "", $d['unit_pembelian']));
                                    $unitP = Unit::where("name",  $unitName)->first();
                                }



                                $priceIncludeTax    =  !empty($d['harga_jual']) ? (int)$d['harga_jual'] : 0;
                                $taxType            =  'inclusive';

                                if (isset($d['pajak'])) {

                                    if ((int)$d['pajak'] > 0 && $priceIncludeTax > 0) {
                                        $taxType            = 'exclusive';
                                        $priceTax           = (int)$d['pajak'] / 100 * (int)$priceIncludeTax;
                                        $priceIncludeTax    = (int)$priceIncludeTax + (int)$priceTax;
                                    }
                                }

                                $variation = Variation::create([
                                    'product_id'        => $product->id,
                                    'sku'               => !empty($d['sku']) ? $d['sku'] : generate_sku($product->id),
                                    'purchase_price'    => !empty($d['harga_modal']) ? (int)$d['harga_modal'] : 0,
                                    'barcode'           => !empty($d['barcode']) ? strtolower(preg_replace("/[^0-9a-zA-Z]/", "", $d['barcode'])) : generate_ean(),
                                    'price_inc_tax'     => (int)$priceIncludeTax,
                                    'selling_price'     => !empty($d['harga_jual']) ? (int)$d['harga_jual'] : 0,
                                    'tax_type'          => $taxType,
                                    'taxrate'           => !empty($d['pajak']) ?  $d['pajak'] : 0,
                                    'unit_id'           => $unit != null ? $unit->id : null,
                                    'unit_sale'         => $unitS != null ? $unitS->id : null,
                                    'unit_purchase'     => $unitP != null ? $unitP->id : null,
                                    'tax_sell'          => !empty($d['pajak_penjualan']) ? $d['pajak_penjualan'] == 'yes' : 'no',
                                    'tax_purchase'      => !empty($d['pajak_pembelian']) ? $d['pajak_pembelian'] == 'yes' : 'no',
                                ]);


                                // End Temporary Code

                                // Stock
                                if (!empty($d['stock'])) {
                                    if ((int)$d['stock'] > 0 && $product->is_stock == 'yes') {
                                        $stock_store = $this->stockObserver->createData($variation);
                                        $this->purchaseObserver->handlingFirstStock($variation, $stock_store, (int)$d['stock']);
                                    }
                                }
                                // End Stock
                            }
                        }
                    }

                    DB::commit();
                    return response()->json([
                        'message' => "Import Data berhasil di lakukan",
                        'status' => true
                    ], 200);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json([
                        'status'    => false,
                        'message'   => $e->getMessage(),
                        'file'      => $e->getFile(),
                        'line'      => $e->getLine()
                    ], 409);
                }
            } else {
                return response()->json([
                    'message' => "Terjadi kesalahan, silahkan coba kembali",
                    'status' => false
                ], 409);
            }
        }
        return response()->json([
            'message' => "File Tidak Terbaca",
            'status' => false
        ], 409);
    }

    /*
    |--------------------------------------------------------------------------
    | 15. Import Product
    |--------------------------------------------------------------------------
    */

    public function downloadSample()
    {
        $file = public_path('berkas/sample_import_products.xlsx');
        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        return Response::download($file, 'sample_import_products.xlsx', $headers);
    }

    /*
    |--------------------------------------------------------------------------
    | 16. Export Product
    |--------------------------------------------------------------------------
    */

    public function download(Request $request)
    {

        $data       = $this->productObserver->getData($request);
        $products   = $data->get();

        return Excel::download(new ProductsExport($products), 'master_data_products.xlsx');
    }

    public function downloadSpt(Request $request)
    {

        return (new ProductTaxFormatExport($request, $this->productObserver))->download('product_spt_format.xlsx');
    }

    /*
    |--------------------------------------------------------------------------
    | 17. Variations List
    |--------------------------------------------------------------------------
    */

    public function variations(Request $request)
    {

        $limit  = $request->input('limit', 10);
        $data   = $this->variationObserver->getData($request);

        $totalRows  = $data->count();
        $variations = $data->paginate($limit);

        return response()->json([
            'totalRows'     => $totalRows,
            'variations'    => VariatioDetailResource::collection($variations),
        ]);
    }


     /*
    |--------------------------------------------------------------------------
    | 18. Update Pricing
    |--------------------------------------------------------------------------
    */

    public function changePrice(Variation $variation, Request $request)
    {
 
        $variation->update([
            'selling_price'     => $request->type == 'sell_price' ? $request->price : $variation->selling_price,
            'grocery'           => $request->type == 'grocery_price' ? $request->price : $variation->grocery
        ]);

        return response()->json([
            'message'       => 'Berhasil memperbaharui harga  ',
            'status'        => true,
        ]);
    }
}
