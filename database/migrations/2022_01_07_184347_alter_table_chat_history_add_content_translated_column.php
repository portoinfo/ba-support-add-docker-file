<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableChatHistoryAddContentTranslatedColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chat_history', function (Blueprint $table) {
            $table->text('content_translated')->nullable(true)->after('content')->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chat_history', function (Blueprint $table) {
            $table->dropColumn(['content_translated']);
        });
    }
}
