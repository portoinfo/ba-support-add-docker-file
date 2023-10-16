<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterTableChatHistoryAddRobotColumnType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            DB::statement("ALTER TABLE chat_history CHANGE COLUMN `type` `type` ENUM('TEXT','URL','IMAGE','EMOTICON','AUDIO','GIF','EVENT','CALL','FILE','OPEN','CLOSE','ROBOT') NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::transaction(function () {
            DB::statement("ALTER TABLE chat_history CHANGE COLUMN `type` `type` ENUM('TEXT','URL','IMAGE','EMOTICON','AUDIO','GIF','EVENT','CALL','FILE','OPEN','CLOSE','ROBOT') NOT NULL");
        });
    }
}
