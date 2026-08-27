<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSettingGrocery extends Migration
{ 
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->enum('grocery_price', ["on", "off"])->default('off')->after("mobile_version");
            $table->enum('price_edit', ["on", "off"])->default('on')->after("grocery_price");
        });
    }

     
    public function down()
    {
        //
    }
}
