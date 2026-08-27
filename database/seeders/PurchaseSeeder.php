<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PurchaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            // Received Seeder
            [
                'module_id'     => 36,
                'name'          => 'received_view',
                'desc'          => 'Lihat Penerimaan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 36,
                'name'          => 'add_received',
                'desc'          => 'Buat Transaksi Penerimaan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 36,
                'name'          => 'update_received',
                'desc'          => 'Edit Transaksi Penerimaan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 36,
                'name'          => 'delete_received',
                'desc'          => 'Hapus Transaksi Penerimaan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 36,
                'name'          => 'print_received',
                'desc'          => 'Print Transaksi Penerimaan',
                'guard_name'    => 'web'
            ],

            // Faktur
            [
                'module_id'     => 37,
                'name'          => 'purchase_faktur_view',
                'desc'          => 'Lihat Faktur Pembelian',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 37,
                'name'          => 'add_purchase_faktur',
                'desc'          => 'Tambah Faktur Pembelian',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 37,
                'name'          => 'update_purchase_faktur',
                'desc'          => 'Edit Faktur Pembelian',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 37,
                'name'          => 'delete_purchase_faktur',
                'desc'          => 'Hapus Faktur Pembelian',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 37,
                'name'          => 'print_purchase_faktur',
                'desc'          => 'Print Faktur Pembelian',
                'guard_name'    => 'web'
            ],

            // Retur
            [
                'module_id'     => 39,
                'name'          => 'purchase_retur_view',
                'desc'          => 'Lihat Retur Pembelian',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 39,
                'name'          => 'add_purchase_retur',
                'desc'          => 'Tambah Retur Pembelian',
                'guard_name'    => 'web'
            ], 
            [
                'module_id'     => 39,
                'name'          => 'delete_purchase_retur',
                'desc'          => 'Hapus Retur Pembelian',
                'guard_name'    => 'web'
            ],

            // Payment
            [
                'module_id'     => 38,
                'name'          => 'purchase_payment_view',
                'desc'          => 'Lihat Pembayaran Pembelian',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 38,
                'name'          => 'add_purchase_payment',
                'desc'          => 'Tambah Pembayaran Pembelian',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 38,
                'name'          => 'update_purchase_payment',
                'desc'          => 'Edit Pembayaran Pembelian',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 38,
                'name'          => 'delete_purchase_payment',
                'desc'          => 'Hapus Pembayaran Pembelian',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 38,
                'name'          => 'print_purchase_payment',
                'desc'          => 'Print Pembayaran Pembelian',
                'guard_name'    => 'web'
            ],

            // Pemasok
            [
                'module_id'     => 40,
                'name'          => 'supplier_view',
                'desc'          => 'Lihat Pemasok',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 40,
                'name'          => 'add_supplier',
                'desc'          => 'Tambah Pemasok',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 40,
                'name'          => 'update_supplier',
                'desc'          => 'Edit Pemasok',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 40,
                'name'          => 'delete_supplier',
                'desc'          => 'Hapus Pemasok',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 40,
                'name'          => 'due_supplier',
                'desc'          => 'Hutang Pemasok',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 40,
                'name'          => 'delete_due_supplier',
                'desc'          => 'Hapus Hutang Pemasok',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 40,
                'name'          => 'saldo_supplier',
                'desc'          => 'Saldo Pemasok',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 40,
                'name'          => 'delete_saldo_supplier',
                'desc'          => 'Hapus Saldo Pemasok',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 40,
                'name'          => 'history_supplier',
                'desc'          => 'Riwayat Transaksi Pemasok',
                'guard_name'    => 'web'
            ], 

        ];

        Permission::insert($data);
    }
}
