<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AttGetTotalWorkingTimesTicket extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
        DROP function IF EXISTS `get_total_working_times_ticket`;
        CREATE DEFINER=CURRENT_USER FUNCTION `get_total_working_times_ticket`(_id INT) RETURNS int(11)
        BEGIN
            SET @total = COALESCE((SELECT SUM(TIMESTAMPDIFF(SECOND, initial_date, COALESCE(final_date, NOW())))
            FROM chat_working_times
            WHERE ticket_id = _id), 0);
        RETURN @total;
        END;
    

        DROP function IF EXISTS `get_total_working_times_chat`;
        CREATE DEFINER=CURRENT_USER FUNCTION `get_total_working_times_chat`(_id INT) RETURNS int(11)
        BEGIN
            SET @total = COALESCE((SELECT SUM(TIMESTAMPDIFF(SECOND, initial_date, COALESCE(final_date, NOW())))
            FROM chat_working_times 
            WHERE chat_id = _id), 0);
        RETURN @total;
        END;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('get_total_working_times_chat');
        Schema::dropIfExists('get_total_working_times_ticket');
    }
}
