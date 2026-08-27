<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SettingPiutangEmployee extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    { 

        Schema::table('transaction_dues', function (Blueprint $table) {
            $table->unsignedBigInteger('kasbon_id')->index()->nullable()->after('transaction_id'); 
            $table->unsignedBigInteger('salary_id')->index()->nullable()->after('kasbon_id'); 
            $table->unsignedBigInteger('employee_id')->index()->nullable()->after('supplier_id'); 
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
