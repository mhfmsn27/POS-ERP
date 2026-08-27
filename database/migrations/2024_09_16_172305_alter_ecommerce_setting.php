<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterEcommerceSetting extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Schema::table('ecommerce_api_settings', function (Blueprint $table) {
        //     $table->enum('show_stock',['yes','no'])->default('yes')->after('ecommerce_activation');
        //     $table->enum('with_stock',['yes','no'])->default('yes')->after('show_stock'); 
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
