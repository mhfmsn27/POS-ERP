<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            // Pengaturan Akun
            [
                'module_id'     => 1,
                'name'          => 'account_crm',
                'desc'          => 'Akun CRM',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 1,
                'name'          => 'account_product',
                'desc'          => 'Akun Produk',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 1,
                'name'          => 'account_transaction',
                'desc'          => 'Akun Transaksi',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 1,
                'name'          => 'account_tax',
                'desc'          => 'Akun Pajak',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 1,
                'name'          => 'key',
                'desc'          => 'Key Transaksi',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 1,
                'name'          => 'hrm',
                'desc'          => 'Hrm Setting',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 1,
                'name'          => 'notification',
                'desc'          => 'Pemberitahuan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 1,
                'name'          => 'store_sett',
                'desc'          => 'Pengaturan Toko / Cabang',
                'guard_name'    => 'web'
            ],

            // Role Permission
            [
                'module_id'     => 2,
                'name'          => 'role_view',
                'desc'          => 'Lihat Group Pengguna',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 2,
                'name'          => 'role_create',
                'desc'          => 'Tambah Group Pengguna',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 2,
                'name'          => 'role_update',
                'desc'          => 'Edit Group Pengguna',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 2,
                'name'          => 'role_delete',
                'desc'          => 'Hapus Group Pengguna',
                'guard_name'    => 'web'
            ],

            // User Role
            [
                'module_id'     => 3,
                'name'          => 'user_view',
                'desc'          => 'Lihat Pengguna',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 3,
                'name'          => 'user_create',
                'desc'          => 'Tambah Pengguna',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 3,
                'name'          => 'user_update',
                'desc'          => 'Edit Pengguna',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 3,
                'name'          => 'user_delete',
                'desc'          => 'Hapus Pengguna',
                'guard_name'    => 'web'
            ],

            // Whatsapp Device
            [
                'module_id'     => 4,
                'name'          => 'device_view',
                'desc'          => 'Lihat Device Whatsapp',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 4,
                'name'          => 'device_create',
                'desc'          => 'Tambah Device Whatsapp',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 4,
                'name'          => 'device_update',
                'desc'          => 'Edit Device Whatsapp',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 4,
                'name'          => 'device_delete',
                'desc'          => 'Hapus Device Whatsapp',
                'guard_name'    => 'web'
            ],

            // Syarat Pembayaran
            [
                'module_id'     => 5,
                'name'          => 'payment_term_view',
                'desc'          => 'Lihat Syarat Pembayaran',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 5,
                'name'          => 'payment_term_create',
                'desc'          => 'Tambah Syarat Pembayaran',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 5,
                'name'          => 'payment_term_update',
                'desc'          => 'Edit Syarat Pembayaran',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 5,
                'name'          => 'payment_term_delete',
                'desc'          => 'Hapus Syarat Pembayaran',
                'guard_name'    => 'web'
            ],

            // Ekspedisi
            [
                'module_id'     => 6,
                'name'          => 'expedition_view',
                'desc'          => 'Lihat Ekspedisi',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 6,
                'name'          => 'expedition_create',
                'desc'          => 'Tambah Ekspedisi',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 6,
                'name'          => 'expedition_update',
                'desc'          => 'Edit Ekspedisi',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 6,
                'name'          => 'expedition_delete',
                'desc'          => 'Hapus Ekspedisi',
                'guard_name'    => 'web'
            ],

            // Devision
            [
                'module_id'     => 7,
                'name'          => 'devision_view',
                'desc'          => 'Lihat Devisi',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 7,
                'name'          => 'devision_create',
                'desc'          => 'Tambah Devisi',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 7,
                'name'          => 'devision_update',
                'desc'          => 'Edit Devisi',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 7,
                'name'          => 'devision_delete',
                'desc'          => 'Hapus Devisi',
                'guard_name'    => 'web'
            ],

            // Designation
            [
                'module_id'     => 8,
                'name'          => 'designation_view',
                'desc'          => 'Lihat Jabatan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 8,
                'name'          => 'designation_create',
                'desc'          => 'Tambah Jabatan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 8,
                'name'          => 'designation_update',
                'desc'          => 'Edit Jabatan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 8,
                'name'          => 'designation_delete',
                'desc'          => 'Hapus Jabatan',
                'guard_name'    => 'web'
            ],

            // Employee
            [
                'module_id'     => 9,
                'name'          => 'employee_view',
                'desc'          => 'Lihat Pegawai',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 9,
                'name'          => 'employee_create',
                'desc'          => 'Tambah Pegawai',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 9,
                'name'          => 'employee_update',
                'desc'          => 'Edit Pegawai',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 9,
                'name'          => 'employee_delete',
                'desc'          => 'Hapus Pegawai',
                'guard_name'    => 'web'
            ],

            // Printer
            [
                'module_id'     => 10,
                'name'          => 'printer_view',
                'desc'          => 'Lihat Printer',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 10,
                'name'          => 'printer_create',
                'desc'          => 'Tambah Printer',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 10,
                'name'          => 'printer_update',
                'desc'          => 'Edit Printer',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 10,
                'name'          => 'printer_delete',
                'desc'          => 'Hapus Printer',
                'guard_name'    => 'web'
            ],

            // Pajak
            [
                'module_id'     => 11,
                'name'          => 'tax_view',
                'desc'          => 'Lihat Pajak',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 11,
                'name'          => 'tax_create',
                'desc'          => 'Tambah Pajak',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 11,
                'name'          => 'tax_update',
                'desc'          => 'Edit Pajak',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 11,
                'name'          => 'tax_delete',
                'desc'          => 'Hapus Pajak',
                'guard_name'    => 'web'
            ],

            // Tunjangan
            [
                'module_id'     => 12,
                'name'          => 'allowance_view',
                'desc'          => 'Lihat Tunjangan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 12,
                'name'          => 'allowance_create',
                'desc'          => 'Tambah Tunjangan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 12,
                'name'          => 'allowance_update',
                'desc'          => 'Edit Tunjangan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 12,
                'name'          => 'allowance_delete',
                'desc'          => 'Hapus Tunjangan',
                'guard_name'    => 'web'
            ],

            // Potongan
            [
                'module_id'     => 13,
                'name'          => 'deduction_view',
                'desc'          => 'Lihat Potongan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 13,
                'name'          => 'deduction_create',
                'desc'          => 'Tambah Potongan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 13,
                'name'          => 'deduction_update',
                'desc'          => 'Edit Potongan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 13,
                'name'          => 'deduction_delete',
                'desc'          => 'Hapus Potongan',
                'guard_name'    => 'web'
            ],

        ];

        Permission::insert($data);
    }
}
