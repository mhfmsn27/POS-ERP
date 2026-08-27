<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryLogStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history_log_stocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('variation_id')->index();
            $table->enum('type_product', ['inventory', 'non_inventory'])->default('inventory');
            $table->string('type');
            $table->decimal('qty',22,4)->default(0);
            $table->unsignedBigInteger('transaction_id')->index();
            $table->unsignedBigInteger('item_id')->index()->nullable();
            $table->unsignedBigInteger('retun_id')->index()->nullable();
            $table->unsignedBigInteger('unit_id')->index()->nullable();
            $table->decimal('from',22,4)->default(0);
            $table->decimal('to',22,4)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('history_log_stocks');
    }
}
