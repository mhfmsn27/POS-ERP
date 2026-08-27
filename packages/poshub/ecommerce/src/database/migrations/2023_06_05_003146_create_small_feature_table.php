<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmallFeatureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('small_features', function (Blueprint $table) {
            $table->id();
            $table->string("image")->default("ecommerce/imgs/theme/icons/icon-1.svg");
            $table->enum("position", ["footer", "about"])->default("footer");
            $table->string("title");
            $table->text("subtitle")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('small_feature');
    }
}
