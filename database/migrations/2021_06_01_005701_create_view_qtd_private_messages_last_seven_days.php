<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateViewQtdPrivateMessagesLastSevenDays extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            CREATE VIEW v_qtd_private_messages_last_seven_days AS
                select
	                count(n.id) qtde_notificacoes,
	                EXTRACT(DAY from n.created_at) as day,
                    EXTRACT(MONTH from n.created_at) as month,
                    EXTRACT(YEAR from n.created_at) as year
                from shyland.notifications n
                where n.sender_id is not null
	                and n.friend_request is null
                    and n.created_at > (CURDATE() - 7)
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
        DB::statement("DROP VIEW v_qtd_private_messages_last_seven_days");
    }
}
