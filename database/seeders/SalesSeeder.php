<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class SalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            // Shipping Seeder
            [
                'module_id'     => 31,
                'name'          => 'shipping_view',
                'desc'          => 'Lihat Pengiriman',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 31,
                'name'          => 'add_shipping',
                'desc'          => 'Buat Transaksi Pengiriman',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 31,
                'name'          => 'update_shipping',
                'desc'          => 'Edit Transaksi Pengiriman',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 31,
                'name'          => 'delete_shipping',
                'desc'          => 'Hapus Transaksi Pengiriman',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 31,
                'name'          => 'print_shipping',
                'desc'          => 'Print Transaksi Pengiriman',
                'guard_name'    => 'web'
            ],

            // Faktur
            [
                'module_id'     => 32,
                'name'          => 'sales_faktur_view',
                'desc'          => 'Lihat Faktur Penjualan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 32,
                'name'          => 'add_sales_faktur',
                'desc'          => 'Tambah Faktur Penjualan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 32,
                'name'          => 'update_sales_faktur',
                'desc'          => 'Edit Faktur Penjualan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 32,
                'name'          => 'delete_sales_faktur',
                'desc'          => 'Hapus Faktur Penjualan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 32,
                'name'          => 'print_sales_faktur',
                'desc'          => 'Print Faktur Penjualan',
                'guard_name'    => 'web'
            ],

            // Retur
            [
                'module_id'     => 34,
                'name'          => 'sales_retur_view',
                'desc'          => 'Lihat Retur Penjualan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 34,
                'name'          => 'add_sales_retur',
                'desc'          => 'Tambah Retur Penjualan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 34,
                'name'          => 'delete_sales_retur',
                'desc'          => 'Hapus Retur Penjualan',
                'guard_name'    => 'web'
            ],

            // Payment
            [
                'module_id'     => 33,
                'name'          => 'sales_payment_view',
                'desc'          => 'Lihat Pembayaran Penjualan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 33,
                'name'          => 'add_sales_payment',
                'desc'          => 'Tambah Pembayaran Penjualan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 33,
                'name'          => 'update_sales_payment',
                'desc'          => 'Edit Pembayaran Penjualan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 33,
                'name'          => 'delete_sales_payment',
                'desc'          => 'Hapus Pembayaran Penjualan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 33,
                'name'          => 'print_sales_payment',
                'desc'          => 'Print Pembayaran Penjualan',
                'guard_name'    => 'web'
            ],

            // Customer
            [
                'module_id'     => 35,
                'name'          => 'customer_view',
                'desc'          => 'Lihat Pelanggan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 35,
                'name'          => 'add_customer',
                'desc'          => 'Tambah Pelanggan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 35,
                'name'          => 'update_customer',
                'desc'          => 'Edit Pelanggan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 35,
                'name'          => 'delete_customer',
                'desc'          => 'Hapus Pelanggan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 35,
                'name'          => 'due_customer',
                'desc'          => 'Piutang Pelanggan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 35,
                'name'          => 'delete_due_customer',
                'desc'          => 'Hapus Piutang Pelanggan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 35,
                'name'          => 'history_customer',
                'desc'          => 'Riwayat Transaksi',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 35,
                'name'          => 'saldo_customer_view',
                'desc'          => 'Lihat Saldo Pelanggan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 35,
                'name'          => 'saldo_customer_delete',
                'desc'          => 'Hapus Saldo Pelanggan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 41,
                'name'          => 'rma_view',
                'desc'          => 'Lihat RMA',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 41,
                'name'          => 'add_rma',
                'desc'          => 'Tambah RMA',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 41,
                'name'          => 'update_rma',
                'desc'          => 'Edit RMA',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 41,
                'name'          => 'delete_rma',
                'desc'          => 'Hapus RMA',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 41,
                'name'          => 'print_rma',
                'desc'          => 'Print RMA',
                'guard_name'    => 'web'
            ],
        ];

        Permission::insert($data);
    }
}
