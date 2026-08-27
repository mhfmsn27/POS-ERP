<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionDuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_dues', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('transaction_id')->index()->nullable();
            $table->unsignedBigInteger('customer_id')->nullable()->index();
            $table->unsignedBigInteger('supplier_id')->nullable()->index();
            $table->decimal('amount', 22, 4)->default(0);
            $table->text('note')->nullable();
            $table->string('no_ref')->nullable();
            $table->string('date');
            $table->enum('status', ['due', 'paid'])->default('due');
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
        Schema::dropIfExists('transaction_dues');
    }
}
