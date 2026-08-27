<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPurchase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->unsignedBigInteger('transaction_id')->nullable()->change();
            $table->unsignedBigInteger('transaction_received_id')->index()->nullable()->after('transaction_id');
            $table->string('publish')->nullable()->change();
            $table->string('discount_type')->nullable()->after('no_batch');
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
