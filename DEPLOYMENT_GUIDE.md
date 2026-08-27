# 🚀 PANDUAN LENGKAP DEPLOYMENT POSHUB ACCOUNTING
### Standalone Enterprise Edition (VPS & Shared Hosting)

Dokumen ini berisi panduan langkah demi langkah (*step-by-step*) untuk melakukan deployment aplikasi **POSHUB ACCOUNTING Enterprise Edition** ke server produksi, baik menggunakan **Virtual Private Server (VPS)** maupun **Shared Hosting (cPanel / DirectAdmin)**.

---

## 📋 Daftar Isi
1. [Spesifikasi & Kebutuhan Server](#1-spesifikasi--kebutuhan-server)
2. [Panduan Deployment ke VPS (Ubuntu / Nginx / Apache)](#2-panduan-deployment-ke-vps-ubuntu--nginx)
3. [Panduan Deployment ke Shared Hosting (cPanel)](#3-panduan-deployment-ke-shared-hosting-cpanel)
4. [Konfigurasi Background Queue & Task Scheduler](#4-konfigurasi-background-queue--task-scheduler)
5. [Optimasi Performa & Keamanan Produksi](#5-optimasi-performa--keamanan-produksi)
6. [Troubleshooting & Solusi Masalah Umum](#6-troubleshooting--solusi-masalah-umum)

---

## 1. Spesifikasi & Kebutuhan Server

| Komponen | Rekomendasi Minimum | Rekomendasi Ideal (Enterprise) |
| :--- | :--- | :--- |
| **Sistem Operasi** | Linux (Ubuntu 20.04/22.04 LTS / Debian / AlmaLinux) | Ubuntu 22.04 / 24.04 LTS |
| **Prosesor (CPU)** | 1 Core vCPU | 2 Core vCPU atau lebih |
| **Memori (RAM)** | 1 GB RAM (dengan Swap 2 GB) | 2 GB - 4 GB RAM |
| **Penyimpanan (Disk)** | 10 GB SSD | 25 GB+ NVMe SSD |
| **Versi PHP** | PHP 8.0 atau PHP 8.1 | **PHP 8.1 (Disarankan)** |
| **Basis Data** | MySQL 5.7+ / MariaDB 10.3+ | **MySQL 8.0+ / MariaDB 10.6+** |
| **Web Server** | Nginx atau Apache 2.4+ | **Nginx + PHP-FPM** |

### Ekstensi PHP yang Wajib Aktif:
- `bcmath`, `ctype`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `pdo_mysql`, `tokenizer`, `xml`, `gd`, `curl`, `zip`

---

## 2. Panduan Deployment ke VPS (Ubuntu / Nginx)

### Langkah 1: Update Server & Instal Paket Pendukung
Buka terminal SSH Anda dan jalankan perintah berikut:
```bash
sudo apt update && sudo apt upgrade -y
sudo apt install -y software-properties-common curl git unzip zip nginx supervisor
```

### Langkah 2: Instal PHP 8.1 & Ekstensi
```bash
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.1-fpm php8.1-cli php8.1-common php8.1-mysql php8.1-zip \
                    php8.1-gd php8.1-mbstring php8.1-curl php8.1-xml php8.1-bcmath
```

### Langkah 3: Instal Composer
```bash
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
```

### Langkah 4: Setup Database MySQL
Masuk ke MySQL console:
```bash
sudo mysql -u root -p
```
Jalankan perintah SQL berikut (ganti password dengan kata sandi aman Anda):
```sql
CREATE DATABASE poshub_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'poshub_user'@'localhost' IDENTIFIED BY 'PasswordSangatKuat123!';
GRANT ALL PRIVILEGES ON poshub_db.* TO 'poshub_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Langkah 5: Unggah & Konfigurasi Source Code
Letakkan source code aplikasi di direktori `/var/www/poshub`:
```bash
# Opsi A: Menggunakan Git Clone
sudo git clone https://github.com/akun-anda/poshub.git /var/www/poshub

# Opsi B: Menggunakan SCP / SFTP (upload file .zip lalu extract)
sudo unzip poshub.zip -d /var/www/poshub
```

Pindah ke folder aplikasi:
```bash
cd /var/www/poshub
```

Salin file `.env`:
```bash
cp .env.example .env
nano .env
```
Sesuaikan konfigurasi berikut pada file `.env`:
```ini
APP_NAME="POSHUB ACCOUNTING"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://poshub.domainanda.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=poshub_db
DB_USERNAME=poshub_user
DB_PASSWORD=PasswordSangatKuat123!
```

### Langkah 6: Instal Dependensi & Inisialisasi Database
```bash
# Instal dependensi PHP produksi
composer install --no-dev --optimize-autoloader

# Generate Application Key
php artisan key:generate --force

# Buat Symlink Storage
php artisan storage:link

# Jalankan Migrasi & Seeder Enterprise
php artisan migrate --seed --force
```

### Langkah 7: Atur Hak Akses Folder (Permissions)
```bash
sudo chown -R www-data:www-data /var/www/poshub
sudo find /var/www/poshub -type f -exec chmod 644 {} \;
sudo find /var/www/poshub -type d -exec chmod 755 {} \;
sudo chmod -R 775 /var/www/poshub/storage /var/www/poshub/bootstrap/cache
```

### Langkah 8: Konfigurasi Virtual Host Nginx
Buat file konfigurasi Nginx:
```bash
sudo nano /etc/nginx/sites-available/poshub.conf
```
Isi dengan konfigurasi berikut (ganti `poshub.domainanda.com` dengan domain Anda):
```nginx
server {
    listen 80;
    server_name poshub.domainanda.com;
    root /var/www/poshub/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    index index.php index.html;
    charset utf-8;

    client_max_body_size 50M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Aktifkan konfigurasi Nginx:
```bash
sudo ln -s /etc/nginx/sites-available/poshub.conf /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Langkah 9: Pasang Sertifikat SSL Gratis (Certbot Let's Encrypt)
```bash
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d poshub.domainanda.com
```

---

## 3. Panduan Deployment ke Shared Hosting (cPanel)

Shared hosting umumnya memiliki struktur `public_html`. Untuk keamanan terbaik, letakkan file inti Laravel di **luar** `public_html` agar file `.env` dan database tidak dapat diakses publik.

```
/home/username/
├── poshub/              <--- Letakkan seluruh file Laravel di sini (kecuali folder public)
│   ├── app/
│   ├── config/
│   ├── database/
│   ├── .env
│   └── ...
└── public_html/         <--- Letakkan ISI folder public Laravel di sini
    ├── index.php
    ├── css/
    ├── js/
    ├── assets/
    └── .htaccess
```

### Langkah 1: Pilih Versi PHP & Ekstensi di cPanel
1. Masuk ke **cPanel** $\rightarrow$ buka menu **Select PHP Version**.
2. Pilih **PHP 8.1** (atau PHP 8.0).
3. Pastikan ekstensi berikut tercentang: `bcmath`, `fileinfo`, `gd`, `intl`, `mbstring`, `mysqli`, `openssl`, `pdo_mysql`, `zip`.

### Langkah 2: Buat Basis Data MySQL di cPanel
1. Buka menu **MySQL Databases**.
2. Buat database baru: contoh `usercpanel_poshub`.
3. Buat user database baru: contoh `usercpanel_posuser` dan password-nya.
4. Hubungkan user ke database dengan mencentang **ALL PRIVILEGES**.

### Langkah 3: Unggah & Ekstrak File
1. Kompres seluruh proyek menjadi file `poshub.zip`.
2. Buka **File Manager** cPanel.
3. Buat folder baru di root `/home/username/poshub/`.
4. Unggah `poshub.zip` ke dalam folder `/home/username/poshub/` lalu ekstrak.
5. Pindahkan seluruh isi dari `/home/username/poshub/public/` ke dalam folder `/home/username/public_html/`.

### Langkah 4: Sesuaikan File `public_html/index.php`
Buka dan edit file `/home/username/public_html/index.php` menggunakan code editor cPanel:
Ubah baris berikut:
```php
// Sebelum:
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';

// Sesuaikan menjadi:
require __DIR__.'/../poshub/vendor/autoload.php';
$app = require_once __DIR__.'/../poshub/bootstrap/app.php';
```

### Langkah 5: Konfigurasi File `.env`
Buka file `/home/username/poshub/.env` dan sesuaikan:
```ini
APP_NAME="POSHUB ACCOUNTING"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://namadomainanda.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=usercpanel_poshub
DB_USERNAME=usercpanel_posuser
DB_PASSWORD=PasswordDatabaseAnda
```

### Langkah 6: Impor Basis Data & Storage Symlink
- **Opsi Terminal cPanel (Jika tersedia SSH/Terminal)**:
  ```bash
  cd /home/username/poshub
  php artisan migrate --seed --force
  php artisan storage:link
  ```
- **Opsi phpMyAdmin (Jika tanpa SSH)**:
  1. Buat file bridge PHP sementara di `public_html/setup-artisan.php`:
     ```php
     <?php
     require __DIR__.'/../poshub/vendor/autoload.php';
     $app = require_once __DIR__.'/../poshub/bootstrap/app.php';
     $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
     $kernel->call('migrate', ['--seed' => true, '--force' => true]);
     $kernel->call('storage:link');
     echo "Migrasi & Symlink Sukses!";
     unlink(__FILE__); // Otomatis hapus file setelah dijalankan
     ```
  2. Buka URL `https://namadomainanda.com/setup-artisan.php` pada browser sekali saja.

---

## 4. Konfigurasi Background Queue & Task Scheduler

### A. Supervisor (Untuk VPS)
Supervisor memastikan proses pengiriman WhatsApp digital receipt, laporan shift Z-report, dan background jobs tetap berjalan otomatis tanpa henti.

Buat file konfigurasi supervisor:
```bash
sudo nano /etc/supervisor/conf.d/poshub-worker.conf
```
Isi dengan:
```ini
[program:poshub-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/poshub/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/poshub/storage/logs/worker.log
stopwaitsecs=3600
```
Aktifkan Supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start poshub-worker:*
```

### B. Cron Job Scheduler (VPS & cPanel)
Jadwalkan cron job untuk pembersihan otomatis backup lama (>30 hari) dan auto-reorder PO:

* **Di VPS (`sudo crontab -u www-data -e`)**:
  ```cron
  * * * * * cd /var/www/poshub && php artisan schedule:run >> /dev/null 2>&1
  ```
* **Di cPanel (Menu *Cron Jobs*)**:
  Pilih interval **Once Per Minute (`* * * * *`)** dan masukkan command:
  ```bash
  /usr/local/bin/php /home/username/poshub/artisan schedule:run >> /dev/null 2>&1
  ```

---

## 5. Optimasi Performa & Keamanan Produksi

Jalankan perintah optimasi berikut di server produksi:
```bash
# 1. Cache Konfigurasi & Rute
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 2. Optimasi Autoloader Composer
composer dump-autoload --optimize --no-dev --classmap-authoritative
```

### Tips Keamanan Tambahan:
1. Pastikan `APP_DEBUG=false` pada `.env` agar pesan error detail dan kredensial database tidak muncul ke publik saat terjadi kesalahan.
2. Pastikan file `.env` memiliki permission `600` atau `640` (`chmod 600 .env`).
3. Manfaatkan fitur **Backup Database** bawaan di menu *Pengaturan $\rightarrow$ Backup Database* untuk mengunduh snapshot basis data secara berkala.

---

## 6. Troubleshooting & Solusi Masalah Umum

### ❓ Masalah 1: Halaman Putih Kosong (*500 Internal Server Error*)
* **Penyebab**: Permission folder `storage` atau `bootstrap/cache` belum diberikan akses tulis.
* **Solusi**:
  ```bash
  chmod -R 775 storage bootstrap/cache
  chown -R www-data:www-data storage bootstrap/cache
  ```

### ❓ Masalah 2: Gambar Logo / Foto Produk Tidak Muncul (*404 Not Found*)
* **Penyebab**: Symlink storage belum terpasang atau terputus.
* **Solusi**:
  ```bash
  php artisan storage:link
  ```

### ❓ Masalah 3: Pesan *"CORS Error"* atau *"CSRF Token Mismatch"*
* **Penyebab**: `APP_URL` di file `.env` belum menggunakan awalan `https://` yang sesuai dengan domain aktif.
* **Solusi**: Perbarui `APP_URL=https://poshub.domainanda.com` lalu jalankan `php artisan config:clear && php artisan config:cache`.

---

🎉 **Selamat! POSHUB ACCOUNTING Enterprise Edition kini telah sukses dideploy dan siap digunakan dengan performa maksimal!**
