<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBidsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bids', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('hauler_id')->default(0);
            $table->string('hauler_email');
            $table->integer('lead_id');
            $table->string('status');
            $table->text('notes');
            $table->decimal('msw_price')->nullable();
            $table->decimal('rec_price')->nullable();
            $table->decimal('rec_offset')->nullable();
            $table->decimal('fuel_surcharge')->nullable();
            $table->decimal('env_surcharge')->nullable();
            $table->decimal('recovery_fee')->nullable();
            $table->decimal('admin_fee')->nullable();
            $table->decimal('other_fees')->nullable();
            $table->decimal('net_monthly')->nullable();
            $table->timestamps();

            $table->index('lead_id');
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
        Schema::dropIfExists('bids');
    }
}
