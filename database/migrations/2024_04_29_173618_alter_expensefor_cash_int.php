<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterExpenseforCashInt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { 
        Schema::table('expenses', function (Blueprint $table) {
            $table->string('type')->nullable()->change();
            $table->string('refund')->default('no')->change();
        });

        Schema::table('account_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('expense_id')->index()->nullable()->after('transaction_id');
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
