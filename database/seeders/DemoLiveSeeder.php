<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DemoLiveSeeder extends Seeder
{
    /**
     * Run safe, rock-solid foundational master data & storefront demo products seeds.
     * Mengisi data master, akun demo, kategori, merek, serta katalog produk demo
     * yang 100% terhubung sempurna ke Marketplace Storefront & Kasir POS.
     *
     * @return void
     */
    public function run()
    {
        // ====================================================================
        // 1. SEED MASTER USERS (AKUN LOGIN RESMI SIAP DEMO)
        // ====================================================================
        if (Schema::hasTable('users')) {
            $hasMerchantId = Schema::hasColumn('users', 'merchant_id');
            $hasStoreId    = Schema::hasColumn('users', 'store_id');
            $hasRoleType   = Schema::hasColumn('users', 'role_type');

            $demoUsers = [
                [
                    'name'              => 'Super Administrator',
                    'email'             => 'admin@poshub.id',
                    'password'          => Hash::make('password123'),
                    'photo'             => 'uploads/image.jpg',
                    'email_verified_at' => now(),
                ],
                [
                    'name'              => 'Store Manager (Budi Pratama)',
                    'email'             => 'manager@poshub.id',
                    'password'          => Hash::make('password123'),
                    'photo'             => 'uploads/image.jpg',
                    'email_verified_at' => now(),
                ],
                [
                    'name'              => 'Kasir Toko (Siti Nurhaliza)',
                    'email'             => 'kasir@poshub.id',
                    'password'          => Hash::make('password123'),
                    'photo'             => 'uploads/image.jpg',
                    'email_verified_at' => now(),
                ],
            ];

            foreach ($demoUsers as $u) {
                $userData = $u;
                if ($hasMerchantId) {
                    $userData['merchant_id'] = 1;
                }
                if ($hasStoreId) {
                    $userData['store_id'] = 1;
                }
                if ($hasRoleType) {
                    $userData['role_type'] = 'administrator';
                }

                DB::table('users')->updateOrInsert(
                    ['email' => $u['email']],
                    array_merge($userData, [
                        'created_at' => now(),
                        'updated_at' => now()
                    ])
                );
            }
        }

        // ====================================================================
        // 2. SEED MASTER GUDANG (WAREHOUSES)
        // ====================================================================
        if (Schema::hasTable('warehouses')) {
            $hasStoreId = Schema::hasColumn('warehouses', 'store_id');
            $warehouses = [
                [
                    'id'          => 1,
                    'name'        => 'Gudang Pusat Logistik (Main DC)',
                    'code'        => 'WH-MAIN-01',
                    'address'     => 'Kawasan Industri Pulogadung, Jakarta Timur',
                    'description' => 'Pusat distribusi logistik utama seluruh cabang',
                    'is_active'   => 1,
                ],
                [
                    'id'          => 2,
                    'name'        => 'Gudang Display Toko Utama',
                    'code'        => 'WH-STORE-01',
                    'address'     => 'Jl. Jenderal Sudirman No. 1, Jakarta',
                    'description' => 'Gudang stok etalase siap jual di area kasir toko',
                    'is_active'   => 1,
                ],
            ];

            foreach ($warehouses as $wh) {
                $whData = $wh;
                if ($hasStoreId) {
                    $whData['store_id'] = 1;
                }
                DB::table('warehouses')->updateOrInsert(
                    ['id' => $wh['id']],
                    array_merge($whData, ['created_at' => now(), 'updated_at' => now()])
                );
            }
        }

        // ====================================================================
        // 3. SEED MASTER SATUAN (UNITS OF MEASURE)
        // ====================================================================
        if (Schema::hasTable('units')) {
            $units = [
                ['id' => 1, 'name' => 'Pieces', 'code' => 'Pcs', 'detail' => 'Satuan dasar per buah / eceran'],
                ['id' => 2, 'name' => 'Dus / Karton', 'code' => 'Karton', 'detail' => 'Satuan grosir besar kemasan pabrik'],
                ['id' => 3, 'name' => 'Lusin', 'code' => 'Lusin', 'detail' => '1 Lusin = 12 Pieces'],
                ['id' => 4, 'name' => 'Pack / Bungkus', 'code' => 'Pack', 'detail' => 'Satuan kemasan ritel'],
                ['id' => 5, 'name' => 'Kilogram', 'code' => 'Kg', 'detail' => 'Satuan berat kilogram'],
                ['id' => 6, 'name' => 'Box', 'code' => 'Box', 'detail' => 'Satuan kotak kemasan'],
            ];

            foreach ($units as $unit) {
                DB::table('units')->updateOrInsert(
                    ['id' => $unit['id']],
                    array_merge($unit, ['created_at' => now(), 'updated_at' => now()])
                );
            }
        }

        // ====================================================================
        // 4. SEED MASTER KATEGORI (CATEGORIES - STOREFRONT READY)
        // ====================================================================
        if (Schema::hasTable('categories')) {
            $hasShowEcom     = Schema::hasColumn('categories', 'show_in_ecommerce');
            $hasFeaturedEcom = Schema::hasColumn('categories', 'featured_category');
            $hasStoreId      = Schema::hasColumn('categories', 'store_id');
            $hasMerchantId   = Schema::hasColumn('categories', 'merchant_id');

            $categories = [
                ['id' => 1, 'name' => 'Makanan & Minuman (F&B)', 'detail' => 'Kopi, Bakery, Minuman Dingin, dan Hidangan Resto', 'is_root_parent' => 1, 'image' => 'uploads/image.jpg'],
                ['id' => 2, 'name' => 'Elektronik & Gadget', 'detail' => 'Smartphone, Tablet, Audio, dan Perangkat Pintar', 'is_root_parent' => 1, 'image' => 'uploads/image.jpg'],
                ['id' => 3, 'name' => 'Aksesoris & Komputer', 'detail' => 'Keyboard, Mouse, Kabel, Charger, dan Headset', 'is_root_parent' => 1, 'image' => 'uploads/image.jpg'],
                ['id' => 4, 'name' => 'Bahan Pokok & Sembako', 'detail' => 'Minyak Goreng, Beras, Gula, dan Kebutuhan Pokok', 'is_root_parent' => 1, 'image' => 'uploads/image.jpg'],
                ['id' => 5, 'name' => 'Fashion & Apparel', 'detail' => 'Kaos Polo, Jaket Hoodie, dan Pakaian Kasual', 'is_root_parent' => 1, 'image' => 'uploads/image.jpg'],
            ];

            foreach ($categories as $cat) {
                $catData = $cat;
                if ($hasShowEcom) {
                    $catData['show_in_ecommerce'] = 'yes';
                }
                if ($hasFeaturedEcom) {
                    $catData['featured_category'] = 'yes';
                }
                if ($hasStoreId) {
                    $catData['store_id'] = 1;
                }
                if ($hasMerchantId) {
                    $catData['merchant_id'] = 1;
                }

                DB::table('categories')->updateOrInsert(
                    ['id' => $cat['id']],
                    array_merge($catData, ['created_at' => now(), 'updated_at' => now()])
                );
            }
        }

        // ====================================================================
        // 5. SEED MASTER MEREK (BRANDS)
        // ====================================================================
        if (Schema::hasTable('brands')) {
            $hasStoreId    = Schema::hasColumn('brands', 'store_id');
            $hasMerchantId = Schema::hasColumn('brands', 'merchant_id');

            $brands = [
                ['id' => 1, 'name' => 'Apple', 'code' => 'BR-AAPL', 'detail' => 'Brand Elektronik Premium'],
                ['id' => 2, 'name' => 'Samsung', 'code' => 'BR-SMSNG', 'detail' => 'Brand Elektronik & Smart Device'],
                ['id' => 3, 'name' => 'Xiaomi', 'code' => 'BR-MI', 'detail' => 'Brand Smartphone & Gadget'],
                ['id' => 4, 'name' => 'Logitech', 'code' => 'BR-LOGI', 'detail' => 'Brand Peripheral & Gaming Gear'],
                ['id' => 5, 'name' => 'Sony', 'code' => 'BR-SNY', 'detail' => 'Brand Audio & Entertainment'],
                ['id' => 6, 'name' => 'Indofood', 'code' => 'BR-INDF', 'detail' => 'Brand Bahan Makanan & Sembako'],
                ['id' => 7, 'name' => 'Asus', 'code' => 'BR-ASUS', 'detail' => 'Brand Komputer & Laptop'],
            ];

            foreach ($brands as $b) {
                $brandData = $b;
                if ($hasStoreId) {
                    $brandData['store_id'] = 1;
                }
                if ($hasMerchantId) {
                    $brandData['merchant_id'] = 1;
                }

                DB::table('brands')->updateOrInsert(
                    ['id' => $b['id']],
                    array_merge($brandData, [
                        'image'      => 'uploads/brand/image.jpg',
                        'created_at' => now(),
                        'updated_at' => now()
                    ])
                );
            }
        }

        // ====================================================================
        // 6. SEED MASTER PEMASOK (SUPPLIERS)
        // ====================================================================
        if (Schema::hasTable('suppliers')) {
            $suppliers = [
                [
                    'id'         => 1,
                    'name'       => 'PT Global Distribusi Nusantara',
                    'code'       => 'SUP-GDN-01',
                    'email'      => 'order@gdn-distributor.co.id',
                    'phone'      => '081122334455',
                    'address'    => 'Jl. Industri Raya Blok C No. 8, Jakarta',
                    'city'       => 'Jakarta',
                    'state'      => 'DKI Jakarta',
                    'country_id' => 1,
                    'detail'     => 'Supplier Resmi Smartphone & Gadget Resmi',
                ],
                [
                    'id'         => 2,
                    'name'       => 'CV Pangan Makmur Utama',
                    'code'       => 'SUP-PMU-02',
                    'email'      => 'sales@panganmakmur.com',
                    'phone'      => '081199887766',
                    'address'    => 'Jl. Rungkut Industri No. 45, Surabaya',
                    'city'       => 'Surabaya',
                    'state'      => 'Jawa Timur',
                    'country_id' => 1,
                    'detail'     => 'Supplier Biji Kopi, Susu, dan Bahan Baku F&B',
                ],
                [
                    'id'         => 3,
                    'name'       => 'PT Cipta Sarana Komputer',
                    'code'       => 'SUP-CSK-03',
                    'email'      => 'b2b@ciptakomputer.id',
                    'phone'      => '081144556677',
                    'address'    => 'Kawasan Niaga Dago No. 12, Bandung',
                    'city'       => 'Bandung',
                    'state'      => 'Jawa Barat',
                    'country_id' => 1,
                    'detail'     => 'Distributor Aksesoris & Hardware POS',
                ],
            ];

            foreach ($suppliers as $sup) {
                DB::table('suppliers')->updateOrInsert(
                    ['id' => $sup['id']],
                    array_merge($sup, ['created_at' => now(), 'updated_at' => now()])
                );
            }
        }

        // ====================================================================
        // 7. SEED MASTER PELANGGAN (CUSTOMERS)
        // ====================================================================
        if (Schema::hasTable('customers')) {
            $customers = [
                [
                    'id'      => 1,
                    'name'    => 'Pelanggan Umum (Walk-in Customer)',
                    'code'    => 'CUST-0001',
                    'email'   => 'customer@poshub.id',
                    'phone'   => '080000000000',
                    'address' => 'Transaksi Langsung di Toko',
                    'city'    => 'Jakarta',
                    'state'   => 'DKI Jakarta',
                    'detail'  => 'Default Walk-in Customer Kasir',
                ],
                [
                    'id'      => 2,
                    'name'    => 'Budi Santoso (Member Platinum VIP)',
                    'code'    => 'CUST-0002',
                    'email'   => 'budi.santoso@gmail.com',
                    'phone'   => '081234567801',
                    'address' => 'Jl. Kebon Jeruk No. 15, Jakarta Barat',
                    'city'    => 'Jakarta',
                    'state'   => 'DKI Jakarta',
                    'detail'  => 'Pelanggan Loyal Tier Platinum (Diskon Khusus 5%)',
                ],
                [
                    'id'      => 3,
                    'name'    => 'Siti Rahmawati (Member Gold)',
                    'code'    => 'CUST-0003',
                    'email'   => 'siti.rahmawati@yahoo.com',
                    'phone'   => '081234567802',
                    'address' => 'Jl. Tebet Raya No. 88, Jakarta Selatan',
                    'city'    => 'Jakarta',
                    'state'   => 'DKI Jakarta',
                    'detail'  => 'Member Gold Aktif',
                ],
                [
                    'id'      => 4,
                    'name'    => 'PT Mitra Niaga Grosir (Mitra B2B)',
                    'code'    => 'CUST-B2B-01',
                    'email'   => 'purchasing@mitraniaga.co.id',
                    'phone'   => '081234567803',
                    'address' => 'Komplek Pergudangan Kamal Muara Blok A-12',
                    'city'    => 'Jakarta',
                    'state'   => 'DKI Jakarta',
                    'detail'  => 'Mitra Korporat B2B - Plafon Kredit Rp 50.000.000 TOP 30 Hari',
                ],
            ];

            foreach ($customers as $cust) {
                DB::table('customers')->updateOrInsert(
                    ['id' => $cust['id']],
                    array_merge($cust, ['created_at' => now(), 'updated_at' => now()])
                );
            }
        }

        // ====================================================================
        // 8. SEED METODE PEMBAYARAN (PAYMENT METHODS)
        // ====================================================================
        if (Schema::hasTable('payment_methods')) {
            $paymentMethods = [
                ['id' => 1, 'name' => 'Tunai (Cash)'],
                ['id' => 2, 'name' => 'QRIS Dinamis (GoPay / OVO / ShopeePay / DANA)'],
                ['id' => 3, 'name' => 'Transfer Bank BCA (Virtual Account)'],
                ['id' => 4, 'name' => 'Transfer Bank Mandiri'],
                ['id' => 5, 'name' => 'EDC Debit / Kartu Kredit BCA'],
                ['id' => 6, 'name' => 'Piutang Dagang / Tempo B2B (TOP)'],
            ];

            foreach ($paymentMethods as $pm) {
                DB::table('payment_methods')->updateOrInsert(
                    ['id' => $pm['id']],
                    array_merge($pm, ['created_at' => now(), 'updated_at' => now()])
                );
            }
        }

        // ====================================================================
        // 9. SEED KATALOG PRODUK DEMO (STOREFRONT & POS READY)
        // ====================================================================
        if (Schema::hasTable('products') && Schema::hasTable('variations')) {
            $hasProductStoreId    = Schema::hasColumn('products', 'store_id');
            $hasProductMerchantId = Schema::hasColumn('products', 'merchant_id');
            $hasIsStock           = Schema::hasColumn('products', 'is_stock');
            $hasIsActive          = Schema::hasColumn('products', 'is_active');
            $hasPriceType         = Schema::hasColumn('products', 'price_type');
            $hasBarcodeCol        = Schema::hasColumn('variations', 'barcode');

            $demoProducts = [
                [
                    'id'             => 1,
                    'name'           => 'Smartphone Flagship X1 Pro 256GB',
                    'sku'            => 'PH-GAD-001',
                    'barcode'        => '899100100101',
                    'type'           => 'single',
                    'category_id'    => 2,
                    'brand_id'       => 3,
                    'unit_id'        => 1,
                    'alert_quantity' => '5',
                    'barcode_type'   => 'C128',
                    'weight'         => '200',
                    'description'    => 'Smartphone Flagship Layar AMOLED 120Hz, Chipset Flagship, RAM 12GB ROM 256GB',
                    'purchase_price' => 4500000,
                    'selling_price'  => 5999000,
                    'stock_qty'      => 35,
                ],
                [
                    'id'             => 2,
                    'name'           => 'Wireless Mechanical Keyboard RGB',
                    'sku'            => 'PH-ACC-001',
                    'barcode'        => '899100100102',
                    'type'           => 'single',
                    'category_id'    => 3,
                    'brand_id'       => 4,
                    'unit_id'        => 1,
                    'alert_quantity' => '10',
                    'barcode_type'   => 'C128',
                    'weight'         => '850',
                    'description'    => 'Keyboard Mechanical Triple Connection (Bluetooth/2.4G/Type-C) dengan Switch Hotswap',
                    'purchase_price' => 450000,
                    'selling_price'  => 750000,
                    'stock_qty'      => 50,
                ],
                [
                    'id'             => 3,
                    'name'           => 'Smart Bluetooth Headset ANC',
                    'sku'            => 'PH-ACC-002',
                    'barcode'        => '899100100103',
                    'type'           => 'single',
                    'category_id'    => 3,
                    'brand_id'       => 5,
                    'unit_id'        => 1,
                    'alert_quantity' => '8',
                    'barcode_type'   => 'C128',
                    'weight'         => '250',
                    'description'    => 'Headset Bluetooth Active Noise Cancelling (ANC) dengan baterai 40 jam',
                    'purchase_price' => 600000,
                    'selling_price'  => 999000,
                    'stock_qty'      => 40,
                ],
                [
                    'id'             => 4,
                    'name'           => 'USB-C Fast Charging Hub 65W GaN',
                    'sku'            => 'PH-ACC-003',
                    'barcode'        => '899100100104',
                    'type'           => 'single',
                    'category_id'    => 3,
                    'brand_id'       => 7,
                    'unit_id'        => 1,
                    'alert_quantity' => '15',
                    'barcode_type'   => 'C128',
                    'weight'         => '120',
                    'description'    => 'Charger GaN 65W 3 Port Type-C + USB Fast Charging Universal',
                    'purchase_price' => 120000,
                    'selling_price'  => 249000,
                    'stock_qty'      => 80,
                ],
                [
                    'id'             => 5,
                    'name'           => 'Kopi Susu Gula Aren Espresso',
                    'sku'            => 'PH-FNB-001',
                    'barcode'        => '899100200201',
                    'type'           => 'single',
                    'category_id'    => 1,
                    'brand_id'       => 6,
                    'unit_id'        => 1,
                    'alert_quantity' => '20',
                    'barcode_type'   => 'C128',
                    'weight'         => '300',
                    'description'    => 'Minuman Kopi Susu Fresh Milk dengan Gula Aren Murni dan Double Shot Espresso',
                    'purchase_price' => 8000,
                    'selling_price'  => 22000,
                    'stock_qty'      => 150,
                ],
                [
                    'id'             => 6,
                    'name'           => 'Croissant Almond Butter Freshly Baked',
                    'sku'            => 'PH-FNB-002',
                    'barcode'        => '899100200202',
                    'type'           => 'single',
                    'category_id'    => 1,
                    'brand_id'       => 6,
                    'unit_id'        => 1,
                    'alert_quantity' => '10',
                    'barcode_type'   => 'C128',
                    'weight'         => '150',
                    'description'    => 'Roti Pastry Croissant Renyah dengan Isian Cream Almond dan Taburan Almond Panggang',
                    'purchase_price' => 12000,
                    'selling_price'  => 28000,
                    'stock_qty'      => 60,
                ],
                [
                    'id'             => 7,
                    'name'           => 'Matcha Green Tea Latte Uji Kyoto',
                    'sku'            => 'PH-FNB-003',
                    'barcode'        => '899100200203',
                    'type'           => 'single',
                    'category_id'    => 1,
                    'brand_id'       => 6,
                    'unit_id'        => 1,
                    'alert_quantity' => '15',
                    'barcode_type'   => 'C128',
                    'weight'         => '300',
                    'description'    => 'Matcha Asli Jepang dengan Fresh Milk Creamy Dingin',
                    'purchase_price' => 9000,
                    'selling_price'  => 25000,
                    'stock_qty'      => 100,
                ],
                [
                    'id'             => 8,
                    'name'           => 'Signature Wagyu Beef Burger',
                    'sku'            => 'PH-FNB-004',
                    'barcode'        => '899100200204',
                    'type'           => 'single',
                    'category_id'    => 1,
                    'brand_id'       => 6,
                    'unit_id'        => 1,
                    'alert_quantity' => '10',
                    'barcode_type'   => 'C128',
                    'weight'         => '350',
                    'description'    => 'Burger Daging Sapi Wagyu Juicy dengan Keju Melted Cheddar dan Kentang Goreng',
                    'purchase_price' => 28000,
                    'selling_price'  => 55000,
                    'stock_qty'      => 45,
                ],
                [
                    'id'             => 9,
                    'name'           => 'Minyak Goreng Pouch 2 Liter',
                    'sku'            => 'PH-GRO-001',
                    'barcode'        => '899100300301',
                    'type'           => 'single',
                    'category_id'    => 4,
                    'brand_id'       => 6,
                    'unit_id'        => 1,
                    'alert_quantity' => '20',
                    'barcode_type'   => 'C128',
                    'weight'         => '2000',
                    'description'    => 'Minyak Goreng Sawit Murni Kualitas Pilihan Kemasan Pouch 2 Liter',
                    'purchase_price' => 28000,
                    'selling_price'  => 34000,
                    'stock_qty'      => 120,
                ],
                [
                    'id'             => 10,
                    'name'           => 'Beras Premium Pandan Wangi 5kg',
                    'sku'            => 'PH-GRO-002',
                    'barcode'        => '899100300302',
                    'type'           => 'single',
                    'category_id'    => 4,
                    'brand_id'       => 6,
                    'unit_id'        => 1,
                    'alert_quantity' => '15',
                    'barcode_type'   => 'C128',
                    'weight'         => '5000',
                    'description'    => 'Beras Pulen Alami Harum Pandan Wangi Kemasan Karung 5kg',
                    'purchase_price' => 65000,
                    'selling_price'  => 78000,
                    'stock_qty'      => 80,
                ],
            ];

            foreach ($demoProducts as $p) {
                $pData = [
                    'name'           => $p['name'],
                    'sku'            => $p['sku'],
                    'type'           => $p['type'],
                    'category_id'    => $p['category_id'],
                    'brand_id'       => $p['brand_id'],
                    'unit_id'        => $p['unit_id'],
                    'alert_quantity' => $p['alert_quantity'],
                    'barcode_type'   => $p['barcode_type'],
                    'weight'         => $p['weight'],
                    'description'    => $p['description'],
                    'image'          => 'uploads/image.jpg',
                ];

                if ($hasProductStoreId) {
                    $pData['store_id'] = 1;
                }
                if ($hasProductMerchantId) {
                    $pData['merchant_id'] = 1;
                }
                if ($hasIsStock) {
                    $pData['is_stock'] = 'yes';
                }
                if ($hasIsActive) {
                    $pData['is_active'] = 'yes';
                }
                if ($hasPriceType) {
                    $pData['price_type'] = 'single';
                }

                // 1. Insert/Update Product
                DB::table('products')->updateOrInsert(
                    ['id' => $p['id']],
                    array_merge($pData, ['created_at' => now(), 'updated_at' => now()])
                );

                // 2. Insert/Update Variation
                $margin = ((float)$p['selling_price'] - (float)$p['purchase_price']) / (float)$p['purchase_price'] * 100;
                $varData = [
                    'product_id'     => $p['id'],
                    'sku'            => $p['sku'],
                    'name'           => 'Default',
                    'purchase_price' => (string)$p['purchase_price'],
                    'selling_price'  => (string)$p['selling_price'],
                    'price_inc_tax'  => (string)$p['selling_price'],
                    'margin'         => (string)round($margin, 2),
                    'unit_id'        => $p['unit_id'],
                ];
                if ($hasBarcodeCol) {
                    $varData['barcode'] = $p['barcode'];
                }

                DB::table('variations')->updateOrInsert(
                    ['id' => $p['id']],
                    array_merge($varData, ['created_at' => now(), 'updated_at' => now()])
                );

                // 3. Insert/Update Price Variation Stores
                if (Schema::hasTable('price_variation_stores')) {
                    DB::table('price_variation_stores')->updateOrInsert(
                        [
                            'variation_id' => $p['id'],
                            'store_id'     => 1,
                        ],
                        [
                            'price'      => $p['selling_price'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }

                // 4. Insert/Update Stock in Warehouse 1 & Store 1
                if (Schema::hasTable('stocks')) {
                    $stockData = [
                        'product_id'    => $p['id'],
                        'variation_id'  => $p['id'],
                        'store_id'      => 1,
                        'qty_available' => $p['stock_qty'],
                    ];
                    if (Schema::hasColumn('stocks', 'warehouse_id')) {
                        $stockData['warehouse_id'] = 1;
                    }

                    DB::table('stocks')->updateOrInsert(
                        [
                            'product_id'   => $p['id'],
                            'variation_id' => $p['id'],
                            'store_id'     => 1,
                        ],
                        array_merge($stockData, ['created_at' => now(), 'updated_at' => now()])
                    );
                }
            }
        }

        // ====================================================================
        // 10. SEED STOREFRONT SLIDERS & BANNERS (MARKETPLACE SHOWCASE)
        // ====================================================================
        if (Schema::hasTable('sliders')) {
            $hasStoreId = Schema::hasColumn('sliders', 'store_id');
            $sliderData = [
                'image'       => 'uploads/slider/image.jpg',
                'title'       => 'Promo Spesial POSHUB Enterprise',
                'subtitle'    => 'Dapatkan penawaran terbaik untuk seluruh produk pilihan berkualitas tinggi hari ini!',
                'button'      => 'yes',
                'button_name' => 'Belanja Sekarang',
                'button_url'  => '/shop',
            ];
            if ($hasStoreId) {
                $sliderData['store_id'] = 1;
            }

            DB::table('sliders')->updateOrInsert(
                ['id' => 1],
                array_merge($sliderData, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        if (Schema::hasTable('banners')) {
            $hasStoreId = Schema::hasColumn('banners', 'store_id');
            $bannerData = [
                'image'       => 'uploads/slider/image.jpg',
                'title'       => 'Koleksi Produk Terlaris',
                'subtitle'    => 'Temukan produk terfavorit dengan harga terbaik',
                'button'      => 'no',
                'button_name' => 'Lihat Katalog',
                'button_url'  => '/shop',
            ];
            if ($hasStoreId) {
                $bannerData['store_id'] = 1;
            }

            DB::table('banners')->updateOrInsert(
                ['id' => 1],
                array_merge($bannerData, ['created_at' => now(), 'updated_at' => now()])
            );
        }

        if (Schema::hasTable('small_features')) {
            $hasStoreId = Schema::hasColumn('small_features', 'store_id');
            $features = [
                ['id' => 1, 'title' => 'Pengiriman Cepat', 'subtitle' => 'Kurir Instan & Kargo Nasional', 'image' => 'uploads/image.jpg'],
                ['id' => 2, 'title' => 'Produk 100% Original', 'subtitle' => 'Jaminan Kualitas & Garansi Resmi', 'image' => 'uploads/image.jpg'],
                ['id' => 3, 'title' => 'Layanan Pelanggan 24/7', 'subtitle' => 'Dukungan WhatsApp Respon Cepat', 'image' => 'uploads/image.jpg'],
                ['id' => 4, 'title' => 'Pembayaran Aman', 'subtitle' => 'QRIS Dinamis & Virtual Account', 'image' => 'uploads/image.jpg'],
            ];

            foreach ($features as $f) {
                $featData = $f;
                if ($hasStoreId) {
                    $featData['store_id'] = 1;
                }
                DB::table('small_features')->updateOrInsert(
                    ['id' => $f['id']],
                    array_merge($featData, ['created_at' => now(), 'updated_at' => now()])
                );
            }
        }
    }
}
