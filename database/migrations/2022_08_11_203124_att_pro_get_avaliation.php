<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AttProGetAvaliation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
        DROP procedure IF EXISTS `pro_get_avaliations_attendants`;
        CREATE DEFINER=CURRENT_USER PROCEDURE `pro_get_avaliations_attendants`(IN pType ENUM('CHAT', 'TICKET'), IN pIdCompany INT, IN pIdDepartment INT, IN pIdAttendant INT, IN pInitialDate DATETIME, IN pFinalDate DATETIME, IN pIsDebugger INT)
            COMMENT 'Busca as avaliações dos atendentes conforme os filtros informados'
        BEGIN
                DECLARE v_id_department INTEGER;
                DECLARE v_id_attendant INTEGER;
                SET v_id_department = COALESCE(pIdDepartment, 0);
                SET v_id_attendant = COALESCE(pIdAttendant, 0);
                    
                SET @SQL_Final = IF(pType = 'CHAT', 
                    CONCAT('SELECT ua2.id as attendant_id, ua2.name as attendant_name, ua.id, ua.name, a.chat_id, a.ticket_id, a.stars_atendent, 
                            a.stars_service, a.`comment` as message, cucd.company_user_id, a.created_at as hour
                        FROM avaliation a
                        JOIN user_client_chat ucc ON ucc.chat_id = a.chat_id
                        JOIN user_client uc ON uc.id = ucc.user_client_id
                        JOIN user_auth ua ON ua.id = uc.user_auth_id
                        JOIN chat c ON a.chat_id = c.id
                        JOIN company_user_company_department cucd ON c.comp_user_comp_depart_id_current = cucd.id
                        JOIN company_user cu ON cucd.company_user_id = cu.id
                        JOIN user_auth ua2 ON cu.user_auth_id = ua2.id
                        WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL ',
                        IF(v_id_department > 0, CONCAT(' AND c.company_department_id = ', v_id_department), ''),
                        IF(v_id_attendant > 0, CONCAT(' AND cucd.company_user_id = ', v_id_attendant), ''),
                        ' AND c.ticket_id IS NULL
                        AND c.created_at BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, '''', 
                        ' ORDER BY a.chat_id; '),
                    CONCAT('SELECT ua2.id as attendant_id, ua2.name as attendant_name, ua.id, ua.name, a.chat_id, a.ticket_id, a.stars_atendent, 
                            a.stars_service, a.`comment` as message, cucd.company_user_id, a.created_at as hour
                        FROM avaliation a
                        JOIN user_client_chat ucc ON ucc.chat_id = a.chat_id
                        JOIN user_client uc ON uc.id = ucc.user_client_id
                        JOIN user_auth ua ON ua.id = uc.user_auth_id
                        JOIN chat c ON a.chat_id = c.id
                        JOIN ticket t ON c.ticket_id = t.id
                        JOIN company_user_company_department cucd ON c.comp_user_comp_depart_id_current = cucd.id
                        JOIN company_user cu ON cucd.company_user_id = cu.id
                        JOIN user_auth ua2 ON cu.user_auth_id = ua2.id
                        WHERE t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL ',
                        IF(v_id_department > 0, CONCAT(' AND t.company_department_id = ', v_id_department), ''),
                        IF(v_id_attendant > 0, CONCAT(' AND cucd.company_user_id = ', v_id_attendant), ''),
                        ' AND t.created_at BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, '''', 
                        ' ORDER BY a.chat_id; ')
                );
            
                IF (pIsDebugger = 0) THEN
                    PREPARE stmt FROM @SQL_Final;
                    EXECUTE stmt;
                    DEALLOCATE PREPARE stmt;
                ELSE
                    SELECT @SQL_Final;
                END IF;
            END
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        

    }
}
