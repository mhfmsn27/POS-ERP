<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProductIsStock extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->enum('is_stock', ['yes', 'no'])->default('yes')->after('weight');
            $table->dropColumn('custom_field1');
            $table->dropColumn('custom_field2');
            $table->dropColumn('custom_field3');
            $table->dropColumn('custom_field4');
            $table->dropColumn('unit_id');
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
