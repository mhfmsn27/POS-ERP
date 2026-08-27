<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFrontierEnterpriseTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Service & Appointment Booking (Salon, Spa, Klinik, Cuci Mobil)
        if (!Schema::hasTable('service_appointments')) {
            Schema::create('service_appointments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->index();
                $table->unsignedBigInteger('customer_id')->nullable()->index();
                $table->string('customer_name', 100);
                $table->string('customer_phone', 30);
                $table->unsignedBigInteger('staff_id')->nullable()->index();
                $table->string('staff_name', 100)->nullable();
                $table->string('service_name', 150);
                $table->date('appointment_date')->index();
                $table->time('start_time');
                $table->time('end_time')->nullable();
                $table->enum('status', ['booked', 'confirmed', 'in_progress', 'completed', 'cancelled', 'no_show'])->default('booked')->index();
                $table->decimal('estimated_fee', 15, 2)->default(0);
                $table->text('notes')->nullable();
                $table->timestamp('reminder_sent_at')->nullable();
                $table->timestamps();
            });
        }

        // 2. Master Produk Konsinyasi (Titip Jual Supplier)
        if (!Schema::hasTable('consignment_products')) {
            Schema::create('consignment_products', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->index();
                $table->unsignedBigInteger('product_id')->index();
                $table->unsignedBigInteger('variation_id')->nullable()->index();
                $table->unsignedBigInteger('supplier_id')->index();
                $table->string('supplier_name', 150);
                $table->decimal('consignor_share_percent', 5, 2)->default(80.00); // 80% ke pemilik barang
                $table->decimal('store_margin_percent', 5, 2)->default(20.00); // 20% margin toko
                $table->decimal('unit_consignor_cost', 15, 2)->default(0);
                $table->enum('settlement_period', ['weekly', 'monthly'])->default('monthly');
                $table->boolean('is_active')->default(true)->index();
                $table->timestamps();

                $table->unique(['store_id', 'product_id', 'supplier_id'], 'uq_consignment_prod_store');
            });
        }

        // 3. Rekap Settlement Pelunasan Konsinyasi
        if (!Schema::hasTable('consignment_settlements')) {
            Schema::create('consignment_settlements', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->index();
                $table->unsignedBigInteger('supplier_id')->index();
                $table->string('settlement_no', 50)->unique();
                $table->date('start_date');
                $table->date('end_date');
                $table->decimal('total_qty_sold', 10, 2)->default(0);
                $table->decimal('total_gross_sales', 15, 2)->default(0);
                $table->decimal('total_consignor_payable', 15, 2)->default(0);
                $table->decimal('total_store_fee', 15, 2)->default(0);
                $table->enum('status', ['draft', 'approved', 'paid'])->default('draft')->index();
                $table->timestamps();
            });
        }

        // 4. Smart Promotion Engine (Kombo, BOGO, Threshold)
        if (!Schema::hasTable('smart_promotions')) {
            Schema::create('smart_promotions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->index();
                $table->string('name', 150);
                $table->enum('promo_type', ['combo_bundle', 'bogo', 'threshold_discount'])->default('combo_bundle')->index();
                $table->json('conditions_json')->nullable();
                $table->json('rewards_json')->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->boolean('is_active')->default(true)->index();
                $table->timestamps();
            });
        }

        // 5. Log Executive Briefing WhatsApp ke Owner
        if (!Schema::hasTable('executive_briefing_logs')) {
            Schema::create('executive_briefing_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->index();
                $table->date('briefing_date')->index();
                $table->decimal('total_omzet', 15, 2)->default(0);
                $table->decimal('gross_profit', 15, 2)->default(0);
                $table->decimal('cash_inflow', 15, 2)->default(0);
                $table->decimal('qris_inflow', 15, 2)->default(0);
                $table->json('top_products_json')->nullable();
                $table->integer('anomalies_count')->default(0);
                $table->string('recipient_phone', 30);
                $table->timestamp('sent_at')->nullable();
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
        Schema::dropIfExists('executive_briefing_logs');
        Schema::dropIfExists('smart_promotions');
        Schema::dropIfExists('consignment_settlements');
        Schema::dropIfExists('consignment_products');
        Schema::dropIfExists('service_appointments');
    }
}
