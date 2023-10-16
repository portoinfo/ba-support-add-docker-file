<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AttProTimesOfAttendantsInService extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("DROP procedure IF EXISTS `pro_times_of_attendants_in_service`;

        CREATE DEFINER=CURRENT_USER PROCEDURE `pro_times_of_attendants_in_service`(IN pType ENUM('CHAT', 'TICKET'), IN pIdCompany INT, IN pIdDepartment INT, IN pIdAttendant INT, IN pInitialDate DATETIME, IN pFinalDate DATETIME, IN pIsDebugger INT)
        COMMENT 'Tempo total de atendimento do atendente'
        BEGIN
            DECLARE v_id_department INTEGER;
            DECLARE v_id_attendant INTEGER;
            SET v_id_department = COALESCE(pIdDepartment, 0);
            SET v_id_attendant = COALESCE(pIdAttendant, 0);
        
            SET @SQL_Final = IF(pType = 'CHAT', 
                CONCAT('SELECT 
                            cwt.chat_id, cwt.initial_date, cwt.final_date,
                            COALESCE(TIME_TO_SEC(TIMEDIFF(cwt.final_date, cwt.initial_date)), 0) tempo_segundos
                        FROM chat c
                        JOIN chat_working_times cwt ON c.id = cwt.chat_id
                        JOIN company_user_company_department cucd ON cwt.company_user_company_department_id = cucd.id
                        WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL ',
                        IF(v_id_department > 0, CONCAT(' AND c.company_department_id = ', v_id_department), ''),
                        IF(v_id_attendant > 0, CONCAT(' AND cucd.company_user_id = ', v_id_attendant), ''),
                        ' AND cwt.ticket_id IS NULL AND cwt.final_date IS NOT NULL
                        AND c.created_at BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, '''', 
                        ' ORDER BY cwt.initial_date;'
                    ),
                    CONCAT('SELECT 
                            cwt.ticket_id, cwt.initial_date, cwt.final_date, 
                            TIME_TO_SEC(TIMEDIFF(cwt.final_date, cwt.initial_date)) tempo_segundos
                        FROM chat c
                        JOIN ticket t ON c.ticket_id = t.id
                        JOIN chat_working_times cwt ON c.id = cwt.chat_id
                        JOIN company_user_company_department cucd ON cwt.company_user_company_department_id = cucd.id
                        WHERE t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL ',
                        IF(v_id_department > 0, CONCAT(' AND t.company_department_id = ', v_id_department), ''),
                        IF(v_id_attendant > 0, CONCAT(' AND cucd.company_user_id = ', v_id_attendant), ''),
                        ' AND cwt.ticket_id IS NOT NULL AND cwt.final_date IS NOT NULL
                        AND t.created_at BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, '''',
                        ' ORDER BY cwt.initial_date;')
                    );
                    
            IF (pIsDebugger = 0) THEN
                PREPARE stmt FROM @SQL_Final;
                EXECUTE stmt;
                DEALLOCATE PREPARE stmt;
            ELSE
                SELECT @SQL_Final;
            END IF;
        END");
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pro_times_of_attendants_in_service');
    }
}
