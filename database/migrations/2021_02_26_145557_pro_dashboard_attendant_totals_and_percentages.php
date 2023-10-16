<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProDashboardAttendantTotalsAndPercentages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "
        CREATE DEFINER=CURRENT_USER PROCEDURE `pro_dashboard_attendant_totals_and_percentages` (IN pIdCompany INT, IN pIdDepartment INT, IN pIdAttendant INT)
        BEGIN
            DECLARE v_id_department INTEGER;
            DECLARE v_id_attendant INTEGER;
            SET v_id_department = COALESCE(pIdDepartment, 0);
            SET v_id_attendant = COALESCE(pIdAttendant, 0);
        
            SELECT
                `type`, 
                count, 
                IFNULL(ROUND((in_progress * 100 / (in_progress + opened))), 0) as perc_em_atendimento,
                IFNULL(ROUND((closed * 100 / count)), 0) as perc_fechados
            FROM (
                SELECT
                    'Chat' AS `type`,
                    COUNT(c.id) AS count,
                    SUM(IF(c.status IN ('CLOSED', 'RESOLVED'), 1, 0)) AS closed,
                    SUM(IF(c.status = 'OPENED', 1, 0)) AS opened,
                    SUM(IF(c.status = 'IN_PROGRESS', 1, 0)) AS in_progress
                FROM chat c
                JOIN company_user_company_department cucd ON c.comp_user_comp_depart_id_current = cucd.id
                JOIN company_user cu ON cucd.company_user_id = cu.id
                WHERE c.company_id = pIdCompany AND c.deleted_at IS NULL
                AND (v_id_department = 0 OR c.company_department_id = v_id_department)
                AND (v_id_attendant = 0 OR cu.user_auth_id = v_id_attendant)
                AND c.status != 'CANCELED'
        
                UNION
                
                SELECT
                    'Ticket' AS `type`,
                    COUNT(t.id) AS count,
                    SUM(IF(t.status IN ('CLOSED', 'RESOLVED'), 1, 0)) AS closed,
                    SUM(IF(t.status = 'OPENED', 1, 0)) AS opened,
                    SUM(IF(t.status = 'IN_PROGRESS', 1, 0)) AS in_progress
                FROM ticket t
                JOIN user_ticket ut on t.id = ut.ticket_id
                JOIN company_user cu ON ut.company_user_id = cu.id
                WHERE t.company_id = pIdCompany AND t.deleted_at IS NULL
                AND (v_id_department = 0 OR t.company_department_id = v_id_department)
                AND (v_id_attendant = 0 OR cu.user_auth_id = v_id_attendant)
                AND t.status != 'CANCELED'
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
        DB::unprepared("DROP procedure IF EXISTS `pro_dashboard_attendant_totals_and_percentages`;");
    }
}
