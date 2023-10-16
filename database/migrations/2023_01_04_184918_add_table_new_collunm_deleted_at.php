<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddTableNewCollunmDeletedAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("ALTER TABLE `category`
            ADD COLUMN `deleted_at` TIMESTAMP NULL DEFAULT NULL AFTER `created_at`,
            ADD COLUMN `deleted_by` INT(11) NULL DEFAULT NULL AFTER `created_by`;
        ");
        // Schema::table('category', function (Blueprint $table) {
        //     $table->timestamp('deleted_at')->nullable()->after('created_at');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('category', function (Blueprint $table) {
            $table->dropColumn(['deleted_at']);
        });
    }
}
