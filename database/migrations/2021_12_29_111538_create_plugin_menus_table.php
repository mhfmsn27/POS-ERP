<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePluginMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plugin_menus', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plugin_id')->index();
            $table->string('name');
            $table->string('route_link');
            $table->unsignedBigInteger("permission_id")->index()->nullable();
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
        Schema::dropIfExists('plugin_menus');
    }
}
