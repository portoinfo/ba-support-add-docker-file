<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AttColumnInformationToTurnIntoTicketSetTypeText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "ALTER TABLE `chat` CHANGE COLUMN `information_to_turn_into_ticket` `information_to_turn_into_ticket` TEXT NULL ;";
        DB::unprepared($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = "ALTER TABLE `chat` CHANGE COLUMN `information_to_turn_into_ticket` `information_to_turn_into_ticket` VARCHAR(255) NULL";
        DB::unprepared($sql);
    }
}
