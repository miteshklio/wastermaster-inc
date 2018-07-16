<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCitiesAndStates extends Migration
{
    /**
     * Uses the tab-delimited file, resources/cities.txt,
     * to import cities and states into the database that we can use.
     *
     * @return void
     */
    public function up()
    {
        // City table
        Schema::create('cities', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('state_id');
        });

        // State Table
        Schema::create('states', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('abbr');
        });

        $importer = new \WasteMaster\v1\Helpers\CityImporter();
        $importer->import('cities.txt');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cities');
        Schema::dropIfExists('states');
    }

}
