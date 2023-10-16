<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AttProdDashboardDay3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
            DROP procedure IF EXISTS `pro_dashboard_attendant_totals_and_percentages`;

            CREATE DEFINER=CURRENT_USER PROCEDURE `pro_dashboard_attendant_totals_and_percentages`(IN pIdCompany INT, IN pIdDepartment INT, IN pIdAttendant INT, IN pIsDebugger INT)
            BEGIN
                DECLARE v_id_department INTEGER;
                DECLARE v_id_attendant INTEGER;
                SET v_id_department = COALESCE(pIdDepartment, 0);
                SET v_id_attendant = COALESCE(pIdAttendant, 0);
            
                SET @SQL_Final = 
                    CONCAT('SELECT
                            `type`, 
                            count, 
                            IFNULL(ROUND((in_progress * 100 / (in_progress + opened))), 0) as perc_em_atendimento,
                            IFNULL(ROUND((closed * 100 / count)), 0) as perc_fechados
                        FROM (
                            SELECT
                                ''Chat'' AS `type`,
                                COUNT(c.id) AS count,
                                SUM(IF(c.status IN (''CLOSED'', ''RESOLVED''), 1, 0)) AS closed,
                                SUM(IF(c.status = ''OPENED'', 1, 0)) AS opened,
                                SUM(IF(c.status = ''IN_PROGRESS'', 1, 0)) AS in_progress
                            FROM chat c
                            JOIN company_user_company_department cucd ON c.comp_user_comp_depart_id_current = cucd.id
                            JOIN company_user cu ON cucd.company_user_id = cu.id
                            WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL ',
                            IF(v_id_department > 0, CONCAT(' AND c.company_department_id = ', v_id_department), ''),
                            IF(v_id_attendant > 0, CONCAT(' AND cu.user_auth_id = ', v_id_attendant), ''),
                            ' AND c.status != ''CANCELED''
            
                            UNION
                            
                            SELECT
                                ''Ticket'' AS `type`,
                                COUNT(t.id) AS count,
                                SUM(IF(t.status IN (''CLOSED'', ''RESOLVED''), 1, 0)) AS closed,
                                SUM(IF(t.status = ''OPENED'', 1, 0)) AS opened,
                                SUM(IF(t.status = ''IN_PROGRESS'', 1, 0)) AS in_progress
                            FROM ticket t
                            JOIN user_ticket ut on t.id = ut.ticket_id
                            JOIN company_user cu ON ut.company_user_id = cu.id
                            WHERE t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL ',
                            IF(v_id_department > 0, CONCAT(' AND t.company_department_id = ', v_id_department), ''),
                            IF(v_id_attendant > 0, CONCAT(' AND cu.user_auth_id = ', v_id_attendant), ''),
                            ' AND t.status != ''CANCELED''
                        ) sub
                        GROUP BY 1;');
            
                IF (pIsDebugger = 0) THEN
                    PREPARE stmt FROM @SQL_Final;
                    EXECUTE stmt;
                    DEALLOCATE PREPARE stmt;
                ELSE
                    SELECT @SQL_Final;
                END IF;
            END;
        
            DROP procedure IF EXISTS `pro_dashboard_attendant_general_average_for_attendances`;

            CREATE DEFINER=CURRENT_USER PROCEDURE `pro_dashboard_attendant_general_average_for_attendances`(IN pIdCompany INT, IN pIdDepartment INT, IN pIdAttendant INT, IN pIsDebugger INT)
            BEGIN
                DECLARE v_id_department INTEGER;
                DECLARE v_id_attendant INTEGER;
                SET v_id_department = COALESCE(pIdDepartment, 0);
                SET v_id_attendant = COALESCE(pIdAttendant, 0);
                    
                SET @SQL_Final = 
                    CONCAT('SELECT
                            -- type, 
                            CAST(AVG(stars_atendent) AS DECIMAL(10, 2)) AS media_stars_atendent, 
                            CAST(AVG(stars_service) AS DECIMAL(10, 2)) AS media_stars_service
                        FROM (
                            SELECT
                                ''Chat'' AS `type`,
                                av.stars_atendent, av.stars_service
                            FROM avaliation av
                            JOIN chat c ON av.chat_id = c.id
                            JOIN company_user_company_department cucd ON c.comp_user_comp_depart_id_current = cucd.id
                            JOIN company_user cu ON cucd.company_user_id = cu.id
                            WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL ',
                            IF(v_id_department > 0, CONCAT(' AND c.company_department_id = ', v_id_department), ''),
                            IF(v_id_attendant > 0, CONCAT(' AND cu.user_auth_id = ', v_id_attendant), ''),
                            ' AND c.status != ''CANCELED''
            
                            UNION
            
                            SELECT
                                ''Ticket'' AS `type`,
                                av.stars_atendent, av.stars_service
                            FROM avaliation av
                            JOIN ticket t ON av.ticket_id = t.id
                            JOIN user_ticket ut on t.id = ut.ticket_id
                            JOIN company_user cu ON ut.company_user_id = cu.id
                            WHERE t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL ',
                            IF(v_id_department > 0, CONCAT(' AND t.company_department_id = ', v_id_department), ''),
                            IF(v_id_attendant > 0, CONCAT(' AND cu.user_auth_id = ', v_id_attendant), ''),
                            ' AND t.status != ''CANCELED''
                        ) sub;
                        -- GROUP BY type;');
                IF (pIsDebugger = 0) THEN
                    PREPARE stmt FROM @SQL_Final;
                    EXECUTE stmt;
                    DEALLOCATE PREPARE stmt;
                ELSE
                    SELECT @SQL_Final;
                END IF;
            END;
            
            DROP procedure IF EXISTS `pro_dashboard_attendant_tickets_average_time_queue_and_service`;

            CREATE DEFINER=CURRENT_USER PROCEDURE `pro_dashboard_attendant_tickets_average_time_queue_and_service`(IN pIdCompany INT, IN pIdDepartment INT, IN pIdAttendant INT, IN pFilterPeriod ENUM('LAST_24_HOURS', 'LAST_7_DAYS', 'LAST_30_DAYS', 'LAST_365_DAYS'), IN pIsDebugger INT)
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
            
                IF (pIsDebugger = 0) THEN
                    PREPARE stmt FROM @SQL_Final;
                    EXECUTE stmt;
                    DEALLOCATE PREPARE stmt;
                ELSE
                    SELECT @SQL_Final;
                END IF;
            END;

            DROP procedure IF EXISTS `pro_dashboard_attendant_chats_average_time_queue_and_service`;

            CREATE DEFINER=CURRENT_USER PROCEDURE `pro_dashboard_attendant_chats_average_time_queue_and_service`(IN pIdCompany INT, IN pIdDepartment INT, IN pIdAttendant INT, IN pFilterPeriod ENUM('LAST_24_HOURS', 'LAST_7_DAYS', 'LAST_30_DAYS', 'LAST_365_DAYS'), IN pIsDebugger INT)
            BEGIN
                DECLARE v_id_department INTEGER;
                DECLARE v_id_attendant INTEGER;
                SET v_id_department = COALESCE(pIdDepartment, 0);
                SET v_id_attendant = COALESCE(pIdAttendant, 0);
                    
                SET @SQL_Final = 
                    CONCAT('SELECT
                        ROUND(AVG(IFNULL(c.queue_time, 0))) AS avg_queue_time, 
                        ROUND(AVG(IFNULL(c.service_time, 0))) AS avg_service_time
                    FROM chat c
                    JOIN company_user_company_department cucd ON c.comp_user_comp_depart_id_current = cucd.id
                    JOIN company_user cu ON cucd.company_user_id = cu.id
                    WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL ',
                    IF(v_id_department > 0, CONCAT(' AND c.company_department_id = ', v_id_department), ''),
                    IF(v_id_attendant > 0, CONCAT(' AND cu.user_auth_id = ', v_id_attendant), ''),
                    ' AND c.status != ''CANCELED''',
                    IF(pFilterPeriod = 'LAST_24_HOURS', ' AND c.created_at >= DATE_SUB(CURDATE(), INTERVAL 24 HOUR);',
                        IF(pFilterPeriod = 'LAST_7_DAYS', ' AND c.created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY);',
                            IF(pFilterPeriod = 'LAST_30_DAYS', ' AND c.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY);',
                                IF(pFilterPeriod = 'LAST_365_DAYS', ' AND c.created_at >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR);', '')
                            )
                        )
                    )
                );
            
                IF (pIsDebugger = 0) THEN
                    PREPARE stmt FROM @SQL_Final;
                    EXECUTE stmt;
                    DEALLOCATE PREPARE stmt;
                ELSE
                    SELECT @SQL_Final;
                END IF;
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
        Schema::dropIfExists('pro_dashboard_attendant_totals_and_percentages');
        Schema::dropIfExists('pro_dashboard_attendant_general_average_for_attendances');
        Schema::dropIfExists('pro_dashboard_attendant_tickets_average_time_queue_and_service');
        Schema::dropIfExists('pro_dashboard_attendant_chats_average_time_queue_and_service');
    }
}
