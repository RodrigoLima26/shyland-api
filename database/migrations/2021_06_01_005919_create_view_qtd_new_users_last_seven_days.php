<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateViewQtdNewUsersLastSevenDays extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            CREATE VIEW v_qtd_new_users_last_seven_days AS
                select
	                count(u.id) qtde_users,
	                EXTRACT(DAY from u.created_at) as day,
                    EXTRACT(MONTH from u.created_at) as month,
                    EXTRACT(YEAR from u.created_at) as year
                from shyland.users u
                where u.created_at > (CURDATE() - 7)
                group by day, month, year;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW v_qtd_new_users_last_seven_days");
    }
}
