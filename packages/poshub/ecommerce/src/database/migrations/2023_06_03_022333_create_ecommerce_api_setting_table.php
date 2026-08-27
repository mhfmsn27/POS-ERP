<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEcommerceApiSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ecommerce_api_settings', function (Blueprint $table) {
            $table->id();
            $table->string("rajaongkir")->nullable();
            $table->string("merchant_id")->nullable();
            $table->string("client_key")->nullable();
            $table->string("server_key")->nullable();
            $table->enum('ecommerce_activation', ['yes', 'no'])->default('no');
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
        Schema::dropIfExists('ecommerce_api_setting');
    }
}
