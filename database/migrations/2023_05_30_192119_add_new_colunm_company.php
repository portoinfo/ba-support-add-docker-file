<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddNewColunmCompany extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // DB::unprepared("
        //     ALTER TABLE `emails`
        //     ADD COLUMN `name_sender` VARCHAR(250) NULL DEFAULT NULL AFTER `email`,
        //     ADD COLUMN `email_sender` VARCHAR(250) NULL DEFAULT NULL AFTER `name_sender`;
        // ");
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
