<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewTableUpdateRobot extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_faq_robot_to_train', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_faq_robot_tool_id');
            $table->integer('last_total');
            $table->integer('total');
            $table->timestamps();
            $table->foreign('company_faq_robot_tool_id')->references('id')->on('company_faq_robot_tools');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_faq_robot_to_train');
    }
}
