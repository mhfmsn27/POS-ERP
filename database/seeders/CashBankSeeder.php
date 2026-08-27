<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class CashBankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            // Payment Method
            [
                'name'          => 'payment_method_view',
                'desc'          => 'Lihat Metode Pembayaran',
                'guard_name'    => 'web'
            ],
            [
                'name'          => 'add_payment_method',
                'desc'          => 'Tambah Metode Pembayaran',
                'guard_name'    => 'web'
            ],
            [
                'name'          => 'update_payment_method',
                'desc'          => 'Edit Metode Pembayaran',
                'guard_name'    => 'web'
            ],
            [
                'name'          => 'delete_payment_method',
                'desc'          => 'Hapus Metode Pembayaran',
                'guard_name'    => 'web'
            ],

            // Expense Category 
            [
                'name'          => 'expense_category_view',
                'desc'          => 'Lihat Kategori Kas masuk dan Keluar',
                'guard_name'    => 'web'
            ],
            [
                'name'          => 'add_expense_category',
                'desc'          => 'Tambah Kategori Kas masuk dan Keluar',
                'guard_name'    => 'web'
            ],
            [
                'name'          => 'update_expense_category',
                'desc'          => 'Edit Kategori Kas Masuk dan Keluar',
                'guard_name'    => 'web'
            ],
            [
                'name'          => 'delete_expense_category',
                'desc'          => 'Hapus Kategori Kas Masuk dan Keluar',
                'guard_name'    => 'web'
            ],

            // Pembayaran
            [
                'name'          => 'expense_view',
                'desc'          => 'Lihat Kategori Kas masuk dan Keluar',
                'guard_name'    => 'web'
            ],
            [
                'name'          => 'add_expense',
                'desc'          => 'Tambah Kategori Kas masuk dan Keluar',
                'guard_name'    => 'web'
            ],
            [
                'name'          => 'update_expense',
                'desc'          => 'Edit Kategori Kas Masuk dan Keluar',
                'guard_name'    => 'web'
            ],
            [
                'name'          => 'delete_expense',
                'desc'          => 'Hapus Kategori Kas Masuk dan Keluar',
                'guard_name'    => 'web'
            ],


            // Penerimaan
            [
                'name'          => 'cash_int_view',
                'desc'          => 'Lihat Penerimaan',
                'guard_name'    => 'web'
            ],
            [
                'name'          => 'add_cash_int',
                'desc'          => 'Tambah Penerimaan',
                'guard_name'    => 'web'
            ],
            [
                'name'          => 'update_cash_int',
                'desc'          => 'Edit Penerimaan',
                'guard_name'    => 'web'
            ],
            [
                'name'          => 'delete_cash_int',
                'desc'          => 'Hapus Penerimaan',
                'guard_name'    => 'web'
            ],

            // Bank History
            [
                'name'          => 'bank_history_view',
                'desc'          => 'Lihat Histori Mutasi Bank',
                'guard_name'    => 'web'
            ],

            // Rekonsiliasi
            [
                'name'          => 'rekonsiliasi',
                'desc'          => 'Lihat Rekonsiliasi Bank',
                'guard_name'    => 'web'
            ],
            [
                'name'          => 'update_rekonsiliasi',
                'desc'          => 'Edit Rekonsiliasi Bank',
                'guard_name'    => 'web'
            ],
           
        ];

        Permission::insert($data);
    }
}
