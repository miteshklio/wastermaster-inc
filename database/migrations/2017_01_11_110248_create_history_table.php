<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('lead_id');
            $table->integer('hauler_id');
            $table->string('type'); // For referencing history item type, like post_bid_match, etc.
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
        Schema::dropTable('history');
    }
}
