<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterStoreIdEcommerce extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sliders', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->index()->after('button_url');
        });

        Schema::table('banners', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->index()->after('button_url');
        });

        Schema::table('ecommerce_api_settings', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->index()->after('price_per_km');
            $table->string('domain_site')->index()->after('store_id');
        });

        Schema::table('ecommerce_shippings', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->index()->after('status');
        });

        Schema::table('small_features', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->index()->after('subtitle');
        });

        Schema::table('blog_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->index()->after('image');
        });

        Schema::table('blogs', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->index()->after('views');
        });

        Schema::table('ecommerce_bank_transansfers', function (Blueprint $table) {
            $table->unsignedBigInteger('store_id')->index()->after('logo');
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
