<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLeadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function(Blueprint $table) {
            $table->increments('id');
            $table->string('company')->nullable();
            $table->string('address')->nullable();
            $table->integer('city_id')->default(0);
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('account_num')->nullable();
            $table->integer('hauler_id')->default(0);
            $table->integer('msw_qty')->nullable();
            $table->integer('msw_yards')->nullable();
            $table->integer('msw_per_week')->nullable();
            $table->integer('rec_qty')->nullable();
            $table->integer('rec_yards')->nullable();
            $table->integer('rec_per_week')->nullable();
            $table->integer('monthly_price')->nullable();
            $table->string('status');
            $table->boolean('archived');
            $table->integer('bid_count');
            $table->timestamps();

            $table->index('city_id');
            $table->index('hauler_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leads');
    }
}
