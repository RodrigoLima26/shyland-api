<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateUsersSearchView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            CREATE VIEW v_users_search AS
                SELECT
                    u.id as user_id,
                    u.email as user_email,
                    u.id_player,
                    u.api_token,
                    u.ban_date,
                    u.created_at as user_created_at,
                    p.username,
                    p.birthdate,
                    s.level,
                    (select count(completed_at) from player_missions where p.id = player_missions.id_player) as finished_missions,
                    (select count(abandoned_at) from player_missions where p.id = player_missions.id_player) as abandoned_missions
                from users as u
                join players as p
                    on p.id = u.id_player
                join statuses as s
                    on s.id_player = p.id
                join player_missions as pm
                    on p.id = pm.id_player
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW v_users_search");
    }
}
