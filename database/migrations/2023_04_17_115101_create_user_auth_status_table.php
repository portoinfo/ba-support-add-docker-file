<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAuthStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_auth_status', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_auth_id');
            $table->string('status', 12)->nullable();
            $table->foreign('user_auth_id')->references('id')->on('user_auth');
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_auth_status');
    }
}
