<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceAreaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create the table itself
        Schema::create('service_area', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name')->default('Unknown');
        });

        // Add it to our Lead, Client, and Haulers.
        Schema::table('leads', function(Blueprint $table) {
            $table->integer('service_area_id')->nullable()->after('city_id');
            $table->text('address')->nullable()->change();
            $table->integer('city_id')->nullable()->change();
        });

        Schema::table('clients', function(Blueprint $table) {
            $table->integer('service_area_id')->nullable()->after('city_id');
            $table->text('address')->nullable()->change();
            $table->integer('city_id')->nullable()->change();
        });

        Schema::table('haulers', function(Blueprint $table) {
            $table->integer('service_area_id')->nullable()->after('city_id');
            $table->integer('city_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the main table
        Schema::dropIfExists('service_area');

        // Remove it from our Lead, Client, and Haulers.
        Schema::table('leads', function(Blueprint $table) {
            $table->dropColumn('service_area_id');
            $table->string('address')->change();
        });

        Schema::table('clients', function(Blueprint $table) {
            $table->dropColumn('service_area_id');
            $table->string('address')->change();
        });

        Schema::table('haulers', function(Blueprint $table) {
            $table->dropColumn('service_area_id');
        });
    }
}
