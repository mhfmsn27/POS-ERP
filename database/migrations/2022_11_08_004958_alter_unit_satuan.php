<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUnitSatuan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('units', function (Blueprint $table) {
            $table->enum("type", ["master", "product"])->default("master")->after("value");
            $table->unsignedBigInteger("product_id")->index()->nullable()->after("type");
            $table->unsignedBigInteger("variation_id")->index()->after("product_id")->nullable();
            $table->decimal("change_price", 22, 4)->default(0)->after("variation_id");
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
