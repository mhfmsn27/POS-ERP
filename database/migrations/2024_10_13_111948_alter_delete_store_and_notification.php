<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterDeleteStoreAndNotification extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->string('two_factor_code')->nullable()->after('logo');
            $table->timestamp('two_factor_expires_at')->nullable()->after('two_factor_code'); 
        });   

        Schema::table('notification_settings', function (Blueprint $table) {
            $table->unsignedBigInteger('delete_store')->nullable()->after('package_buy'); 
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
