<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProDashboardDepartmentTotalsAndPercentages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "
        CREATE DEFINER=CURRENT_USER PROCEDURE `pro_dashboard_department_totals_and_percentages` (IN pIdCompany INT, IN pIdDepartment INT)
        BEGIN
            DECLARE v_id_department INTEGER;
            SET v_id_department = COALESCE(pIdDepartment, 0);
                
            SELECT
                `type`, 
                count, 
                IFNULL( ROUND((in_progress * 100 / (in_progress + opened))), 0) as perc_em_atendimento,
                IFNULL( ROUND((closed * 100 / count)), 0) as perc_fechados
            FROM (
                SELECT
                    'Chat' AS `type`,
                    COUNT(id) AS count,
                    SUM(IF(status IN ('CLOSED', 'RESOLVED'), 1, 0)) AS closed,
                    SUM(IF(status = 'OPENED', 1, 0)) AS opened,
                    SUM(IF(status = 'IN_PROGRESS', 1, 0)) AS in_progress
                FROM chat
                WHERE company_id = pIdCompany AND deleted_at IS NULL
                AND status != 'CANCELED'
                AND (v_id_department = 0 OR company_department_id = v_id_department)
        
                UNION
                
                SELECT
                    'Ticket' AS `type`,
                    COUNT(id) AS count,
                    SUM(IF(status IN ('CLOSED', 'RESOLVED'), 1, 0)) AS closed,
                    SUM(IF(status = 'OPENED', 1, 0)) AS opened,
                    SUM(IF(status = 'IN_PROGRESS', 1, 0)) AS in_progress
                FROM ticket
                WHERE company_id = pIdCompany AND deleted_at IS NULL
                AND status != 'CANCELED'
                AND (v_id_department = 0 OR company_department_id = v_id_department)
            ) sub
            GROUP BY 1;
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
        DB::unprepared('DROP procedure IF EXISTS `pro_dashboard_department_totals_and_percentages`;');
    }
}
