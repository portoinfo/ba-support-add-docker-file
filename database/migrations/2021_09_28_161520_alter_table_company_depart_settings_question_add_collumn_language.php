<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableCompanyDepartSettingsQuestionAddCollumnLanguage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_depart_settings_question', function (Blueprint $table) {
            $table->text('language')->nullable(true)->after('mandatory')->default(null);
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
            $table->dropColumn(['language']);
        });
    }
}
