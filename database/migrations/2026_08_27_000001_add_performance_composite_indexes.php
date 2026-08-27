<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPerformanceCompositeIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Composite Index pada account_transactions
        if (Schema::hasTable('account_transactions')) {
            Schema::table('account_transactions', function (Blueprint $table) {
                $table->index(['account_id', 'operation_date', 'type'], 'idx_actx_acc_date_type');
                $table->index(['account_id', 'type', 'operation_date'], 'idx_actx_acc_type_date');
                $table->index(['account_id', 'after_rekonsiliasi', 'amount', 'type'], 'idx_actx_rekon_lookup');
                $table->index(['transaction_id', 'sub_type'], 'idx_actx_tx_subtype');
                $table->index(['ref_no'], 'idx_actx_ref_no');
            });
        }

        // 2. Composite Index pada transactions
        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->index(['store_id', 'type', 'transaction_date'], 'idx_tx_store_type_date');
                $table->index(['customer_id', 'payment_status', 'transaction_date'], 'idx_tx_cust_status_date');
                $table->index(['supplier_id', 'payment_status', 'transaction_date'], 'idx_tx_supp_status_date');
                $table->index(['type', 'status', 'payment_status'], 'idx_tx_type_status_pay');
            });
        }

        // 3. Composite Index pada stocks
        if (Schema::hasTable('stocks')) {
            Schema::table('stocks', function (Blueprint $table) {
                $table->index(['store_id', 'variation_id', 'qty_available'], 'idx_stk_store_var_qty');
                $table->index(['product_id', 'store_id'], 'idx_stk_prod_store');
            });
        }

        // 4. Composite Index pada history_log_stocks
        if (Schema::hasTable('history_log_stocks')) {
            Schema::table('history_log_stocks', function (Blueprint $table) {
                $table->index(['product_id', 'variation_id', 'created_at'], 'idx_hls_prod_var_created');
                $table->index(['transaction_id', 'type'], 'idx_hls_tx_type');
            });
        }

        // 5. Composite Index pada sells
        if (Schema::hasTable('sells')) {
            Schema::table('sells', function (Blueprint $table) {
                $table->index(['transaction_id', 'variation_id'], 'idx_sells_tx_var');
                $table->index(['store_id', 'product_id'], 'idx_sells_store_prod');
            });
        }

        // 6. Composite Index pada purchases
        if (Schema::hasTable('purchases')) {
            Schema::table('purchases', function (Blueprint $table) {
                $table->index(['transaction_id', 'variation_id'], 'idx_purch_tx_var');
                $table->index(['store_id', 'product_id'], 'idx_purch_store_prod');
            });
        }

        // 7. Composite Index pada rekonsiliasi_data
        if (Schema::hasTable('rekonsiliasi_data')) {
            Schema::table('rekonsiliasi_data', function (Blueprint $table) {
                $table->index(['account_id', 'status', 'type', 'amount'], 'idx_rekon_acc_stat_type_amt');
                $table->index(['status', 'date'], 'idx_rekon_status_date');
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
        if (Schema::hasTable('account_transactions')) {
            Schema::table('account_transactions', function (Blueprint $table) {
                $table->dropIndex('idx_actx_acc_date_type');
                $table->dropIndex('idx_actx_acc_type_date');
                $table->dropIndex('idx_actx_rekon_lookup');
                $table->dropIndex('idx_actx_tx_subtype');
                $table->dropIndex('idx_actx_ref_no');
            });
        }

        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropIndex('idx_tx_store_type_date');
                $table->dropIndex('idx_tx_cust_status_date');
                $table->dropIndex('idx_tx_supp_status_date');
                $table->dropIndex('idx_tx_type_status_pay');
            });
        }

        if (Schema::hasTable('stocks')) {
            Schema::table('stocks', function (Blueprint $table) {
                $table->dropIndex('idx_stk_store_var_qty');
                $table->dropIndex('idx_stk_prod_store');
            });
        }

        if (Schema::hasTable('history_log_stocks')) {
            Schema::table('history_log_stocks', function (Blueprint $table) {
                $table->dropIndex('idx_hls_prod_var_created');
                $table->dropIndex('idx_hls_tx_type');
            });
        }

        if (Schema::hasTable('sells')) {
            Schema::table('sells', function (Blueprint $table) {
                $table->dropIndex('idx_sells_tx_var');
                $table->dropIndex('idx_sells_store_prod');
            });
        }

        if (Schema::hasTable('purchases')) {
            Schema::table('purchases', function (Blueprint $table) {
                $table->dropIndex('idx_purch_tx_var');
                $table->dropIndex('idx_purch_store_prod');
            });
        }

        if (Schema::hasTable('rekonsiliasi_data')) {
            Schema::table('rekonsiliasi_data', function (Blueprint $table) {
                $table->dropIndex('idx_rekon_acc_stat_type_amt');
                $table->dropIndex('idx_rekon_status_date');
            });
        }
    }
}
