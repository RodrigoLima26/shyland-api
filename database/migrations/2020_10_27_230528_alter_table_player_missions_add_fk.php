<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTablePlayerMissionsAddFk extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('player_missions', function (Blueprint $table) {

            $table->foreign('id_player')->references('id')->on('players');
            $table->foreign('id_mission')->references('id')->on('missions');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('player_missions', function (Blueprint $table) {

            $table->dropForeign(['id_player']);
            $table->dropForeign(['id_mission']);

        });
    }
}
