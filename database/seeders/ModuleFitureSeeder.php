<?php

namespace Database\Seeders;

use App\Models\Admin\ModuleFeature;
use Illuminate\Database\Seeder;

class ModuleFitureSeeder extends Seeder
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
                'id'            => 1,
                'name'          => 'Preferensi',
            ],
            [
                'id'            => 2,
                'name'          => 'Role Permission',
            ],
            [
                'id'            => 3,
                'name'          => 'Pengguna',
            ],
            [
                'id'            => 4,
                'name'          => 'Whatsapp Device',
            ],
            [
                'id'            => 5,
                'name'          => 'Syarat Pembayaran',
            ],
            [
                'id'            => 6,
                'name'          => 'Data Ekspedisi',
            ],
            [
                'id'            => 7,
                'name'          => 'Data Devisi',
            ],
            [
                'id'            => 8,
                'name'          => 'Data Jabatan',
            ],
            [
                'id'            => 9,
                'name'          => 'Data Pegawai',
            ],
            [
                'id'            => 10,
                'name'          => 'Data Printer',
            ],
            [
                'id'            => 11,
                'name'          => 'Data Pajak',
            ],
            [
                'id'            => 12,
                'name'          => 'Tunjangan',
            ],
            [
                'id'            => 13,
                'name'          => 'Potongan',
            ],
            [
                'id'            => 14,
                'name'          => 'Barang dan Jasa',
            ],
            [
                'id'            => 15,
                'name'          => 'Kategori Barang dan Jasa',
            ],

            // Sec 02
            [
                'id'            => 16,
                'name'          => 'Brand',
            ],
            [
                'id'            => 17,
                'name'          => 'Satuan',
            ],
            [
                'id'            => 18,
                'name'          => 'Etalase',
            ],
            [
                'id'            => 19,
                'name'          => 'Gudang',
            ],
            [
                'id'            => 20,
                'name'          => 'Pemindahan Barang',
            ],
            [
                'id'            => 21,
                'name'          => 'Stok Opname',
            ],
            [
                'id'            => 22,
                'name'          => 'Akun Perkiraan',
            ],
            [
                'id'            => 23,
                'name'          => 'Jurnal Umum',
            ],
            [
                'id'            => 24,
                'name'          => 'Kasbon Pegawai',
            ],
            [
                'id'            => 25,
                'name'          => 'Gaji Pegawai',
            ],
            [
                'id'            => 26,
                'name'          => 'Komisi Pegawai',
            ],
            [
                'id'            => 27,
                'name'          => 'Metode Pembayaran',
            ],
            [
                'id'            => 28,
                'name'          => 'Kategori Pembayaran',
            ],
            [
                'id'            => 29,
                'name'          => 'Mutasi Bank',
            ],
            [
                'id'            => 30,
                'name'          => 'Rekonsiliasi',
            ],

            // Sec 03
            [
                'id'            => 31,
                'name'          => 'Pengiriman Pesanan',
            ],
            [
                'id'            => 32,
                'name'          => 'Faktur Penjualan',
            ],
            [
                'id'            => 33,
                'name'          => 'Penerimaan Penjualan',
            ],
            [
                'id'            => 34,
                'name'          => 'Retur Penjualan',
            ],
            [
                'id'            => 35,
                'name'          => 'Pelanggan',
            ],
            [
                'id'            => 36,
                'name'          => 'Penerimaan Pembelian',
            ],
            [
                'id'            => 37,
                'name'          => 'Faktur Pembelian',
            ],
            [
                'id'            => 38,
                'name'          => 'Pembayaran Pembelian',
            ],
            [
                'id'            => 39,
                'name'          => 'Retur Pembelian',
            ],
            [
                'id'            => 40,
                'name'          => 'Pemasok',
            ],
            [
                'id'            => 41,
                'name'          => 'RMA',
            ],
            [
                'id'            => 42,
                'name'          => 'Laporan',
            ],
            [
                'id'            => 43,
                'name'          => 'E-Commerce Media Konten ',
            ],
            [
                'id'            => 44,
                'name'          => 'E-Commerce Blog & Page',
            ],
            [
                'id'            => 45,
                'name'          => 'E-Commerce Setting',
            ],
            [
                'id'            => 46,
                'name'          => 'E-Commerce Transaction',
            ],
            [
                'id'            => 47,
                'name'          => 'POS',
            ],
            [
                'id'            => 48,
                'name'          => 'Penerimaan Pembayaran',
            ],
            [
                'id'            => 49,
                'name'          => 'Pembayaran Pembayaran',
            ],
            [
                'id'            => 50,
                'name'          => 'Dashboard',
            ],
        ];

        ModuleFeature::insert($data);
    }
}
