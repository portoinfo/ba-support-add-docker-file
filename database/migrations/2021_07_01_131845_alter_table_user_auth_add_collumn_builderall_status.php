<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableUserAuthAddCollumnBuilderallStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_auth', function (Blueprint $table) {
            $table->string('builderall_status', 20)->nullable()->default('ACTIVE');
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
            $table->dropColumn('builderall_status');
        });
    }
}
