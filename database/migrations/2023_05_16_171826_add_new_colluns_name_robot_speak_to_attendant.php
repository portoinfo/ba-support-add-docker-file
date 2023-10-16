<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddNewCollunsNameRobotSpeakToAttendant extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            DB::statement("ALTER TABLE `company_faq_robot_info` ADD COLUMN `talk_to_attendant` VARCHAR(255) NULL AFTER `change_tools_keywords`;");
            DB::statement("ALTER TABLE `company_faq_robot_info` ADD COLUMN `name_robot` VARCHAR(255) NULL AFTER `change_tools_keywords`;");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
