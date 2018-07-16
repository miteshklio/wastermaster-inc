<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterAmountsMakeDecimals extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function(Blueprint $table)
        {
            $table->decimal('msw_yards')->nullable()->change();
            $table->decimal('msw_per_week')->nullable()->change();
            $table->decimal('rec_yards')->nullable()->change();
            $table->decimal('rec_per_week')->nullable()->change();
        });

        Schema::table('leads', function(Blueprint $table)
        {
            $table->decimal('msw_yards')->nullable()->change();
            $table->decimal('msw_per_week')->nullable()->change();
            $table->decimal('rec_yards')->nullable()->change();
            $table->decimal('rec_per_week')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function(Blueprint $table)
        {
            $table->integer('msw_yards')->nullable()->change();
            $table->integer('msw_per_week')->nullable()->change();
            $table->integer('rec_yards')->nullable()->change();
            $table->integer('rec_per_week')->nullable()->change();
        });

        Schema::table('leads', function(Blueprint $table)
        {
            $table->integer('msw_yards')->nullable()->change();
            $table->integer('msw_per_week')->nullable()->change();
            $table->integer('rec_yards')->nullable()->change();
            $table->integer('rec_per_week')->nullable()->change();
        });
    }
}
