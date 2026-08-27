<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRmaTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rma_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->index();
            $table->unsignedBigInteger('store_id')->index();
            $table->string('invoice')->nullable();
            $table->string('ref_no')->nullable(); 
            $table->text('note')->nullable();
            $table->dateTime('estimate_date')->nullable();
            $table->decimal('estimate_price', 22, 4)->default(0);
            $table->decimal('price',22,4)->default(0); 
            $table->enum('status', ['pending', 'process', 'complete', 'taken'])->default('pending');
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
        Schema::dropIfExists('rma_transactions');
    }
}
