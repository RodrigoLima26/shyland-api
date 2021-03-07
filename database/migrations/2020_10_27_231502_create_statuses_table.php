<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_player')->unsigned();
            $table->integer('level')->unsigned()->default(1);
            $table->integer('confidence')->unsigned()->default(0);
            $table->integer('conf_next_level')->unsigned()->default(35);
            $table->integer('courage')->unsigned()->default(1);
            $table->integer('inteligence')->unsigned()->default(1);
            $table->integer('friendship')->unsigned()->default(1);
            $table->integer('sociability')->unsigned()->default(1);
            $table->integer('kindness')->unsigned()->default(1);
            $table->integer('criativity')->unsigned()->default(1);
            $table->integer('intelligence')->unsigned()->default(1);
            $table->integer('gold')->unsigned()->default(500);
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
        Schema::dropIfExists('statuses');
    }
}
