<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNextGenEnterpriseTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Tabel Pelacakan Batch & Tanggal Kadaluarsa (FEFO)
        if (!Schema::hasTable('product_batches')) {
            Schema::create('product_batches', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id')->index();
                $table->unsignedBigInteger('variation_id')->index();
                $table->unsignedBigInteger('store_id')->nullable()->index();
                $table->string('batch_number', 50)->index();
                $table->date('manufactured_date')->nullable();
                $table->date('expiry_date')->index();
                $table->decimal('initial_qty', 12, 2)->default(0);
                $table->decimal('current_qty', 12, 2)->default(0);
                $table->decimal('cost_price', 15, 2)->default(0);
                $table->enum('status', ['active', 'near_expiry', 'expired', 'depleted'])->default('active')->index();
                $table->timestamps();

                $table->index(['store_id', 'variation_id', 'expiry_date', 'status'], 'idx_batch_fefo_lookup');
            });
        }

        // 2. Tabel Konfigurasi Auto-Reorder & Forecasting Stok
        if (!Schema::hasTable('inventory_reorder_settings')) {
            Schema::create('inventory_reorder_settings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->nullable()->index();
                $table->unsignedBigInteger('variation_id')->index();
                $table->decimal('safety_stock', 10, 2)->default(10);
                $table->integer('lead_time_days')->default(3); // Estimasi lama pengiriman supplier
                $table->decimal('min_reorder_qty', 10, 2)->default(20);
                $table->decimal('max_reorder_qty', 10, 2)->default(100);
                $table->boolean('auto_reorder_enabled')->default(true);
                $table->timestamps();

                $table->unique(['store_id', 'variation_id'], 'uq_store_variation_reorder');
            });
        }

        // 3. Tabel Konfigurasi CRMHUB OMNICHANNEL & WA Gateway
        if (!Schema::hasTable('omnichannel_gateway_settings')) {
            Schema::create('omnichannel_gateway_settings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->nullable()->index();
                $table->string('provider', 50)->default('crmhub_omnichannel'); // 'crmhub_omnichannel', 'senderwa', 'custom_webhook'
                $table->string('gateway_url', 255)->default('http://127.0.0.1:8000/api/whatsapp/send');
                $table->string('api_token', 255)->nullable();
                $table->string('instance_id', 100)->nullable();
                $table->string('sender_phone', 30)->nullable();
                $table->boolean('enable_digital_receipt')->default(true);
                $table->boolean('enable_shift_z_report_wa')->default(true);
                $table->boolean('enable_low_stock_wa')->default(true);
                $table->string('manager_phone', 30)->nullable(); // Nomor WA Owner/Manager untuk Z-Report
                $table->timestamps();
            });
        }

        // 4. Tabel State Dual Screen Customer-Facing Display
        if (!Schema::hasTable('pos_customer_display_states')) {
            Schema::create('pos_customer_display_states', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('session_token', 64)->unique();
                $table->string('status', 20)->default('idle'); // 'idle', 'scanning', 'paying', 'thank_you'
                $table->json('cart_payload')->nullable();
                $table->decimal('subtotal', 15, 2)->default(0);
                $table->decimal('discount_total', 15, 2)->default(0);
                $table->decimal('tax_total', 15, 2)->default(0);
                $table->decimal('grand_total', 15, 2)->default(0);
                $table->decimal('pay_amount', 15, 2)->default(0);
                $table->decimal('change_amount', 15, 2)->default(0);
                $table->string('banner_promo_url', 255)->nullable();
                $table->timestamps();

                $table->index(['store_id', 'status'], 'idx_display_store_status');
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
        Schema::dropIfExists('pos_customer_display_states');
        Schema::dropIfExists('omnichannel_gateway_settings');
        Schema::dropIfExists('inventory_reorder_settings');
        Schema::dropIfExists('product_batches');
    }
}
