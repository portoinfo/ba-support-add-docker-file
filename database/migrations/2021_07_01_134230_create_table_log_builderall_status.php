<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableLogBuilderallStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_builderall_status', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_auth_id')->nullable(false);
            $table->string('old_status', 20)->nullable(false);    
            $table->string('new_status', 20)->nullable(false);    
            $table->string('origin', 20)->nullable(false); 
            $table->timestamp('created_at')->useCurrent(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_builderall_status');
    }
}
