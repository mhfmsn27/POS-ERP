<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterVariationTax extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('variations', function (Blueprint $table) {
            $table->unsignedBigInteger("rak_id",)->nullable()->after("image");
            $table->unsignedBigInteger('unit_id')->nullable()->after('rak_id');
            $table->enum("tax_type",["inclusive","exclusive"])->default("inclusive")->after("margin");
            $table->char("taxrate",100)->default(0)->after("tax_type");
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
