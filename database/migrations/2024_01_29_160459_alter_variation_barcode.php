<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterVariationBarcode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('variations', function (Blueprint $table) {
            $table->string('barcode')->nullable()->after('sku'); 
            $table->string('name')->nullable()->change();
            $table->dropColumn('reseller');
            $table->dropColumn('margin_grocery');
            $table->dropColumn('margin');
            $table->dropColumn('image'); 
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
