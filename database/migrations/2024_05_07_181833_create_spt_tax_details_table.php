<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSptTaxDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spt_tax_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('spt_tax_id')->index();
            $table->string('transaction_type');
            $table->string('type');
            $table->decimal('credit',22,4)->default(0);
            $table->decimal('amount',22,4)->default(0);
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
        Schema::dropIfExists('spt_tax_details');
    }
}
