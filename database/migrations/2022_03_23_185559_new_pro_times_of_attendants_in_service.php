<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NewProTimesOfAttendantsInService extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
            DROP procedure IF EXISTS `pro_times_of_attendants_in_service`;
            
            CREATE DEFINER=CURRENT_USER PROCEDURE `pro_times_of_attendants_in_service`(IN pIdCompany INT, IN pIdDepartment INT, IN pIdAttendant INT, IN pFilterPeriod ENUM('LAST_24_HOURS', 'LAST_7_DAYS', 'LAST_30_DAYS', 'LAST_365_DAYS'))
            COMMENT 'Tempo total de atendimento do atendente'
            BEGIN
                DECLARE v_id_department INTEGER;
                DECLARE v_id_attendant INTEGER;
                SET v_id_department = COALESCE(pIdDepartment, 0);
                SET v_id_attendant = COALESCE(pIdAttendant, 0);

                SET @SQL_Final = 
                    CONCAT('SELECT c.update_status_in_progress, c.update_status_closed_resolved, 
                        TIME_TO_SEC(TIMEDIFF(c.update_status_closed_resolved, c.update_status_in_progress)) tempo_segundos
                            FROM chat c
                            JOIN company_user_company_department cucd ON c.comp_user_comp_depart_id_current = cucd.id
                            WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL ',
                            IF(v_id_department > 0, CONCAT(' AND c.company_department_id = ', v_id_department), ''),
                            IF(v_id_attendant > 0, CONCAT(' AND cucd.company_user_id = ', v_id_attendant), ''),
                            ' AND c.`type` IN (''DEFAULT'', ''CHANGED_TO_TICKET'')
                            AND c.status IN (''CLOSED'', ''RESOLVED'')
                            AND c.created_at >= DATE_SUB(CURDATE(), INTERVAL ', 
                                IF(pFilterPeriod = 'LAST_24_HOURS', ' 24 HOUR)', IF(pFilterPeriod = 'LAST_7_DAYS', ' 7 DAY)',
                                    IF(pFilterPeriod = 'LAST_30_DAYS', ' 30 DAY)', IF(pFilterPeriod = 'LAST_365_DAYS', ' 1 YEAR)', '')))), 
                            ' ORDER BY c.update_status_in_progress;'
                        );
                        
                PREPARE stmt FROM @SQL_Final;
                EXECUTE stmt;
                DEALLOCATE PREPARE stmt;
                
                -- select @SQL_Final;
            END;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("DROP procedure IF EXISTS `pro_times_of_attendants_in_service`;");
    }
}
