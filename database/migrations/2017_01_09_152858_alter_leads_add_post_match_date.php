<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterLeadsAddPostMatchDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leads', function(Blueprint $table) {
            $table->datetime('pre_match_sent')->nullable()->after('bid_count');
            $table->datetime('post_match_sent')->nullable()->after('bid_count');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leads', function(Blueprint $table) {
            $table->dropColumn('pre_match_sent');
            $table->dropColumn('post_match_sent');
        });
    }
}
