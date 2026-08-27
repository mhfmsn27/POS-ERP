<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEcommerceEnterpriseAddonsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Tabel Reservasi Stok E-Commerce (Anti-Overselling Flash Sale)
        if (!Schema::hasTable('ecommerce_stock_reservations')) {
            Schema::create('ecommerce_stock_reservations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->index();
                $table->unsignedBigInteger('transaction_id')->index();
                $table->unsignedBigInteger('product_id')->index();
                $table->unsignedBigInteger('variation_id')->index();
                $table->integer('quantity')->default(1);
                $table->enum('status', ['held', 'committed', 'released'])->default('held')->index();
                $table->timestamp('expires_at')->nullable()->index();
                $table->timestamps();

                $table->index(['store_id', 'product_id', 'status'], 'idx_ecom_res_store_prod');
            });
        }

        // 2. Tabel Promo Flash Sale & Countdown Timer
        if (!Schema::hasTable('ecommerce_flash_sales')) {
            Schema::create('ecommerce_flash_sales', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->index();
                $table->unsignedBigInteger('product_id')->index();
                $table->unsignedBigInteger('variation_id')->nullable()->index();
                $table->string('name', 100)->default('Flash Sale Spesial');
                $table->decimal('original_price', 15, 2)->default(0);
                $table->decimal('flash_price', 15, 2)->default(0);
                $table->integer('quota_total')->default(100);
                $table->integer('quota_sold')->default(0);
                $table->timestamp('start_time')->nullable()->index();
                $table->timestamp('end_time')->nullable()->index();
                $table->boolean('is_active')->default(true)->index();
                $table->timestamps();

                $table->index(['store_id', 'is_active', 'start_time', 'end_time'], 'idx_ecom_flash_active_period');
            });
        }

        // 3. Tabel Pelacakan Keranjang Tertinggal (Abandoned Cart via WhatsApp)
        if (!Schema::hasTable('abandoned_cart_logs')) {
            Schema::create('abandoned_cart_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->index();
                $table->unsignedBigInteger('customer_id')->nullable()->index();
                $table->string('customer_phone', 30)->nullable()->index();
                $table->string('customer_name', 100)->nullable();
                $table->json('cart_payload');
                $table->decimal('total_amount', 15, 2)->default(0);
                $table->enum('status', ['pending', 'notified', 'recovered', 'expired'])->default('pending')->index();
                $table->timestamp('wa_sent_at')->nullable();
                $table->timestamps();
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
        Schema::dropIfExists('abandoned_cart_logs');
        Schema::dropIfExists('ecommerce_flash_sales');
        Schema::dropIfExists('ecommerce_stock_reservations');
    }
}
