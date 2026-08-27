<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRmaDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rma_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rma_transactions_id')->index();
            $table->string('product_name')->nullable();
            $table->text('complaint')->nullable();
            $table->string('completeness')->nullable();
            $table->string('taken_name')->nullable();
            $table->text('note')->nullable();
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
        Schema::dropIfExists('rma_details');
    }
}
