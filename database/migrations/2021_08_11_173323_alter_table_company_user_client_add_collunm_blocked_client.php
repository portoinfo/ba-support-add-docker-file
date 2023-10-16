<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableCompanyUserClientAddCollunmBlockedClient extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('blacklist_user', function (Blueprint $table) {
            $table->text('reason')->after('type')->nullable(true);
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('blacklist_user', function (Blueprint $table) {
            $table->dropColumn(['reason']);
        });
    }
}
