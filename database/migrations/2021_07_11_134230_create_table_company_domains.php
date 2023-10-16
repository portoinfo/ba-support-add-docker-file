<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableCompanyDomains extends Migration
{
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_domains', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('company_id');
            $table->string('domain', 255)->unique();
            $table->foreign('company_id')->references('id')->on('company');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_domains');
    }
}
