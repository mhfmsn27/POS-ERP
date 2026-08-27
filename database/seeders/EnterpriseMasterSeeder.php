<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class EnterpriseMasterSeeder extends Seeder
{
    /**
     * Run the enterprise database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 1. Inisialisasi Master Merchant Enterprise
        if (Schema::hasTable('merchants')) {
            DB::table('merchants')->updateOrInsert(
                ['id' => 1],
                [
                    'name'       => 'POSHUB ENTERPRISE',
                    'email'      => 'admin@poshub.id',
                    'phone'      => '081234567890',
                    'address'    => 'Kantor Pusat POSHUB',
                    'status'     => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // 2. Inisialisasi Master Paket Enterprise Lifetime
        if (Schema::hasTable('packages')) {
            DB::table('packages')->updateOrInsert(
                ['id' => 1],
                [
                    'name'         => 'POSHUB Enterprise Lifetime',
                    'description'  => 'Paket Akses Penuh Enterprise Tanpa Batas (Unlimited)',
                    'price'        => 0,
                    'interval'     => 'lifetime',
                    'status'       => 'active',
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]
            );
        }

        // 3. Inisialisasi Toko / Cabang Utama Default
        if (Schema::hasTable('stores')) {
            DB::table('stores')->updateOrInsert(
                ['id' => 1],
                [
                    'name'           => 'Toko Utama POSHUB',
                    'email'          => 'store@poshub.id',
                    'phone'          => '081234567890',
                    'address'        => 'Jl. Jenderal Sudirman No. 1, Jakarta',
                    'merchant_id'    => 1,
                    'country_id'     => 1,
                    'currency_id'    => 54,
                    'accountant_use' => 'yes',
                    'shift_register' => 'active',
                    'tax_option'     => 'active',
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]
            );
        }

        // 4. Inisialisasi Transaction Package Lifetime Aktif
        if (Schema::hasTable('transaction_packages')) {
            DB::table('transaction_packages')->updateOrInsert(
                ['store_id' => 1, 'package_id' => 1],
                [
                    'merchant_id' => 1,
                    'ref_no'      => 'PKG-LIFETIME-ENTERPRISE',
                    'amount'      => 0,
                    'status'      => 'success',
                    'start_date'  => now()->format('Y-m-d'),
                    'end_date'    => '2099-12-31',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]
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
