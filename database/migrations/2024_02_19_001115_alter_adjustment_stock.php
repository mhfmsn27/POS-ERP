<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAdjustmentStock extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_adjusment_details', function (Blueprint $table) {
            $table->enum('type_adjustment', ['add', 'min'])->default('add')->after('unit_id');
            $table->decimal('stock_sistem', 22, 4)->default(0)->after('type_adjustment');
            $table->decimal('unit_qty', 22, 4)->default(0)->after('stock_sistem');
            $table->decimal('actual_stock', 22, 4)->default(0)->after('unit_qty'); 
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
