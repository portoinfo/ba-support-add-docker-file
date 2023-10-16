<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProDashboardCompanyIndicators extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "
        CREATE DEFINER=CURRENT_USER PROCEDURE `pro_dashboard_company_indicators`(IN pIdCompany INT)
        BEGIN
            SELECT 'Departments' AS `type`, COUNT(id) AS Count
            FROM company_department
            WHERE company_id = pIdCompany AND deleted_at IS NULL
        
            UNION
        
            SELECT 'Groups' AS `type`, COUNT(id) AS Count
            FROM user_group
            WHERE company_id = pIdCompany AND deleted_at IS NULL
        
            UNION
        
            SELECT 'Atendents' AS `type`, COUNT(id) AS Count
            FROM company_user
            WHERE company_id = pIdCompany AND deleted_at IS NULL
        
            UNION
        
            SELECT 'Clients' AS `type`, COUNT(user_client_id) AS Count
            FROM company_user_client
            WHERE company_id = pIdCompany AND deleted_at IS NULL;
        END
        ";
        DB::unprepared($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP procedure IF EXISTS `pro_dashboard_company_indicators`;");
    }
}
