<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTransactionPayment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transaction_payments', function (Blueprint $table) {
            $table->string('method')->change()->default('cash');
            $table->dropColumn('card_transaction_number');
            $table->dropColumn('card_number');
            $table->dropColumn('card_type');
            $table->dropColumn('card_holder_name');
            $table->dropColumn('card_month');
            $table->dropColumn('card_year');
            $table->dropColumn('card_security');
            $table->dropColumn('cheque_number');
            $table->dropColumn('bank_account_number');
            $table->dropColumn('no_rek');
            $table->dropColumn('bank_id');
            $table->dropColumn('an');
            $table->unsignedInteger('payment_method_id')->index()->nullable()->after('method'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
