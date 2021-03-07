<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayerMissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('player_missions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_mission')->unsigned();
            $table->integer('id_player')->unsigned();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('abandoned_at')->nullable();
            $table->timestamps();
            $table->softdeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('player_missions');
    }
}
