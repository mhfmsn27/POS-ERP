<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class EnterpriseMasterSeeder extends Seeder
{
    /**
     * Run the enterprise database seeds safely.
     * Menggunakan Schema::hasColumn untuk menjamin kompatibilitas 100%
     * dengan skema database tanpa error 1054 Unknown column.
     *
     * @return void
     */
    public function run()
    {
        // 1. Inisialisasi Master Merchant Enterprise
        if (Schema::hasTable('merchants')) {
            $merchantData = [
                'name'       => 'POSHUB ENTERPRISE',
                'owner_id'   => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (Schema::hasColumn('merchants', 'email')) {
                $merchantData['email'] = 'admin@poshub.id';
            }
            if (Schema::hasColumn('merchants', 'phone')) {
                $merchantData['phone'] = '081234567890';
            }
            if (Schema::hasColumn('merchants', 'address')) {
                $merchantData['address'] = 'Kantor Pusat POSHUB';
            }
            if (Schema::hasColumn('merchants', 'status')) {
                $merchantData['status'] = 'active';
            }

            DB::table('merchants')->updateOrInsert(
                ['id' => 1],
                $merchantData
            );
        }

        // 2. Inisialisasi Master Paket Enterprise Lifetime
        if (Schema::hasTable('packages')) {
            $pkgData = [
                'name'        => 'POSHUB Enterprise Lifetime',
                'description' => 'Paket Akses Penuh Enterprise Tanpa Batas (Unlimited)',
                'price'       => 0,
                'created_at'  => now(),
                'updated_at'  => now(),
            ];

            if (Schema::hasColumn('packages', 'limit_day')) {
                $pkgData['limit_day'] = 99999;
            }
            if (Schema::hasColumn('packages', 'interval')) {
                $pkgData['interval'] = 'lifetime';
            }
            if (Schema::hasColumn('packages', 'status')) {
                $pkgData['status'] = 'active';
            }

            DB::table('packages')->updateOrInsert(
                ['id' => 1],
                $pkgData
            );
        }

        // 3. Inisialisasi Toko / Cabang Utama Default
        if (Schema::hasTable('stores')) {
            $storeData = [
                'name'        => 'Toko Utama POSHUB',
                'email'       => 'store@poshub.id',
                'phone'       => '081234567890',
                'address'     => 'Jl. Jenderal Sudirman No. 1, Jakarta',
                'country_id'  => 1,
                'currency_id' => 54,
                'created_at'  => now(),
                'updated_at'  => now(),
            ];

            if (Schema::hasColumn('stores', 'merchant_id')) {
                $storeData['merchant_id'] = 1;
            }
            if (Schema::hasColumn('stores', 'accountant_use')) {
                $storeData['accountant_use'] = 'yes';
            }
            if (Schema::hasColumn('stores', 'shift_register')) {
                $storeData['shift_register'] = 'active';
            }
            if (Schema::hasColumn('stores', 'tax_option')) {
                $storeData['tax_option'] = 'active';
            }

            DB::table('stores')->updateOrInsert(
                ['id' => 1],
                $storeData
            );
        }

        // 4. Inisialisasi Transaction Package Lifetime Aktif
        if (Schema::hasTable('transaction_packages')) {
            $trxPkgData = [
                'merchant_id'    => 1,
                'ref_no'         => 'PKG-LIFETIME-ENTERPRISE',
                'status'         => 'success',
                'payment_status' => 'paid',
                'end_date'       => '2099-12-31 23:59:59',
                'subtotal'       => 0,
                'tax'            => 0,
                'grand_total'    => 0,
                'created_at'     => now(),
                'updated_at'     => now(),
            ];

            if (Schema::hasColumn('transaction_packages', 'amount')) {
                $trxPkgData['amount'] = 0;
            }
            if (Schema::hasColumn('transaction_packages', 'start_date')) {
                $trxPkgData['start_date'] = now()->format('Y-m-d');
            }

            DB::table('transaction_packages')->updateOrInsert(
                ['store_id' => 1, 'package_id' => 1],
                $trxPkgData
            );
        }

        // 5. Inisialisasi Pengaturan Gateway CRMHUB OMNICHANNEL
        if (Schema::hasTable('omnichannel_gateway_settings')) {
            DB::table('omnichannel_gateway_settings')->updateOrInsert(
                ['provider' => 'crmhub_omnichannel'],
                [
                    'store_id'                  => 1,
                    'gateway_url'               => 'http://127.0.0.1:8000/api/whatsapp/send',
                    'enable_digital_receipt'    => true,
                    'enable_shift_z_report_wa'  => true,
                    'enable_low_stock_wa'       => true,
                    'manager_phone'             => '081234567890',
                    'updated_at'                => now(),
                ]
            );
        }

        // 6. Inisialisasi Denah Meja Standar Resto
        if (Schema::hasTable('restaurant_tables')) {
            $defaultTables = [
                ['table_number' => 'T-01', 'capacity' => 2, 'zone' => 'Main Hall'],
                ['table_number' => 'T-02', 'capacity' => 4, 'zone' => 'Main Hall'],
                ['table_number' => 'T-03', 'capacity' => 4, 'zone' => 'Main Hall'],
                ['table_number' => 'VIP-01', 'capacity' => 8, 'zone' => 'VIP Room'],
                ['table_number' => 'OUT-01', 'capacity' => 4, 'zone' => 'Outdoor Garden'],
            ];

            foreach ($defaultTables as $tbl) {
                DB::table('restaurant_tables')->updateOrInsert(
                    ['store_id' => 1, 'table_number' => $tbl['table_number']],
                    [
                        'capacity'   => $tbl['capacity'],
                        'zone'       => $tbl['zone'],
                        'status'     => 'available',
                        'updated_at' => now(),
                    ]
                );
            }
        }

        // 7. Inisialisasi Promosi Cerdas Pembuka
        if (Schema::hasTable('smart_promotions')) {
            DB::table('smart_promotions')->updateOrInsert(
                ['store_id' => 1, 'name' => 'Diskon Pembukaan Belanja Min 200rb'],
                [
                    'promo_type'      => 'threshold_discount',
                    'conditions_json' => json_encode(['min_spend' => 200000]),
                    'rewards_json'    => json_encode(['discount_amount' => 20000]),
                    'start_date'      => now()->format('Y-m-d'),
                    'end_date'        => now()->addMonths(3)->format('Y-m-d'),
                    'is_active'       => true,
                    'updated_at'      => now(),
                ]
            );
        }
    }
}
