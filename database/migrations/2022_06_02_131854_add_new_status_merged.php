<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddNewStatusMerged extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            DB::statement("ALTER TABLE `ticket` CHANGE COLUMN `status` `status` 
            ENUM('OPENED','IN_PROGRESS','CLOSED','RESOLVED','CANCELED','MERGED') 
            CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' NULL DEFAULT NULL;");
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
            DB::statement("ALTER TABLE `ticket` CHANGE COLUMN `status` `status` 
            ENUM('OPENED','IN_PROGRESS','CLOSED','RESOLVED','CANCELED') 
            CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' NULL DEFAULT NULL;");
        });
    }
}
