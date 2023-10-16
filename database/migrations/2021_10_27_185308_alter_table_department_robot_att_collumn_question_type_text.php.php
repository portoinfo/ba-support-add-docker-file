<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableDepartmentRobotAttCollumnQuestionTypeText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('department_robot', function (Blueprint $table) {
            $table->text('question')->nullable(true)->after('company_department_id')->default(null)->change();
            $table->dropForeign('fk_departmet_robot_departmet_robot_id')->change();
            $table->dropColumn('department_robot_id')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('department_robot');
    }
}
