<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class ReportsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'module_id'     => 42,
                'name'          => 'commission_reports',
                'desc'          => 'Laporan Komisi',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 42,
                'name'          => 'due_customer_reports',
                'desc'          => 'Laporan Piutang Pelanggan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 42,
                'name'          => 'saldo_customer_reports',
                'desc'          => 'Laporan Saldo Pelanggan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 42,
                'name'          => 'due_supplier_reports',
                'desc'          => 'Laporan Hutang Supplier',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 42,
                'name'          => 'saldo_supplier_reports',
                'desc'          => 'Laporan Saldo Supplier',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 42,
                'name'          => 'neraca_standart',
                'desc'          => 'Neraca Standart',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 42,
                'name'          => 'product_reports',
                'desc'          => 'Laporan Produk',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 42,
                'name'          => 'product_minus',
                'desc'          => 'Laporan Produk Minus',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 42,
                'name'          => 'profit_loss',
                'desc'          => 'Laporan Laba Rugi',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 42,
                'name'          => 'profit_loss_priode',
                'desc'          => 'Laporan Laba Rugi Priode',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 42,
                'name'          => 'tax_sales',
                'desc'          => 'Laporan Pajak Penjualan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 42,
                'name'          => 'tax_purchase',
                'desc'          => 'Laporan Pajak Pembelian',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 42,
                'name'          => 'tax_retur_sale',
                'desc'          => 'Laporan Pajak Retur Penjualan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 42,
                'name'          => 'tax_retur_purchase',
                'desc'          => 'Laporan Pajak Retur Pembelian',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 42,
                'name'          => 'spt',
                'desc'          => 'SPT Pajak',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 42,
                'name'          => 'log_activity',
                'desc'          => 'Log Aktivitas',
                'guard_name'    => 'web'
            ],

        ];

        Permission::insert($data);
    }
}
