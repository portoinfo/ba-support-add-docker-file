<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHiddenForClientToChatHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /**
         * ALTER TABLE `ba_support`.`chat_history` ADD COLUMN `hidden_for_client` TINYINT UNSIGNED NOT NULL DEFAULT 0 AFTER `content`;
         */
        Schema::table('chat_history', function (Blueprint $table) {
            $table->unsignedTinyInteger('hidden_for_client')->nullable(false)->default(0)->after('content');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chat_history', function (Blueprint $table) {
            $table->dropColumn('hidden_for_client');
        });
    }
}
