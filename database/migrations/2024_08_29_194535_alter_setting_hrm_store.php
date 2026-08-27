<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterSettingHrmStore extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings_hrms', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->after('merchant_id');
        });

        Schema::table('taxrates', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->after('merchant_id');
        });
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
