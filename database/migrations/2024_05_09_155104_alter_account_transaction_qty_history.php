<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAccountTransactionQtyHistory extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_transactions', function (Blueprint $table) {
            $table->decimal('qty_history', 22, 4)->default(0)->after('item_id');
            $table->decimal('older_amount', 22, 4)->default(0)->after('qty_history');
            $table->decimal('older_qty_history', 22, 4)->default(0)->after('older_amount');
            $table->unsignedBigInteger('adjust_account_id')->index();
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
