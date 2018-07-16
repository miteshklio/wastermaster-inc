<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AdditionalDumpster extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->integer('msw2_qty')->nullable();
            $table->integer('msw2_yards')->nullable();
            $table->integer('msw2_per_week')->nullable();
            $table->integer('rec2_qty')->nullable();
            $table->integer('rec2_yards')->nullable();
            $table->integer('rec2_per_week')->nullable();
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->integer('msw2_qty')->nullable();
            $table->integer('msw2_yards')->nullable();
            $table->integer('msw2_per_week')->nullable();
            $table->integer('rec2_qty')->nullable();
            $table->integer('rec2_yards')->nullable();
            $table->integer('rec2_per_week')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('msw2_qty');
            $table->dropColumn('msw2_yards');
            $table->dropColumn('msw2_per_week');
            $table->dropColumn('rec2_qty');
            $table->dropColumn('rec2_yards');
            $table->dropColumn('rec2_per_week');
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('msw2_qty');
            $table->dropColumn('msw2_yards');
            $table->dropColumn('msw2_per_week');
            $table->dropColumn('rec2_qty');
            $table->dropColumn('rec2_yards');
            $table->dropColumn('rec2_per_week');
        });
    }
}
