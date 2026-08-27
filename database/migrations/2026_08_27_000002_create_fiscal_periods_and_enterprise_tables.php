<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFiscalPeriodsAndEnterpriseTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Tabel Periode Akuntansi / Fiscal Periods
        if (!Schema::hasTable('fiscal_periods')) {
            Schema::create('fiscal_periods', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->nullable()->index();
                $table->string('name', 100); // e.g. "Januari 2026"
                $table->date('start_date')->index();
                $table->date('end_date')->index();
                $table->enum('status', ['open', 'locked', 'closed'])->default('open')->index();
                $table->unsignedBigInteger('closed_by')->nullable();
                $table->dateTime('closed_at')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['store_id', 'status', 'start_date', 'end_date'], 'idx_fiscal_store_status_dates');
            });
        }

        // 2. Kolom Selisih Kas pada shift_registers
        if (Schema::hasTable('shift_registers')) {
            Schema::table('shift_registers', function (Blueprint $table) {
                if (!Schema::hasColumn('shift_registers', 'physical_cash_count')) {
                    $table->decimal('physical_cash_count', 15, 2)->default(0)->after('close_amount');
                }
                if (!Schema::hasColumn('shift_registers', 'expected_cash_amount')) {
                    $table->decimal('expected_cash_amount', 15, 2)->default(0)->after('physical_cash_count');
                }
                if (!Schema::hasColumn('shift_registers', 'cash_difference')) {
                    $table->decimal('cash_difference', 15, 2)->default(0)->after('expected_cash_amount');
                }
                if (!Schema::hasColumn('shift_registers', 'closing_notes')) {
                    $table->text('closing_notes')->nullable()->after('cash_difference');
                }
            });
        }

        // 3. Tabel Mutasi Stok Antar Cabang / In-Transit Inter-Store Transfers
        if (!Schema::hasTable('store_transfers')) {
            Schema::create('store_transfers', function (Blueprint $table) {
                $table->id();
                $table->string('ref_no', 50)->unique();
                $table->unsignedBigInteger('from_store_id')->index();
                $table->unsignedBigInteger('to_store_id')->index();
                $table->enum('status', ['draft', 'pending', 'in_transit', 'received', 'cancelled'])->default('draft')->index();
                $table->decimal('total_qty_sent', 12, 2)->default(0);
                $table->decimal('total_qty_received', 12, 2)->default(0);
                $table->decimal('discrepancy_qty', 12, 2)->default(0);
                $table->text('discrepancy_notes')->nullable();
                $table->unsignedBigInteger('sent_by')->nullable();
                $table->unsignedBigInteger('received_by')->nullable();
                $table->dateTime('sent_at')->nullable();
                $table->dateTime('received_at')->nullable();
                $table->timestamps();

                $table->index(['from_store_id', 'status'], 'idx_transfer_from_status');
                $table->index(['to_store_id', 'status'], 'idx_transfer_to_status');
            });
        }

        if (!Schema::hasTable('store_transfer_items')) {
            Schema::create('store_transfer_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('transfer_id')->index();
                $table->unsignedBigInteger('product_id')->index();
                $table->unsignedBigInteger('variation_id')->index();
                $table->decimal('qty_sent', 12, 2)->default(0);
                $table->decimal('qty_received', 12, 2)->default(0);
                $table->decimal('qty_discrepancy', 12, 2)->default(0);
                $table->timestamps();
            });
        }

        // 4. Tabel Loyalty Points & Membership
        if (!Schema::hasTable('customer_loyalty_points')) {
            Schema::create('customer_loyalty_points', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('customer_id')->index();
                $table->unsignedBigInteger('store_id')->nullable()->index();
                $table->unsignedBigInteger('transaction_id')->nullable()->index();
                $table->integer('points_earned')->default(0);
                $table->integer('points_redeemed')->default(0);
                $table->integer('balance_after')->default(0);
                $table->enum('type', ['earn', 'redeem', 'adjust', 'expire'])->default('earn')->index();
                $table->string('notes', 255)->nullable();
                $table->timestamps();
            });
        }

        // 5. Tabel Anti-Fraud & Forensic POS Security Logs
        if (!Schema::hasTable('pos_security_audit_logs')) {
            Schema::create('pos_security_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('store_id')->nullable()->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('action', 50)->index(); // 'drawer_open_no_sale', 'cart_item_removed', 'discount_override', 'transaction_voided'
                $table->string('ref_no', 50)->nullable()->index();
                $table->json('metadata')->nullable();
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();

                $table->index(['store_id', 'action', 'created_at'], 'idx_security_store_action_date');
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
        Schema::dropIfExists('pos_security_audit_logs');
        Schema::dropIfExists('customer_loyalty_points');
        Schema::dropIfExists('store_transfer_items');
        Schema::dropIfExists('store_transfers');

        if (Schema::hasTable('shift_registers')) {
            Schema::table('shift_registers', function (Blueprint $table) {
                $columns = ['physical_cash_count', 'expected_cash_amount', 'cash_difference', 'closing_notes'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('shift_registers', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        Schema::dropIfExists('fiscal_periods');
    }
}
