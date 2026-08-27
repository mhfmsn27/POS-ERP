<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStrategicEnterpriseTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Tabel Log Mutasi Rekening Bank Impor
        if (!Schema::hasTable('bank_statement_logs')) {
            Schema::create('bank_statement_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->nullable()->index();
                $table->unsignedBigInteger('account_id')->nullable()->index(); // Akun Kas Bank di COA
                $table->string('bank_name', 50)->default('BCA');
                $table->date('transaction_date')->index();
                $table->string('description', 255)->nullable();
                $table->enum('type', ['CR', 'DB'])->default('CR'); // CR = Uang Masuk, DB = Uang Keluar
                $table->decimal('amount', 15, 2)->default(0);
                $table->decimal('balance_after', 15, 2)->default(0);
                $table->enum('status', ['unmatched', 'matched', 'ignored'])->default('unmatched')->index();
                $table->unsignedBigInteger('matched_transaction_id')->nullable()->index();
                $table->string('matched_notes', 255)->nullable();
                $table->unsignedBigInteger('imported_by')->nullable();
                $table->timestamps();

                $table->index(['store_id', 'status', 'transaction_date'], 'idx_bank_recon_lookup');
            });
        }

        // 2. Tabel Transaksi Pembayaran QRIS Dinamis
        if (!Schema::hasTable('qris_payment_transactions')) {
            Schema::create('qris_payment_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->index();
                $table->unsignedBigInteger('transaction_id')->nullable()->index();
                $table->string('invoice_number', 64)->unique();
                $table->decimal('amount', 15, 2)->default(0);
                $table->text('qris_string')->nullable();
                $table->string('qris_image_url', 255)->nullable();
                $table->enum('status', ['pending', 'paid', 'expired', 'failed'])->default('pending')->index();
                $table->string('payment_provider', 50)->default('dynamic_qris');
                $table->string('external_reference', 100)->nullable()->index();
                $table->timestamp('paid_at')->nullable();
                $table->timestamp('expired_at')->nullable();
                $table->json('callback_payload')->nullable();
                $table->timestamps();
            });
        }

        // 3. Tabel Tiket Pesanan Layar Dapur (Kitchen Display System - KDS)
        if (!Schema::hasTable('kitchen_order_tickets')) {
            Schema::create('kitchen_order_tickets', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->index();
                $table->unsignedBigInteger('transaction_id')->index();
                $table->string('ticket_number', 50)->index();
                $table->string('table_number', 50)->nullable();
                $table->string('customer_name', 100)->nullable();
                $table->string('station', 50)->default('kitchen')->index(); // 'kitchen', 'bar', 'grill', 'dessert'
                $table->json('items_payload');
                $table->enum('status', ['pending', 'cooking', 'ready', 'served', 'cancelled'])->default('pending')->index();
                $table->string('notes', 255)->nullable();
                $table->timestamp('started_cooking_at')->nullable();
                $table->timestamp('ready_at')->nullable();
                $table->timestamp('served_at')->nullable();
                $table->timestamps();

                $table->index(['store_id', 'station', 'status'], 'idx_kds_store_station_status');
            });
        }

        // 4. Tabel Riwayat Export DJP E-Faktur
        if (!Schema::hasTable('efaktur_export_logs')) {
            Schema::create('efaktur_export_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->nullable()->index();
                $table->string('tax_period', 7)->index(); // YYYY-MM
                $table->integer('total_invoices')->default(0);
                $table->decimal('total_dpp', 15, 2)->default(0);
                $table->decimal('total_ppn', 15, 2)->default(0);
                $table->string('file_name', 255)->nullable();
                $table->unsignedBigInteger('exported_by')->nullable();
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
        Schema::dropIfExists('efaktur_export_logs');
        Schema::dropIfExists('kitchen_order_tickets');
        Schema::dropIfExists('qris_payment_transactions');
        Schema::dropIfExists('bank_statement_logs');
    }
}
