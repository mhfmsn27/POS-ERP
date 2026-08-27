<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLicensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->string("name", 100)->nullable(); 
            $table->string('business_name')->nullable();
            $table->text("purchase");
            $table->string("email");
            $table->string("ip_or_domain")->nullable();
            $table->enum("type", ["online", "offline"])->default("offline");
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string("barcode_code")->nullable();
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
        Schema::dropIfExists('licenses');
    }
}
