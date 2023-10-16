<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterTableTypeAndExtraData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "ALTER TABLE `ba_support`.`company_department` 
                    ADD COLUMN `type` varchar(100) NOT NULL AFTER `description`;
                ALTER TABLE `ba_support`.`chat` 
                    ADD COLUMN `extra_data` TEXT NOT NULL AFTER `user_agent`;";
        DB::unprepared($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {   
        $sql = "ALTER TABLE `ba_support`.`company_department` DROP COLUMN `type`;
                ALTER TABLE `ba_support`.`chat` DROP COLUMN `extra_data`;";
        DB::unprepared($sql);
    }
}
