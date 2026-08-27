<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSellPurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sell_purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sell_id')->index()->nullable();
            $table->unsignedBigInteger('stock_adjustment_id')->index()->nullable();
            $table->unsignedBigInteger('purchase_id')->index()->nullable();
            $table->decimal('qty', 22, 4);
            $table->decimal('qty_return', 22, 4)->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('sell_id')->references('id')->on('sells');
            //$table->foreign('purchase_id')->references('id')->on('purchases');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sell_purchases');
    }
}
