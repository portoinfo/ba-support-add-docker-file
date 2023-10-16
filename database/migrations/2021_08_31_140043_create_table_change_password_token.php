<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableChangePasswordToken extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('change_password_token', function (Blueprint $table) {
            $table->id();
            $table->string('token', 100)->nullable();
            $table->unsignedBigInteger('user_auth_id');
            $table->timestamp('created_at')->useCurrent()->nullable(now());
            $table->timestamp('used_at')->useCurrent()->nullable();
            $table->timestamp('expired_at')->useCurrent()->nullable();
            $table->foreign('user_auth_id')->references('id')->on('user_auth');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('change_password_token');
    }
}
