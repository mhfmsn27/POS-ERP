<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCategoryEcommerce extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->enum("show_in_ecommerce", ["yes", "no"])->default("yes")->after("image");
            $table->enum("featured_category",["yes","no"])->default("no")->after("show_in_ecommerce");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('show_in_ecommerce');
            $table->dropColumn('featured_category'); 
        });
    }
}
