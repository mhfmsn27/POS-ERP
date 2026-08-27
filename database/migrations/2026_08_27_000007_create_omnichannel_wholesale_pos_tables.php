<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOmnichannelWholesalePosTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Wholesale & Dynamic Tier Pricing
        if (!Schema::hasTable('wholesale_tier_prices')) {
            Schema::create('wholesale_tier_prices', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->index();
                $table->unsignedBigInteger('product_id')->index();
                $table->unsignedBigInteger('variation_id')->nullable()->index();
                $table->decimal('min_quantity', 10, 2)->default(1);
                $table->decimal('max_quantity', 10, 2)->nullable();
                $table->decimal('tier_price', 15, 2);
                $table->enum('customer_group', ['all', 'retail', 'reseller', 'agent', 'distributor'])->default('all')->index();
                $table->timestamps();

                $table->index(['store_id', 'product_id', 'customer_group'], 'idx_wholesale_tier_lookup');
            });
        }

        // 2. Digital Gift Cards & Prepaid Store Credit
        if (!Schema::hasTable('digital_gift_cards')) {
            Schema::create('digital_gift_cards', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->index();
                $table->string('card_code', 50)->unique();
                $table->string('pin_hash', 100);
                $table->decimal('initial_balance', 15, 2)->default(0);
                $table->decimal('current_balance', 15, 2)->default(0);
                $table->date('expires_at')->nullable()->index();
                $table->enum('status', ['active', 'used', 'expired', 'blocked'])->default('active')->index();
                $table->timestamps();
            });
        }

        // 3. Log Transaksi Gift Card
        if (!Schema::hasTable('gift_card_transactions')) {
            Schema::create('gift_card_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('card_id')->index();
                $table->unsignedBigInteger('transaction_id')->nullable()->index();
                $table->enum('type', ['redeem', 'topup'])->default('redeem');
                $table->decimal('amount', 15, 2);
                $table->decimal('balance_after', 15, 2);
                $table->timestamps();
            });
        }

        // 4. Tutup Buku & Period Lock Akuntansi
        if (!Schema::hasTable('accounting_period_closings')) {
            Schema::create('accounting_period_closings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->index();
                $table->enum('period_type', ['monthly', 'yearly'])->default('monthly');
                $table->date('period_date')->index();
                $table->unsignedBigInteger('closed_by')->index();
                $table->decimal('retained_earnings_amount', 15, 2)->default(0);
                $table->boolean('is_locked')->default(true)->index();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique(['store_id', 'period_type', 'period_date'], 'uq_store_period_closing');
            });
        }

        // 5. Audit Log Deteksi Anomali / Fraud Kasir
        if (!Schema::hasTable('cashier_fraud_audit_logs')) {
            Schema::create('cashier_fraud_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('cashier_name', 100);
                $table->string('anomaly_type', 100);
                $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium')->index();
                $table->json('details_json')->nullable();
                $table->timestamp('detected_at')->index();
                $table->timestamps();
            });
        }

        // 6. Rekomendasi Titik Pesan Ulang & EOQ AI
        if (!Schema::hasTable('inventory_reorder_recommendations')) {
            Schema::create('inventory_reorder_recommendations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->index();
                $table->unsignedBigInteger('product_id')->index();
                $table->unsignedBigInteger('variation_id')->nullable()->index();
                $table->decimal('current_stock', 10, 2)->default(0);
                $table->decimal('safety_stock', 10, 2)->default(0);
                $table->decimal('reorder_point', 10, 2)->default(0);
                $table->decimal('recommended_order_qty', 10, 2)->default(0);
                $table->unsignedBigInteger('supplier_id')->nullable()->index();
                $table->enum('status', ['pending', 'ordered', 'dismissed'])->default('pending')->index();
                $table->timestamps();
            });
        }

        // 7. Pengiriman Kurir Toko & Bukti Serah Terima (e-POD)
        if (!Schema::hasTable('delivery_dispatches')) {
            Schema::create('delivery_dispatches', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->index();
                $table->unsignedBigInteger('transaction_id')->index();
                $table->string('driver_name', 100);
                $table->string('driver_phone', 30);
                $table->enum('status', ['assigned', 'picked_up', 'in_transit', 'delivered', 'failed'])->default('assigned')->index();
                $table->text('epod_signature_url')->nullable();
                $table->text('epod_photo_url')->nullable();
                $table->string('recipient_name', 100)->nullable();
                $table->text('recipient_notes')->nullable();
                $table->timestamp('delivered_at')->nullable();
                $table->timestamps();
            });
        }

        // 8. Denah & Manajemen Meja Restoran
        if (!Schema::hasTable('restaurant_tables')) {
            Schema::create('restaurant_tables', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->index();
                $table->string('table_number', 30);
                $table->integer('capacity')->default(4);
                $table->string('zone', 50)->default('Main Hall');
                $table->enum('status', ['available', 'occupied', 'billed', 'reserved'])->default('available')->index();
                $table->unsignedBigInteger('current_transaction_id')->nullable()->index();
                $table->timestamps();

                $table->unique(['store_id', 'table_number'], 'uq_store_table_number');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restaurant_tables');
        Schema::dropIfExists('delivery_dispatches');
        Schema::dropIfExists('inventory_reorder_recommendations');
        Schema::dropIfExists('cashier_fraud_audit_logs');
        Schema::dropIfExists('accounting_period_closings');
        Schema::dropIfExists('gift_card_transactions');
        Schema::dropIfExists('digital_gift_cards');
        Schema::dropIfExists('wholesale_tier_prices');
    }
}
