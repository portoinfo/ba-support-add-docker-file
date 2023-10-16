<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProDashboardDetailsNumbersAttendant extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "
            CREATE DEFINER=CURRENT_USER PROCEDURE `pro_dashboard_details_numbers_attendant` (IN pIdCompany INT, IN pIdAttendant INT, IN pIdDepartment INT, IN pInitialDate DATETIME, IN pFinalDate DATETIME)
            COMMENT 'Searches for closed, resolved, ongoing chats/tickets and also the average queue / service time.'
            BEGIN
                DECLARE v_id_department INTEGER;
                SET v_id_department = COALESCE(pIdDepartment, 0);
            
                SELECT
                    `type`, 
                    count, 
                    -- opened,
                    in_progress,
                    closed,
                    resolved,
                    changed_to_ticket,
                    -- IFNULL(ROUND((in_progress * 100 / (in_progress + opened))), 0) as perc_em_atendimento,
                    -- IFNULL(ROUND((closed * 100 / count)), 0) as perc_fechados,
                    avg_queue_time,
                    avg_service_time
                FROM (
                    SELECT
                        'Chat' AS `type`,
                        COUNT(c.id) AS count,
                        -- SUM(IF(c.status = 'OPENED', 1, 0)) AS opened,
                        SUM(IF(c.status = 'IN_PROGRESS', 1, 0)) AS in_progress,
                        SUM(IF(c.status = 'CLOSED', 1, 0)) AS closed,
                        SUM(IF(c.status = 'RESOLVED', 1, 0)) AS resolved,
                        SUM(IF(c.status IN ('IN_PROGRESS', 'CLOSED', 'RESOLVED') AND c.type = 'CHANGED_TO_TICKET', 1, 0)) AS changed_to_ticket,
                        ROUND(AVG(IFNULL(c.queue_time, 0))) AS avg_queue_time,
                        ROUND(AVG(IFNULL(c.service_time, 0))) AS avg_service_time
                    FROM chat c
                    JOIN company_user_company_department cucd ON c.comp_user_comp_depart_id_current = cucd.id
                    JOIN company_user cu ON cucd.company_user_id = cu.id
                    JOIN user_auth u on cu.user_auth_id = u.id
                    WHERE c.company_id = pIdCompany AND c.deleted_at IS NULL
                    AND (v_id_department = 0 OR c.company_department_id = v_id_department)
                    AND cu.user_auth_id = pIdAttendant
                    AND (pInitialDate IS NULL OR c.created_at >= pInitialDate)
                    AND (pFinalDate IS NULL OR DATE(c.created_at) <= pFinalDate)
                    AND c.status != 'CANCELED'
                    AND c.ticket_id IS NULL
                ) sub_chat
                
                UNION
                
                SELECT
                    `type`, 
                    count, 
                    -- opened,
                    in_progress,
                    closed,
                    resolved,
                    changed_to_ticket,
                    -- IFNULL(ROUND((in_progress * 100 / (in_progress + opened))), 0) as perc_em_atendimento,
                    -- IFNULL(ROUND((closed * 100 / count)), 0) as perc_fechados,
                    avg_queue_time,
                    avg_service_time
                FROM (
                    SELECT
                        'Ticket' AS `type`,
                        COUNT(t.id) AS count,
                        -- SUM(IF(t.status = 'OPENED', 1, 0)) AS opened,
                        SUM(IF(t.status = 'IN_PROGRESS', 1, 0)) AS in_progress,
                        SUM(IF(t.status = 'CLOSED', 1, 0)) AS closed,
                        SUM(IF(t.status = 'RESOLVED', 1, 0)) AS resolved,
                        0 AS changed_to_ticket,
                        ROUND(AVG(IFNULL(t.queue_time, 0))) AS avg_queue_time, 
                        ROUND(AVG(IFNULL(t.service_time, 0))) AS avg_service_time
                    FROM ticket t
                    JOIN user_ticket ut on t.id = ut.ticket_id
                    JOIN company_user cu ON ut.company_user_id = cu.id
                    WHERE t.company_id = pIdCompany AND t.deleted_at IS NULL
                    AND (v_id_department = 0 OR t.company_department_id = v_id_department)
                    AND cu.user_auth_id = pIdAttendant
                    AND (pInitialDate IS NULL OR t.created_at >= pInitialDate)
                    AND (pFinalDate IS NULL OR DATE(t.created_at) <= pFinalDate)
                    AND t.status != 'CANCELED'
                ) sub_ticket;
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
        DB::unprepared("DROP procedure IF EXISTS `pro_dashboard_details_numbers_attendant`;");
    }
}
