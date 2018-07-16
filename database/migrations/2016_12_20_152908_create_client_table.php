<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function(Blueprint $table) {
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
            $table->decimal('prior_total')->nullable();
            $table->decimal('msw_price')->nullable();
            $table->decimal('rec_price')->nullable();
            $table->decimal('rec_offset')->nullable();
            $table->decimal('fuel_surcharge')->nullable();
            $table->decimal('env_surcharge')->nullable();
            $table->decimal('recovery_fee')->nullable();
            $table->decimal('admin_fee')->nullable();
            $table->decimal('other_fees')->nullable();
            $table->decimal('net_monthly')->nullable();
            $table->decimal('gross_profit')->nullable();
            $table->decimal('total')->nullable();

            $table->boolean('archived');
            $table->timestamps();
            $table->softDeletes();

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
        Schema::dropIfExists('clients');
    }
}
