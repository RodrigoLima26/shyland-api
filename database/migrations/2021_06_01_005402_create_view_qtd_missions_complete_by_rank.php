<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateViewQtdMissionsCompleteByRank extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            CREATE VIEW v_qtd_missions_complete_by_rank AS
                select
	                count(pm.id) qtde_missoes,
	                m.rank
                from shyland.player_missions pm
                join shyland.missions m on m.id = pm.id_mission
                where pm.created_at > (CURDATE() - 7)
                group by m.rank;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW v_qtd_missions_complete_by_rank");
    }
}
