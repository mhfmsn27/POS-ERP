<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBannerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string("title")->nullable();
            $table->string("image")->default('uploads/banner/image.jpg');
            $table->enum("position", ["home", "shop", "blog", "mobile"])->default("home");
            $table->enum("button", ["yes", "no"])->default("no");
            $table->string("button_name")->nullable();
            $table->string("button_url")->nullable();
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
        Schema::dropIfExists('banner');
    }
}
