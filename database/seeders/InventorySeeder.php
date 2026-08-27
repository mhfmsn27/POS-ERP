<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            // Barang dan JAsa
            [
                'module_id'     => 14,
                'name'          => 'product_view',
                'desc'          => 'Lihat Barang dan Jasa',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 14,
                'name'          => 'product_create',
                'desc'          => 'Tambah Barang dan Jasa',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 14,
                'name'          => 'product_update',
                'desc'          => 'Edit Barang dan Jasa',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 14,
                'name'          => 'product_delete',
                'desc'          => 'Delete Barang dan Jasa',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 14,
                'name'          => 'product_stock',
                'desc'          => 'Lihat Stok Barang dan Jasa',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 14,
                'name'          => 'product_history',
                'desc'          => 'Riwayat Arus Stok',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 14,
                'name'          => 'product_update_price',
                'desc'          => 'Edit Harga Barang dan Jasa',
                'guard_name'    => 'web'
            ],

            // Kategori
            [
                'module_id'     => 15,
                'name'          => 'category_view',
                'desc'          => 'Lihat Kategori Barang dan Jasa',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 15,
                'name'          => 'category_create',
                'desc'          => 'Tambah Kategori Barang dan Jasa',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 15,
                'name'          => 'category_update',
                'desc'          => 'Edit Kategori Barang dan Jasa',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 15,
                'name'          => 'category_delete',
                'desc'          => 'Hapus Kategori Barang dan Jasa',
                'guard_name'    => 'web'
            ],

            // Brand
            [
                'module_id'     => 16,
                'name'          => 'brand_view',
                'desc'          => 'Lihat Brand',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 16,
                'name'          => 'brand_create',
                'desc'          => 'Tambah Brand',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 16,
                'name'          => 'brand_update',
                'desc'          => 'Edit Brand',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 16,
                'name'          => 'brand_delete',
                'desc'          => 'Hapus Brand',
                'guard_name'    => 'web'
            ],

            // Unit
            [
                'module_id'     => 17,
                'name'          => 'unit_view',
                'desc'          => 'Lihat Satuan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 17,
                'name'          => 'unit_create',
                'desc'          => 'Tambah Satuan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 17,
                'name'          => 'unit_update',
                'desc'          => 'Edit Satuan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 17,
                'name'          => 'unit_delete',
                'desc'          => 'Hapus Satuan',
                'guard_name'    => 'web'
            ],

            // Etalase
            [
                'module_id'     => 18,
                'name'          => 'rak_view',
                'desc'          => 'Lihat Etalase',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 18,
                'name'          => 'rak_create',
                'desc'          => 'Tambah Etalase',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 18,
                'name'          => 'rak_update',
                'desc'          => 'Edit Etalase',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 18,
                'name'          => 'rak_delete',
                'desc'          => 'Hapus Etalase',
                'guard_name'    => 'web'
            ],

            // Gudang
            [
                'module_id'     => 19,
                'name'          => 'warehouse_view',
                'desc'          => 'Lihat Gudang',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 19,
                'name'          => 'warehouse_create',
                'desc'          => 'Tambah Gudang',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 19,
                'name'          => 'warehouse_update',
                'desc'          => 'Edit Gudang',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 19,
                'name'          => 'warehouse_delete',
                'desc'          => 'Hapus Gudang',
                'guard_name'    => 'web'
            ],

            // Pemindahan Barang
            [
                'module_id'     => 20,
                'name'          => 'warehouse_transfer_view',
                'desc'          => 'Lihat Pemindahan Barang',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 20,
                'name'          => 'warehouse_transfer_create',
                'desc'          => 'Tambah Pemindahan Barang',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 20,
                'name'          => 'warehouse_transfer_update',
                'desc'          => 'Edit Pemindahan Barang',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 20,
                'name'          => 'warehouse_transfer_delete',
                'desc'          => 'Hapus Pemindahan Barang',
                'guard_name'    => 'web'
            ],

            // Stok Opname
            [
                'module_id'     => 21,
                'name'          => 'adjustment_view',
                'desc'          => 'Lihat Stok Opname',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 21,
                'name'          => 'adjustment_create',
                'desc'          => 'Tambah Stok Opname',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 21,
                'name'          => 'adjustment_update',
                'desc'          => 'Edit Stok Opname',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 21,
                'name'          => 'adjustment_delete',
                'desc'          => 'Hapus Stok Opname',
                'guard_name'    => 'web'
            ],
        ];

        Permission::insert($data);
    }
}
