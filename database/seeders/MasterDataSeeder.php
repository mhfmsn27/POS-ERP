<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            // Metode Pembayaran
            [
                'module_id'     => 27,
                'name'          => 'payment_method_view',
                'desc'          => 'Lihat Metode Pembayaran',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 27,
                'name'          => 'payment_method_create',
                'desc'          => 'Tambah Metode Pembayaran',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 27,
                'name'          => 'payment_method_update',
                'desc'          => 'Edit Metode Pembayaran',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 27,
                'name'          => 'payment_method_delete',
                'desc'          => 'Hapus Metode Pembayaran',
                'guard_name'    => 'web'
            ],

            // Payment Category
            [
                'module_id'     => 28,
                'name'          => 'payment_category_view',
                'desc'          => 'Lihat Kategori Pembayaran',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 28,
                'name'          => 'payment_category_create',
                'desc'          => 'Tambah Kategori Pembayaran',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 28,
                'name'          => 'payment_category_update',
                'desc'          => 'Edit Kategori Pembayaran',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 28,
                'name'          => 'payment_category_delete',
                'desc'          => 'Hapus Kategori Pembayaran',
                'guard_name'    => 'web'
            ],

            // Penerimaan
            [
                'module_id'     => 48,
                'name'          => 'cash_int_view',
                'desc'          => 'Lihat Penerimaan Pembayaran',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 48,
                'name'          => 'cash_int_create',
                'desc'          => 'Tambah Penerimaan Pembayaran',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 48,
                'name'          => 'cash_int_update',
                'desc'          => 'Edit Penerimaan Pembayaran',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 48,
                'name'          => 'cash_int_delete',
                'desc'          => 'Hapus Penerimaan Pembayaran',
                'guard_name'    => 'web'
            ],

            // Pembayaran
            [
                'module_id'     => 49,
                'name'          => 'cash_out_view',
                'desc'          => 'Lihat Pembayaran',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 49,
                'name'          => 'cash_out_create',
                'desc'          => 'Tambah Pembayaran',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 49,
                'name'          => 'cash_out_update',
                'desc'          => 'Edit Pembayaran',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 49,
                'name'          => 'cash_out_delete',
                'desc'          => 'Hapus Pembayaran',
                'guard_name'    => 'web'
            ],

            // Mutasi
            [
                'module_id'     => 29,
                'name'          => 'mutation_view',
                'desc'          => 'Lihat Mutasi Bank',
                'guard_name'    => 'web'
            ], 

            // Rekonsiliasi
            [
                'module_id'     => 30,
                'name'          => 'rekonsiliasi_view',
                'desc'          => 'Lihat Rekonsiliasi',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 30,
                'name'          => 'rekonsiliasi_action',
                'desc'          => 'Tindakan Rekonsiliasi',
                'guard_name'    => 'web'
            ],
        ];

        Permission::insert($data);
    }
}
