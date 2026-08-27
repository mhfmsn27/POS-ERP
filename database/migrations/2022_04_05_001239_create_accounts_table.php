<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("store_id")->index();
            $table->unsignedBigInteger("account_type_id")->index();
            $table->unsignedBigInteger("created_by")->index();
            $table->string("name",100);
            $table->string("number", 100);
            $table->string("closed_date")->nullable();
            $table->enum("closed", ["yes", "no"])->default("no");
            $table->text("note")->nullable();
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
        Schema::dropIfExists('accounts');
    }
}
