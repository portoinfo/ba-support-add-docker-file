<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewCollunmChangedToTicketAt extends Migration
{
    /**
     * Run the migrations.
     *
     * ALTER TABLE `ba_support`.`chat` 
     * ADD COLUMN `change_to_ticket_at` DATETIME NULL DEFAULT NULL AFTER `type`;
     * @return void
     */
    public function up()
    {
        Schema::table('chat', function (Blueprint $table) {
            $table->datetime('changed_to_ticket_at')->after('type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chat', function (Blueprint $table) {
            $table->dropColumn('changed_to_ticket_at');
        });
    }
}
