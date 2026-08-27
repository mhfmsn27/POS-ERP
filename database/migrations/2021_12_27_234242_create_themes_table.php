<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThemesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('themes', function (Blueprint $table) {
            $table->id();
            $table->enum('type',["mdh_ecommerce",'website'])->default('mdh_ecommerce');
            $table->string('name');
            $table->string('path');
            $table->enum("type_themes",["free","purchase"])->default('free');
            $table->string('image')->default('uploads/image.jpg');
            $table->string('purchase_code')->nullable();
            $table->unsignedBigInteger('transaction_id')->index()->nullable();
            $table->unsignedBigInteger('author_id')->index()->nullable();
            $table->unsignedBigInteger('theme_id')->index()->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('themes');
    }
}
