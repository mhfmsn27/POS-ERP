<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSptTaxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spt_taxes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id')->index();
            $table->string('start_date');
            $table->string('end_date')->nullable();
            $table->string('ntpt')->nullable();
            $table->string('payment_date')->nullable();
            $table->decimal('amount',22,4)->default(0);
            $table->enum('type',['lebih','kurang'])->default('lebih');
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::table('account_transactions', function (Blueprint $table) { 
            $table->unsignedBigInteger('spt_taxes_id')->index()->nullable()->after('item_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('spt_taxes');
    }
}
