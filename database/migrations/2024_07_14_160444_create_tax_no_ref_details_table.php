<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaxNoRefDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tax_no_ref_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tax_no_ref_id')->index();
            $table->unsignedBigInteger('transaction_id')->index()->nullable();
            $table->string('number'); 
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
        Schema::dropIfExists('tax_no_ref_details');
    }
}
