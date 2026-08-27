<div align="center">

# 🏬 POSHUB ACCOUNTING
### *Next-Generation Enterprise ERP, Omnichannel POS & Financial Accounting Platform*

[![Laravel Version](https://img.shields.io/badge/Laravel-8.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-8.0%20%7C%208.1-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL Support](https://img.shields.io/badge/MySQL-8.0+-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Edition](https://img.shields.io/badge/Edition-Enterprise%20Standalone-10b981?style=for-the-badge)](https://poshub.id)
[![License](https://img.shields.io/badge/License-Lifetime%20Unlimited-1f57db?style=for-the-badge)](https://poshub.id)

<p align="center">
  <strong>Solusi Lengkap ERP Terpadu untuk Retail, Grosir, F&B Resto/Kafe, Distribusi, Jasa, dan E-Commerce.</strong><br>
  Dirancang khusus untuk operasional mandiri (<em>100% Self-Hosted Standalone</em>) dengan akses tanpa batas (<em>Unlimited Lifetime</em>).
</p>

[📖 Panduan Deployment VPS & cPanel](DEPLOYMENT_GUIDE.md) • [🌐 Panduan Interaktif HTML](DEPLOYMENT_GUIDE.html) • [⚡ Hotkeys Kasir](#-pintasan-keyboard-kasir-pos-hotkeys) • [🛠️ Modul Fitur](#-ekosistem-modul--fitur-utama)

---

</div>

## 📌 Sekilas Tentang POSHUB ACCOUNTING

**POSHUB ACCOUNTING Enterprise Edition** adalah sistem otomasi bisnis dan pencatatan keuangan komprehensif (*Enterprise Resource Planning*) yang memadukan titik penjualan kasir modern (**Omnichannel Point of Sale**), pembukuan akuntansi standar berpasangan (**Double-Entry Bookkeeping**), manajemen rantai pasok (**Supply Chain & Inventory**), toko online terintegrasi (**E-Commerce Storefront**), serta otomasi pesan instan (**CRMHUB WhatsApp Gateway**).

Aplikasi ini dapat diinstal secara mandiri pada server VPS (Linux/Ubuntu), Cloud Server, Dedicated Server, maupun Shared Hosting (cPanel) tanpa biaya langganan bulanan/tahunan (*Zero SaaS Fees*).

---

## 🏛️ Arsitektur Sistem

```
+---------------------------------------------------------------------------------------------------+
|                                     POSHUB ACCOUNTING ECOSYSTEM                                   |
+---------------------------------------------------------------------------------------------------+
|                                                                                                   |
|   🛒 FRONT-OFFICE (POS & SALES)               📊 BACK-OFFICE (ACCOUNTING & FINANCE)               |
|   ├── Kasir Cepat (Hotkeys F1-F10)            ├── Jurnal Otomatis (General Journal)               |
|   ├── Antrean Transaksi Offline Sync          ├── Buku Besar & Neraca Saldo (Trial Balance)       |
|   ├── Denah Meja Resto & KDS Dapur            ├── Laporan Laba Rugi (Profit & Loss)               |
|   ├── Dynamic QRIS & Split Bill               ├── Neraca Keuangan & Proyeksi Arus Kas             |
|   └── Cetak Struk & Label Barcode Rak         └── Rekonsiliasi Bank & Depresiasi Aset             |
|                                                                                                   |
|   📦 SUPPLY CHAIN & WAREHOUSE                 🤝 CRM & OMNICHANNEL INTEGRATION                    |
|   ├── Multi-Gudang & Antar Cabang             ├── Struk Digital WhatsApp Otomatis                 |
|   ├── Pelacakan FEFO Batch & Expired          ├── Notifikasi Z-Report Shift ke HP Owner           |
|   ├── Serial Number / IMEI Per Unit           ├── Poin Loyalitas & VIP Member Tier                |
|   └── Auto-Draft PO dari Stock Alert          └── Pengingat Jatuh Tempo Piutang via WA            |
|                                                                                                   |
|   🛍️ E-COMMERCE & ONLINE STORE                🛡️ SYSTEM MAINTENANCE & SECURITY                    |
|   ├── Katalog & Stok Tersinkronisasi          ├── 1-Click Database Backup (.sql)                  |
|   ├── Kalkulator Ongkir RajaOngkir            ├── 1-Click Bersihkan Cache & Optimasi DB           |
|   └── Payment Gateway (Midtrans)              └── Monitor Kapasitas Server Real-Time              |
|                                                                                                   |
+---------------------------------------------------------------------------------------------------+
```

---

## 🚀 Ekosistem Modul & Fitur Utama

### 1. 🛒 Kasir POS Modern & Fleksibel (*Omnichannel Point of Sale*)
* **Multi-Format Kasir**: Mendukung layar sentuh (*Touchscreen*), desktop PC kasir, tablet, dan smartphone mobile.
* **Kecepatan Tinggi dengan Hotkeys (`F1`-`F10`)**: Operasional kasir super cepat tanpa ketergantungan mouse.
* **Resiliensi Jaringan (Mode Offline Queue)**: Transaksi tetap dapat diproses saat koneksi internet terputus dan otomatis tersinkronisasi saat kembali online.
* **Manajemen Resto & Kafe**: Denah meja dinamis (*Table Management*), *Kitchen Display System (KDS)*, dan *Kitchen Order Ticket (KOT)*.
* **Metode Pembayaran Lengkap**: Tunai, QRIS Dinamis, Kartu Debit/Kredit (EDC), Transfer Bank, Pembayaran Tempo/Piutang, Poin Reward, dan *Split Bill* (Pisah Tagihan).

### 2. 📊 Akuntansi & Keuangan Terpadu (*Double-Entry Accounting*)
* **Bagan Akun (Chart of Accounts)**: Struktur akun akuntansi fleksibel dan siap pakai sesuai standar PSAK.
* **Jurnal Transaksi Otomatis**: Setiap penjualan, pembelian, biaya operasional, dan mutasi kas langsung membukukan jurnal debit/kredit tanpa perlu input manual.
* **Laporan Keuangan Real-Time**:
  * 📈 **Laporan Laba Rugi (Income Statement)**
  * ⚖️ **Laporan Neraca (Balance Sheet)**
  * 🌊 **Laporan Arus Kas (Cash Flow Statement)**
  * 📖 **Buku Besar (General Ledger)** & **Neraca Saldo (Trial Balance)**
* **Rekonsiliasi Bank**: Pencocokan mutasi rekening koran bank dengan catatan buku kas.
* **Pengelolaan Aset Tetap**: Pencatatan nilai aset, umur ekonomis, dan kalkulasi otomatis penyusutan/depresiasi berkala.

### 3. 📦 Manajemen Inventori & Rantai Pasok (*Supply Chain*)
* **Multi-Cabang & Multi-Gudang**: Transfer stok antar-cabang dengan status transit, verifikasi terima, dan audit selisih.
* **Pelacakan Batch & Kedaluwarsa (FEFO)**: Cocok untuk industri farmasi, makanan/minuman, dan kosmetik (*First Expired, First Out*).
* **Serial Number & IMEI Tracking**: Pelacakan nomor seri unik per unit barang untuk toko elektronik, gadget, dan komputer.
* **Pengadaan Otomatis (1-Click Auto-Draft PO)**: Memindai produk di bawah batas aman stok minimum dan otomatis membuatkan draf *Purchase Order* siap kirim ke supplier.

### 4. 🏷️ Generator Label Barcode & Rak Produk (*Siap Cetak*)
* **Engine Code-128 SVG Murni**: Ringan, presisi tinggi, dan tanpa dependensi library eksternal.
* **Mendukung Berbagai Ukuran Kertas**:
  * 🖨️ **Thermal Roll Barcode**: Ukuran 33x15mm (3 kolom), 40x30mm (2 kolom), dan 50x30mm (1 kolom).
  * 📄 **Kertas Label Stiker A4 & Tom & Jerry** (T&J 108 / 121).
* **Keterangan Lengkap**: Memuat Nama Toko, Nama Produk, Varian, Barcode SKU, dan Harga Jual (Rp).

### 5. 📱 Integrasi WhatsApp CRMHUB OMNICHANNEL
* **Struk Digital WhatsApp**: Mengirimkan nota resmi pembelian berformat rapi langsung ke nomor WhatsApp pelanggan setelah checkout.
* **Laporan Tutup Shift (Z-Report) ke WhatsApp Owner**: Ringkasan kas harian (modal awal, omset tunai, non-tunai, pengeluaran, dan selisih kas) dikirimkan otomatis ke WhatsApp Pemilik Toko saat kasir menutup shift.
* **Pengingat Piutang Jatuh Tempo**: Notifikasi otomatis penagihan invoice jatuh tempo ke pelanggan.

### 6. 🎁 Program Loyalitas & Member VIP (CRM)
* **Akumulasi Poin Otomatis**: Setiap kelipatan belanja tertentu (misal: Rp 10.000) otomatis menghasilkan 1 Poin loyalitas.
* **Tingkatan Membership Dinamis**:
  * 🥉 **Bronze Member** (0% Auto Diskon)
  * 🥈 **Silver Member** (2% Auto Diskon)
  * 🥇 **Gold VIP** (5% Auto Diskon)
  * 💎 **Platinum VIP** (10% Auto Diskon)
* **Penukaran Poin di Kasir**: Poin dapat langsung ditukarkan menjadi voucher potongan belanja saat pembayaran.

### 7. 🛍️ E-Commerce & Storefront Online (Poshub Ecommerce)
* **Katalog & Stok Terintegrasi**: Sinkronisasi stok antara toko fisik dan toko online secara real-time.
* **Kalkulator Ongkir Ekspedisi**: Terhubung dengan API RajaOngkir untuk perhitungan tarif JNE, J&T, SiCepat, POS, Anteraja, dll.
* **Gerbang Pembayaran Otomatis**: Integrasi Midtrans Payment Gateway untuk pembayaran otomatis.

### 8. 🛡️ Pemeliharaan & Pencadangan Basis Data Mandiri
* **1-Click Database Backup GUI**: Snapshot database `.sql` langsung dari antarmuka admin dengan opsi unduh ke komputer lokal dan pembersihan otomatis cadangan lama (>30 hari).
* **1-Click Maintenance Toolkit**: Pembersihan cache Blade Views, route cache, app cache, serta optimasi query tabel database (`OPTIMIZE TABLE`).
* **Server Health Monitor**: Pemantau kapasitas penyimpanan harddisk server dan status ekstensi PHP.

---

## ⚡ Pintasan Keyboard Kasir (POS Hotkeys)

Untuk mempercepat proses pelayanan pada jam sibuk (*peak hours*), gunakan tombol shortcut berikut:

| Tombol | Fungsi Utama |
| :---: | :--- |
| **`F1`** | Membuka jendela bantuan daftar tombol pintasan keyboard. |
| **`F2`** | Fokus langsung ke kotak **Cari Produk / Scan Barcode**. |
| **`F4`** | **Simpan / Tahan Pesanan** (*Hold Bill* / *Pending Cart*). |
| **`F7`** | Buka laci kasir otomatis (*Kick Cash Drawer*). |
| **`F8`** | **Bayar Uang Pas** (*Exact Cash Payment*). |
| **`F9`** | **Pisah Tagihan** (*Split Bill*). |
| **`F10`** | **Selesaikan Pembayaran** (*Submit Checkout*). |
| **`ESC`** | Menutup modal pop-up / Batalkan aksi. |

---

## 💻 Kebutuhan Sistem & Spesifikasi Server

| Komponen | Spesifikasi Minimum | Rekomendasi Produksi (Enterprise) |
| :--- | :--- | :--- |
| **Sistem Operasi** | Linux (Ubuntu 20.04/22.04 LTS / Debian / AlmaLinux) / Windows Server | **Ubuntu 22.04 / 24.04 LTS** |
| **PHP Version** | PHP 8.0 | **PHP 8.1 (Sangat Disarankan)** |
| **Basis Data** | MySQL 5.7+ / MariaDB 10.3+ | **MySQL 8.0+ / MariaDB 10.6+** |
| **Web Server** | Apache 2.4+ / Nginx | **Nginx + PHP-FPM** |
| **Processor (CPU)** | 1 Core vCPU | 2 Core vCPU atau lebih |
| **Memory (RAM)** | 1 GB RAM (dengan Swap 2 GB) | 2 GB - 4 GB RAM |
| **Penyimpanan (Disk)** | 10 GB SSD | 30 GB+ NVMe SSD |

### Ekstensi PHP Wajib:
`bcmath`, `ctype`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `pdo_mysql`, `tokenizer`, `xml`, `gd`, `curl`, `zip`

---

## 🚀 Panduan Instalasi Cepat (Quick Start)

### 1. Clone Repositori & Masuk ke Folder Proyek
```bash
git clone https://github.com/username-anda/poshub-accounting.git
cd poshub-accounting
```

### 2. Pasang Dependensi Composer
```bash
composer install --no-dev --optimize-autoloader
```

### 3. Konfigurasi Environment (`.env`)
Salin file template lingkungan:
```bash
cp .env.example .env
```
Sesuaikan konfigurasi database Anda di dalam file `.env`:
```ini
APP_NAME="POSHUB ACCOUNTING"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=poshub_accounting
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Inisialisasi Kunci Aplikasi & Symlink Storage
```bash
php artisan key:generate
php artisan storage:link
```

### 5. Jalankan Migrasi & Master Seeder Enterprise
```bash
php artisan migrate --seed
```

### 6. Jalankan Server Pengembangan Lokal
```bash
php artisan serve
```
Akses aplikasi melalui browser di: **`http://127.0.0.1:8000`**

---

## 🔑 Akun Default Sistem

Setelah proses seeding selesai, Anda dapat masuk menggunakan akun default:

| Tipe Pengguna | Email Login | Password Default | Akses Panel |
| :--- | :--- | :--- | :--- |
| **Super Administrator** | `admin@poshub.id` | `123456` | `/administrator` (Dashboard Pusat) |
| **Manager Toko / Kasir** | `admin@poshub.id` | `123456` | `/app` (Panel Operasional Bisnis) |

> ⚠️ **PENTING**: Segera ubah kata sandi default setelah berhasil masuk untuk keamanan sistem Anda!

---

## 🌐 Dokumentasi Lengkap Deployment

Untuk panduan deployment langkah-demi-langkah ke server VPS atau cPanel:
* 📄 **[DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md)** — Panduan format teks Markdown.
* 🌐 **[DEPLOYMENT_GUIDE.html](DEPLOYMENT_GUIDE.html)** — Panduan interaktif dengan fitur salin kode 1-klik (Dapat dibuka langsung di browser).

---

## 📄 Hak Cipta & Lisensi

Hak Cipta © 2026 **POSHUB ENTERPRISE**. Seluruh hak cipta dilindungi undang-undang.
Dilisensikan untuk penggunaan mandiri internal perusahaan (*Standalone Enterprise Edition*).
