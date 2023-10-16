<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterTableTicketChatAnswerModifyTypeText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "ALTER TABLE `ba_support`.`ticket_chat_answer` CHANGE COLUMN `answer` `answer` TEXT NOT NULL ;";
        DB::unprepared($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {   
        $sql = "ALTER TABLE `ba_support`.`ticket_chat_answer` CHANGE COLUMN `answer` `answer` VARCHAR(500) NOT NULL";
        DB::unprepared($sql);
    }
}
