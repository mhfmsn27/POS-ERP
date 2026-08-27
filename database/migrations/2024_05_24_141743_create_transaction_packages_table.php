<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_packages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('merchant_id')->index();
            $table->unsignedBigInteger('store_id')->index();
            $table->unsignedBigInteger('package_id')->index();
            $table->timestamp('end_date')->nullable();
            $table->string('ref_no')->nullable();
            $table->enum('status', ['pending', 'success', 'process'])->default('pending');
            $table->enum('payment_status', ['due', 'paid'])->default('due');
            $table->decimal('subtotal', 22, 4)->default(0);
            $table->decimal('tax', 22, 4)->default(0);
            $table->decimal('grand_total', 22, 4)->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transaction_packages');
    }
}
