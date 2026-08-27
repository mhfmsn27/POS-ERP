<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterEcommerceSettingSosmed extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ecommerce_api_settings', function (Blueprint $table) {
            $table->string('facebook_url')->nullable()->after('server_key');
            $table->string('twitter_url')->nullable()->after('facebook_url');
            $table->string('instagram_url')->nullable()->after('twitter_url');
            $table->string('youtube_url')->nullable()->after('instagram_url');
            $table->string("copyright")->nullable()->after('youtube_url');
            $table->string("about_title")->nullable()->after('copyright');
            $table->string("about_image")->nullable()->after('about_title');
            $table->longText("about_text")->nullable()->after('about_image'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('ecommerce_api_settings', function (Blueprint $table) {
            $table->dropColumn('facebook_url');
            $table->dropColumn('twitter_url');
            $table->dropColumn('instagram_url');
            $table->dropColumn('youtube_url');
            $table->dropColumn('copyright');
            $table->dropColumn('about_title');
            $table->dropColumn('about_image');
            $table->dropColumn('about_text');
        });
    }
}
