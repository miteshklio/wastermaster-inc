<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterLeadsYardsDecimal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->decimal('msw2_yards')->default(0)->change();
            $table->decimal('msw2_per_week')->default(0)->change();
            $table->decimal('rec2_yards')->default(0)->change();
            $table->decimal('rec2_per_week')->default(0)->change();
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->decimal('msw2_yards')->default(0)->change();
            $table->decimal('msw2_per_week')->default(0)->change();
            $table->decimal('rec2_yards')->default(0)->change();
            $table->decimal('rec2_per_week')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->decimal('msw2_yards')->default(0)->change();
            $table->decimal('msw2_per_week')->default(0)->change();
            $table->decimal('rec2_yards')->default(0)->change();
            $table->decimal('rec2_per_week')->default(0)->change();
        });

        Schema::table('leads', function (Blueprint $table) {
            $table->decimal('msw2_yards')->default(0)->change();
            $table->decimal('msw2_per_week')->default(0)->change();
            $table->decimal('rec2_yards')->default(0)->change();
            $table->decimal('rec2_per_week')->default(0)->change();
        });
    }
}
