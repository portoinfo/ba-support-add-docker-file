<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateChatTableAddValueChangedToTicketToEnumType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `chat` CHANGE COLUMN `type` `type` ENUM('DEFAULT', 'TRANSFERED', 'TICKET', 'CHANGED_TO_TICKET') CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' NOT NULL DEFAULT 'DEFAULT'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `chat` CHANGE COLUMN `type` `type` ENUM('DEFAULT', 'TRANSFERED', 'TICKET') CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' NOT NULL DEFAULT 'DEFAULT'");
    }
}
