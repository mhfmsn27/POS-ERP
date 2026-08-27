<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterCustomerAuthentication extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string("password")->nullable()->after("detail");
            $table->timestamp('email_verify')->nullable()->after('password');
            $table->string("code_verify_email")->nullable()->after('email_verify');
            $table->timestamp('verify_expire')->nullable()->after('code_verify_email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn('password');
            $table->dropColumn('email_verify');
            $table->dropColumn('code_verify_email');
            $table->dropColumn('verify_expire');
        });
    }
}
