<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketDatetimeIndexes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ticket', function (Blueprint $table) {
            $table->index('created_at', 'idx_ticket_created_at');
            $table->index('deleted_at', 'idx_ticket_deleted_at');
        });
    }

    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ticket', function (Blueprint $table) {
            $table->dropIndex('idx_ticket_created_at');
            $table->dropIndex('idx_ticket_deleted_at');
        });
    }
}
