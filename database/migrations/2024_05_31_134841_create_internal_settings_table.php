<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInternalSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('internal_settings', function (Blueprint $table) {
            $table->id();
            $table->string('white_logo')->nullable();
            $table->string('dark_logo')->nullable();
            $table->decimal('tax',22,4)->default(0);
            $table->string('midtrans_key')->nullable();
            $table->string('midtrans_client')->nullable();
            $table->string('midtrans_server')->nullable();
            $table->string('whatsapp_server')->nullable();
            $table->string('whatsapp_phone')->nullable();
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
        Schema::dropIfExists('internal_settings');
    }
}
