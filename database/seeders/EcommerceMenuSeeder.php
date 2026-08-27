<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\PluginMenu;
use Illuminate\Database\Seeder;

class EcommerceMenuSeeder extends Seeder
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
                'module_id'     => 43,
                'name'          => 'media_view',
                'desc'          => 'Lihat Media Konten',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 43,
                'name'          => 'media_create',
                'desc'          => 'Tambah Media Konten',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 43,
                'name'          => 'media_update',
                'desc'          => 'Edit Media Konten',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 43,
                'name'          => 'media_delete',
                'desc'          => 'Hapus Media Konten',
                'guard_name'    => 'web'
            ],

            // Blog
            [
                'module_id'     => 44,
                'name'          => 'blog_view',
                'desc'          => 'Lihat Blog & Page',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 44,
                'name'          => 'blog_create',
                'desc'          => 'Tambah Blog & Page',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 44,
                'name'          => 'blog_update',
                'desc'          => 'Edit Blog & Page',
                'guard_name'    => 'web'
            ],
            [
                'module_id'     => 44,
                'name'          => 'blog_delete',
                'desc'          => 'Hapus Blog & Page',
                'guard_name'    => 'web'
            ],

            // Setting
            [
                'module_id'     => 45,
                'name'          => 'setting_ecommerce_view',
                'desc'          => 'Lihat Pengaturan',
                'guard_name'    => 'web'
            ], 
            [
                'module_id'     => 45,
                'name'          => 'setting_ecommerce_update',
                'desc'          => 'Edit Pengaturan',
                'guard_name'    => 'web'
            ], 

            // Transaction
            [
                'module_id'     => 46,
                'name'          => 'transaction_ecommerce_view',
                'desc'          => 'Lihat Pesanan',
                'guard_name'    => 'web'
            ], 
            [
                'module_id'     => 46,
                'name'          => 'transaction_ecommerce_process',
                'desc'          => 'Proses Pesanan',
                'guard_name'    => 'web'
            ], 
            [
                'module_id'     => 47,
                'name'          => 'pos',
                'desc'          => 'POS',
                'guard_name'    => 'web'
            ],  

            // Dashboard
            [
                'module_id'     => 50,
                'name'          => 'd_analytic_sales',
                'desc'          => 'Analisis Penjualan',
                'guard_name'    => 'web'
            ], 
            [
                'module_id'     => 50,
                'name'          => 'd_log_activity',
                'desc'          => 'Log Aktivitas',
                'guard_name'    => 'web'
            ], 
            [
                'module_id'     => 50,
                'name'          => 'd_stock_minimum',
                'desc'          => 'Stok Minimum',
                'guard_name'    => 'web'
            ],  
            [
                'module_id'     => 50,
                'name'          => 'd_stock_minus',
                'desc'          => 'Stok Minus',
                'guard_name'    => 'web'
            ], 
            [
                'module_id'     => 50,
                'name'          => 'd_profit_loss_year',
                'desc'          => 'Laba Rugi Tahun ini',
                'guard_name'    => 'web'
            ], 
            [
                'module_id'     => 50,
                'name'          => 'd_customer_due',
                'desc'          => 'Pelanggan Ber-Utang',
                'guard_name'    => 'web'
            ],  

            [
                'module_id'     => 50,
                'name'          => 'd_user_active',
                'desc'          => 'Pengguna Aktif',
                'guard_name'    => 'web'
            ], 
            [
                'module_id'     => 50,
                'name'          => 'd_sales_up',
                'desc'          => 'Penjualan Teratas',
                'guard_name'    => 'web'
            ], 
            [
                'module_id'     => 50,
                'name'          => 'd_customer_up',
                'desc'          => 'Pelanggan Teratas',
                'guard_name'    => 'web'
            ],  

            [
                'module_id'     => 50,
                'name'          => 'd_daily_sale',
                'desc'          => 'Penjualan Harian',
                'guard_name'    => 'web'
            ], 
            [
                'module_id'     => 50,
                'name'          => 'd_rekon',
                'desc'          => 'Rekonsiliasi Bank',
                'guard_name'    => 'web'
            ], 
            [
                'module_id'     => 50,
                'name'          => 'd_due_company',
                'desc'          => 'Piutang Usaha',
                'guard_name'    => 'web'
            ],  
            [
                'module_id'     => 50,
                'name'          => 'd_due',
                'desc'          => 'Hutang Usaha',
                'guard_name'    => 'web'
            ], 
             
        ];

        Permission::insert($data);
    }
}
