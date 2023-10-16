<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableChatHistoryModifyContentToText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "ALTER TABLE `chat_history` MODIFY `content` TEXT;";
        DB::unprepared($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = "ALTER TABLE `chat_history` MODIFY `content` VARCHAR(500);";
        DB::unprepared($sql);
    }

}
