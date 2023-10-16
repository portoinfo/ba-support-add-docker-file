<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProDashboardAttendantTicketsAverageTimeQueueAndService extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "
        CREATE DEFINER=CURRENT_USER PROCEDURE `pro_dashboard_attendant_tickets_average_time_queue_and_service`(IN pIdCompany INT, IN pIdDepartment INT, IN pIdAttendant INT, IN pFilterPeriod ENUM('LAST_24_HOURS', 'LAST_7_DAYS', 'LAST_30_DAYS', 'LAST_365_DAYS'))
        BEGIN
            DECLARE v_id_department INTEGER;
            DECLARE v_id_attendant INTEGER;
            SET v_id_department = COALESCE(pIdDepartment, 0);
            SET v_id_attendant = COALESCE(pIdAttendant, 0);
                
            SET @SQL_Final = 
                CONCAT('SELECT
                    ROUND(AVG(IFNULL(t.queue_time, 0))) AS avg_queue_time, 
                    ROUND(AVG(IFNULL(t.service_time, 0))) AS avg_service_time
                FROM ticket t
                JOIN user_ticket ut on t.id = ut.ticket_id
                JOIN company_user cu ON ut.company_user_id = cu.id
                WHERE t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL ',
                IF(v_id_department > 0, CONCAT(' AND t.company_department_id = ', v_id_department), ''),
                IF(v_id_attendant > 0, CONCAT(' AND cu.user_auth_id = ', v_id_attendant), ''),
                ' AND t.status != ''CANCELED''',
                IF(pFilterPeriod = 'LAST_24_HOURS', ' AND t.created_at >= DATE_SUB(CURDATE(), INTERVAL 24 HOUR);',
                    IF(pFilterPeriod = 'LAST_7_DAYS', ' AND t.created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY);',
                        IF(pFilterPeriod = 'LAST_30_DAYS', ' AND t.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY);',
                            IF(pFilterPeriod = 'LAST_365_DAYS', ' AND t.created_at >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR);', '')
                        )
                    )
                )
            );
         
            PREPARE stmt FROM @SQL_Final;
            EXECUTE stmt;
            DEALLOCATE PREPARE stmt;
            
            -- select @SQL_Final;
        END";
        DB::unprepared($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP procedure IF EXISTS `pro_dashboard_attendant_tickets_average_time_queue_and_service`;");
    }
}
