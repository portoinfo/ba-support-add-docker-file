<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTypeCompDepartSettingQuestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_depart_settings_question', function (Blueprint $table) {
            $table->enum('type_question', ['TICKET', 'CHAT'])->after('question')->nullable('tc');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_depart_settings_question', function (Blueprint $table) {
            $table->dropColumn(['type_question']);
        });
    }
}
