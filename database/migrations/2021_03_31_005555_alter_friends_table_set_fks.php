<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterFriendsTableSetFks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('friends', function (Blueprint $table) {

            $table->foreign('id_player_1')->references('id')->on('players');
            $table->foreign('id_player_2')->references('id')->on('players');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('friends', function (Blueprint $table) {

            $table->dropForeign(['id_player_1']);
            $table->dropForeign(['id_player_2']);

        });
    }
}
