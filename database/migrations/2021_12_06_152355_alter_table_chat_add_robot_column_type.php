<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterTableChatAddRobotColumnType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            DB::statement("ALTER TABLE chat CHANGE COLUMN `status` `status` ENUM('OPENED','IN_PROGRESS','CLOSED','RESOLVED','CANCELED', 'ROBOT') NOT NULL DEFAULT 'OPENED'");
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
            DB::statement("ALTER TABLE chat CHANGE COLUMN `status` `status` ENUM('OPENED','IN_PROGRESS','CLOSED','RESOLVED','CANCELED') NOT NULL DEFAULT 'OPENED'");
        });
    }
}
