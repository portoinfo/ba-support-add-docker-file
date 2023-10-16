<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCookiesAcceptedToUserAuthTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_auth', function (Blueprint $table) {
            $table->tinyInteger('cookies_accepted')->after('terms_user')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_auth', function (Blueprint $table) {
            $table->dropColumn(['cookies_accepted']);
        });
    }
}
