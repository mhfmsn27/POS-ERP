<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id')->index()->nullable();
            $table->enum('type',['general','personal'])->default('general');
            $table->unsignedBigInteger('user_register')->index()->nullable();
            $table->unsignedBigInteger('user_add')->index()->nullable();
            $table->unsignedBigInteger('add_store')->index()->nullable();
            $table->unsignedBigInteger('ecommerce_order')->index()->nullable();
            $table->unsignedBigInteger('ecommerce_payment')->index()->nullable();
            $table->unsignedBigInteger('ecommerce_shipping')->index()->nullable();
            $table->unsignedBigInteger('ecommerce_received')->index()->nullable();
            $table->unsignedBigInteger('rma_add')->index()->nullable();
            $table->unsignedBigInteger('rma_progress')->index()->nullable();
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
        Schema::dropIfExists('notification_settings');
    }
}
