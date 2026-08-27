<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            // Akun Perkiraan
            [
                'module_id'     => 22,
                'name'          => 'account_type_view',
                'desc'          => 'Lihat Tipe Akun',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 22,
                'name'          => 'account_type_create',
                'desc'          => 'Tambah Tipe Akun',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 22,
                'name'          => 'account_type_update',
                'desc'          => 'Edit Tipe Akun',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 22,
                'name'          => 'account_type_delete',
                'desc'          => 'Hapus Tipe Akun',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 22,
                'name'          => 'account_view',
                'desc'          => 'Lihat Akun Perkiraan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 22,
                'name'          => 'account_create',
                'desc'          => 'Tambah Akun Perkiraan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 22,
                'name'          => 'account_update',
                'desc'          => 'Edit Akun Perkiraan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 22,
                'name'          => 'account_delete',
                'desc'          => 'Hapus Akun Perkiraan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 22,
                'name'          => 'account_history',
                'desc'          => 'Riwayat Akun Perkiraan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 22,
                'name'          => 'account_deposit',
                'desc'          => 'Deposit Akun Perkiraan',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 22,
                'name'          => 'account_transfer',
                'desc'          => 'Transfer Akun Perkiraan',
                'guard_name'    => 'web'
            ],

            // Jurnal Umum
            [
                'module_id'     => 23,
                'name'          => 'journal_view',
                'desc'          => 'Lihat Jurnal Umum',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 23,
                'name'          => 'journal_create',
                'desc'          => 'Tambah Jurnal Umum',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 23,
                'name'          => 'journal_update',
                'desc'          => 'Edit Jurnal Umum',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 23,
                'name'          => 'journal_delete',
                'desc'          => 'Hapus Jurnal Umum',
                'guard_name'    => 'web'
            ],

            // Kasbon Pegawai
            [
                'module_id'     => 24,
                'name'          => 'kasbon_view',
                'desc'          => 'Lihat Kasbon Pegawai',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 24,
                'name'          => 'kasbon_create',
                'desc'          => 'Tambah Kasbon Pegawai',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 24,
                'name'          => 'kasbon_update',
                'desc'          => 'Edit Kasbon Pegawai',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 24,
                'name'          => 'kasbon_delete',
                'desc'          => 'Hapus Kasbon Pegawai',
                'guard_name'    => 'web'
            ], 

            // Gaji Pegawai
            [
                'module_id'     => 25,
                'name'          => 'salary_view',
                'desc'          => 'Lihat Gaji Pegawai',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 25,
                'name'          => 'salary_create',
                'desc'          => 'Tambah Gaji Pegawai',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 25,
                'name'          => 'salary_update',
                'desc'          => 'Edit Gaji Pegawai',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 25,
                'name'          => 'salary_delete',
                'desc'          => 'Hapus Gaji Pegawai',
                'guard_name'    => 'web'
            ], 

            // Komisi
            [
                'module_id'     => 26,
                'name'          => 'commission_view',
                'desc'          => 'Lihat Komisi Pegawai',
                'guard_name'    => 'web'
            ],
        ];

        Permission::insert($data);
    }
}
