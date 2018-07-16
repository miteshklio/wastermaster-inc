<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHaulersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('haulers', function(Blueprint $table) {
             $table->increments('id');
             $table->string('name');
             $table->integer('city_id');
             $table->boolean('svc_recycle')->default(0);
             $table->boolean('svc_waste')->default(0);
             $table->text('emails')->nullable();
             $table->boolean('archived')->default(false);
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
        Schema::dropIfExists('haulers');
    }
}
