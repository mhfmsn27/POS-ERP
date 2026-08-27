#!/bin/bash
# ==============================================================================
# POSHUB ACCOUNTING - VPS Automated 1-Click Setup Script
# Ubuntu / Debian / CentOS Linux Environment
# ==============================================================================

set -e

echo "===================================================================="
echo "🚀 Starting POSHUB ACCOUNTING Enterprise Production Setup..."
echo "===================================================================="

# 1. Pastikan file .env ada
if [ ! -f .env ]; then
    echo "📋 Copying .env.example to .env..."
    cp .env.example .env
    php artisan key:generate
fi

# 2. Set Storage Permissions
echo "🔒 Setting file and directory permissions..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# 3. Create Storage Symlink
echo "🔗 Linking public storage..."
php artisan storage:link || true

# 4. Migrate Database (All 224 Migrations)
echo "🗄️ Running database migrations (Schema Build)..."
php artisan migrate --force

# 5. Seed Enterprise Default Data
echo "🌱 Seeding default enterprise configurations..."
php artisan db:seed --force

# 6. Optimize Laravel Production Caches
echo "⚡ Optimizing routes, configs, and blade views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "===================================================================="
echo "✅ POSHUB ACCOUNTING SETUP COMPLETE & READY FOR PRODUCTION!"
echo "===================================================================="
