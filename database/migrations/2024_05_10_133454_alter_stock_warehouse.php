<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterStockWarehouse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stocks', function (Blueprint $table) {
            $table->unsignedBigInteger('warehouse_id')->index()->after('store_id')->nullable();
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->unsignedBigInteger('warehouse_default_id')->index()->after('commission_type')->nullable();
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('warehouse_id')->index()->after('store_id')->nullable();
            $table->unsignedBigInteger('old_warehouse_id')->index()->after('warehouse_id')->nullable();
            $table->unsignedBigInteger('from_warehouse_id')->index()->after('old_warehouse_id')->nullable();
            $table->unsignedBigInteger('to_warehouse_id')->index()->after('from_warehouse_id')->nullable();
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
