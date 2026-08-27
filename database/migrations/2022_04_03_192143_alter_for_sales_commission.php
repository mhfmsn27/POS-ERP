<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterForSalesCommission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->unsignedBigInteger("commission_contact_id")->index()->nullable()->after("tranfer_parent");
            $table->enum("commission_contact_type", ["none", "agent", "user", "employee"])->default("none")->after("commission_contact_id");
            $table->decimal("commission_contact_total", 22, 4)->default(0)->after("commission_contact_type");
        });

        Schema::table('stores', function (Blueprint $table) {
            $table->enum("commission_system", [0, 1])->default(0)->after("shift_register");
            $table->enum("commission_type", ["none", "agent", "user", "employee"])->default("none")->after("commission_system");
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->unsignedBigInteger("contact_id")->index()->nullable()->after("shift_register");
            $table->enum("contact_type", ["none", "agent", "user", "employee"])->default("none")->after("contact_id");
        });

        Schema::table('users', function (Blueprint $table) {
            $table->char("commission_percentase")->default(0)->after("timezone");
            $table->char("max_commission")->default(0)->after("commission_percentase");
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->char("commission_percentase")->default(0)->after("status");
            $table->char("max_commission")->default(0)->after("commission_percentase");
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
