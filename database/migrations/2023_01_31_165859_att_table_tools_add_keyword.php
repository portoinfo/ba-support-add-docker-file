<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AttTableToolsAddKeyword extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("ALTER TABLE `company_faq_robot_tools`
            ADD COLUMN `keywords` text NULL DEFAULT NULL AFTER `description`;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_faq_robot_tools', function (Blueprint $table) {
            $table->dropColumn(['keywords']);
        });
    }
}
