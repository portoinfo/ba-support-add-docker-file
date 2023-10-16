<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AttProDashboards extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
            DROP procedure IF EXISTS `pro_dashboard_attendant_tickets_chats_resolved`;
            
            CREATE DEFINER=CURRENT_USER PROCEDURE `pro_dashboard_attendant_tickets_chats_resolved`(IN pIdCompany INT, IN pIdDepartment INT, IN pIdAttendant INT, IN pFilterPeriod ENUM('WEEK', 'MONTH', 'YEAR'), IN pIsDebugger INT)
            BEGIN
                DECLARE v_id_department INTEGER;
                DECLARE v_id_attendant INTEGER;
                SET v_id_department = COALESCE(pIdDepartment, 0);
                SET v_id_attendant = COALESCE(pIdAttendant, 0);
                    
                SET @SQL_Final = IF(pFilterPeriod != 'MONTH',
                            CONCAT(' SELECT ', IF(pFilterPeriod = 'WEEK', 'day_, ', 'month_year, '), ' year, month, day, SUM(count) AS count FROM (
                                    SELECT
                                        ''Chat'' AS `type`, ',
                                        IF(pFilterPeriod = 'WEEK', ' DATE_FORMAT(c.update_status_closed_resolved, \"%d-%m\") as day_, ',
                                            ' DATE_FORMAT(c.update_status_closed_resolved, \"%m-%Y\") as month_year, '),
                                        'EXTRACT(YEAR FROM c.update_status_closed_resolved) AS year,
                                        EXTRACT(MONTH FROM c.update_status_closed_resolved) AS month,
                                        EXTRACT(DAY FROM c.update_status_closed_resolved) AS day,
                                        COUNT(*) AS count
                                    FROM chat c
                                    JOIN company_user_company_department cucd ON c.comp_user_comp_depart_id_current = cucd.id
                                    JOIN company_user cu ON cucd.company_user_id = cu.id
                                    WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL ',
                                    IF(v_id_department > 0, CONCAT(' AND c.company_department_id = ', v_id_department), ''),
                                    IF(v_id_attendant > 0, CONCAT(' AND cu.user_auth_id = ', v_id_attendant), ''),
                                    IF(pFilterPeriod = 'WEEK', ' AND c.update_status_closed_resolved between DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())-1 DAY) AND CURDATE() ',
                                        ' AND EXTRACT(YEAR FROM c.update_status_closed_resolved) = EXTRACT(YEAR FROM CURDATE()) '),
                                    ' AND c.status != ''CANCELED''
                                    GROUP BY 1, 2

                                    UNION

                                    SELECT
                                        ''Ticket'' AS `type`, ',
                                        IF(pFilterPeriod = 'WEEK', ' DATE_FORMAT(t.update_status_closed_resolved, \"%d-%m\") as day_, ',
                                            ' DATE_FORMAT(t.update_status_closed_resolved, \"%m-%Y\") as month_year, '),
                                        'EXTRACT(YEAR FROM t.update_status_closed_resolved) AS year,
                                        EXTRACT(MONTH FROM t.update_status_closed_resolved) AS month,
                                        EXTRACT(DAY FROM t.update_status_closed_resolved) AS day,
                                        COUNT(*) AS count
                                    FROM ticket t
                                    JOIN user_ticket ut on t.id = ut.ticket_id
                                    JOIN company_user cu ON ut.company_user_id = cu.id
                                    WHERE t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL ',
                                    IF(v_id_department > 0, CONCAT(' AND t.company_department_id = ', v_id_department), ''),
                                    IF(v_id_attendant > 0, CONCAT(' AND cu.user_auth_id = ', v_id_attendant), ''),
                                    IF(pFilterPeriod = 'WEEK', ' AND t.update_status_closed_resolved BETWEEN DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())-1 DAY) AND CURDATE() ',
                                        ' AND EXTRACT(YEAR FROM t.update_status_closed_resolved) = EXTRACT(YEAR FROM curdate()) '),
                                    ' AND t.status != ''CANCELED''
                                    GROUP BY 1, 2
                                ) sub
                                GROUP BY 1;'
                            ),
                            CONCAT(' SELECT week, count(*) as count
                                FROM ( 
                                    SELECT ''Chat'' as type,
                                        CASE
                                            WHEN EXTRACT(DAY FROM c.update_status_closed_resolved) BETWEEN 1 AND 7 THEN 1
                                            WHEN EXTRACT(DAY FROM c.update_status_closed_resolved) BETWEEN 8 AND 14 THEN 2
                                            WHEN EXTRACT(DAY FROM c.update_status_closed_resolved) BETWEEN 15 AND 21 THEN 3
                                            WHEN EXTRACT(DAY FROM c.update_status_closed_resolved) > 21 THEN 4
                                        END AS week
                                    FROM chat c
                                    JOIN company_user_company_department cucd ON c.comp_user_comp_depart_id_current = cucd.id
                                    JOIN company_user cu ON cucd.company_user_id = cu.id
                                    WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL ',
                                    IF(v_id_department > 0, CONCAT(' AND c.company_department_id = ', v_id_department), ''),
                                    IF(v_id_attendant > 0, CONCAT(' AND cu.user_auth_id = ', v_id_attendant), ''),
                                    ' AND c.update_status_closed_resolved >= DATE_FORMAT(CURDATE(), ''%Y-%m-01'')
                                    AND c.status != ''CANCELED''
                                    
                                    UNION ALL
                                    
                                    SELECT ''Ticket'' as type, 
                                        CASE
                                            WHEN EXTRACT(DAY FROM t.update_status_closed_resolved) BETWEEN 1 AND 7 THEN 1
                                            WHEN EXTRACT(DAY FROM t.update_status_closed_resolved) BETWEEN 8 AND 14 THEN 2
                                            WHEN EXTRACT(DAY FROM t.update_status_closed_resolved) BETWEEN 15 AND 21 THEN 3
                                            WHEN EXTRACT(DAY FROM t.update_status_closed_resolved) > 21 THEN 4
                                        END AS week
                                    FROM ticket t
                                    JOIN user_ticket ut on t.id = ut.ticket_id
                                    JOIN company_user cu ON ut.company_user_id = cu.id
                                    WHERE t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL ', 
                                    IF(v_id_department > 0, CONCAT(' AND t.company_department_id = ', v_id_department), ''),
                                    IF(v_id_attendant > 0, CONCAT(' AND cu.user_auth_id = ', v_id_attendant), ''),
                                    ' AND t.update_status_closed_resolved >= DATE_FORMAT(CURDATE(), ''%Y-%m-01'')
                                    AND t.status != ''CANCELED''
                                ) sub
                                GROUP BY 1
                                ORDER BY week;'
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
            

            DROP procedure IF EXISTS `pro_dashboard_attendant_tickets_chats_opened_x_closed`;

            CREATE DEFINER=CURRENT_USER PROCEDURE `pro_dashboard_attendant_tickets_chats_opened_x_closed`(IN pIdCompany INT, IN pIdDepartment INT, IN pIdAttendant INT, IN pFilterPeriod ENUM('WEEK', 'MONTH', 'YEAR'), IN pIsDebugger INT)
            BEGIN
                DECLARE v_id_department INTEGER;
                DECLARE v_id_attendant INTEGER;
                SET v_id_department = COALESCE(pIdDepartment, 0);
                SET v_id_attendant = COALESCE(pIdAttendant, 0); 
                    
                SET @SQL_Final = IF(pFilterPeriod != 'MONTH',
                            CONCAT(' SELECT
                                    ''Chats_Opened'' AS `type`, ',
                                    IF(pFilterPeriod = 'WEEK', ' DATE_FORMAT(c.created_at, \"%d-%m\") as day_, ',
                                        ' DATE_FORMAT(c.created_at, \"%m-%Y\") AS month_year, '),
                                    'EXTRACT(YEAR FROM c.created_at) AS year,
                                    EXTRACT(MONTH FROM c.created_at) AS month,
                                    EXTRACT(DAY FROM c.created_at) AS day,
                                    COUNT(*) AS count
                                FROM chat c ',
                                IF(v_id_attendant > 0, ' JOIN company_user_company_department cucd ON c.comp_user_comp_depart_id_current = cucd.id 
                                JOIN company_user cu ON cucd.company_user_id = cu.id ', ' '),
                                ' WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL AND c.ticket_id IS NULL',
                                IF(v_id_department > 0, CONCAT(' AND c.company_department_id = ', v_id_department), ''),
                                IF(v_id_attendant > 0, CONCAT(' AND cu.user_auth_id = ', v_id_attendant), ''),
                                IF(pFilterPeriod = 'WEEK', ' AND DATE(c.created_at) BETWEEN DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())-1 DAY) AND CURDATE() ',
                                    ' AND EXTRACT(YEAR FROM c.created_at) = EXTRACT(YEAR FROM CURDATE()) '),
                                ' AND c.status != ''CANCELED''
                                GROUP BY 1, 2

                                UNION ALL

                                SELECT
                                    ''Tickets_Opened'' AS `type`, ',
                                    IF(pFilterPeriod = 'WEEK', ' DATE_FORMAT(t.created_at, \"%d-%m\") as day_, ',
                                        ' DATE_FORMAT(t.created_at, \"%m-%Y\") AS month_year, '),
                                    'EXTRACT(YEAR FROM t.created_at) AS year,
                                    EXTRACT(MONTH FROM t.created_at) AS month,
                                    EXTRACT(DAY FROM t.created_at) AS day,
                                    COUNT(*) AS count
                                FROM ticket t ',
                                IF(v_id_attendant > 0, ' JOIN user_ticket ut on t.id = ut.ticket_id
                                JOIN company_user cu ON ut.company_user_id = cu.id ', ' '),
                                ' WHERE t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL ',
                                IF(v_id_department > 0, CONCAT(' AND t.company_department_id = ', v_id_department), ''),
                                IF(v_id_attendant > 0, CONCAT(' AND cu.user_auth_id = ', v_id_attendant), ''),
                                IF(pFilterPeriod = 'WEEK', ' AND DATE(t.created_at) BETWEEN DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())-1 DAY) AND CURDATE() ',
                                    ' AND EXTRACT(YEAR FROM t.created_at) = EXTRACT(YEAR FROM CURDATE()) '),
                                ' AND t.status != ''CANCELED''
                                GROUP BY 1, 2

                                UNION ALL

                                SELECT
                                    ''Chats_Closed'' AS `type`, ',
                                    IF(pFilterPeriod = 'WEEK', ' DATE_FORMAT(c.update_status_closed_resolved, \"%d-%m\") as day_, ',
                                        ' DATE_FORMAT(c.update_status_closed_resolved, \"%m-%Y\") as month_year, '),
                                    'EXTRACT(YEAR FROM c.update_status_closed_resolved) AS year,
                                    EXTRACT(MONTH FROM c.update_status_closed_resolved) AS month,
                                    EXTRACT(DAY FROM c.update_status_closed_resolved) AS day,
                                    COUNT(*) AS count
                                FROM chat c ',
                                IF(v_id_attendant > 0, ' JOIN company_user_company_department cucd ON c.comp_user_comp_depart_id_current = cucd.id 
                                JOIN company_user cu ON cucd.company_user_id = cu.id ', ' '),
                                ' WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL AND c.ticket_id IS NULL',
                                IF(v_id_department > 0, CONCAT(' AND c.company_department_id = ', v_id_department), ''),
                                IF(v_id_attendant > 0, CONCAT(' AND cu.user_auth_id = ', v_id_attendant), ''),
                                IF(pFilterPeriod = 'WEEK', ' AND DATE(c.update_status_closed_resolved) BETWEEN DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())-1 DAY) AND CURDATE() ',
                                    ' AND EXTRACT(YEAR FROM c.update_status_closed_resolved) = EXTRACT(YEAR FROM CURDATE()) '),
                                ' AND c.status != ''CANCELED''
                                GROUP BY 1, 2

                                UNION ALL

                                SELECT
                                    ''Tickets_Closed'' AS `type`, ',
                                    IF(pFilterPeriod = 'WEEK', ' DATE_FORMAT(t.update_status_closed_resolved, \"%d-%m\") as day_, ',
                                        ' DATE_FORMAT(t.update_status_closed_resolved, \"%m-%Y\") as month_year, '),
                                    'EXTRACT(YEAR FROM t.update_status_closed_resolved) AS year,
                                    EXTRACT(MONTH FROM t.update_status_closed_resolved) AS month,
                                    EXTRACT(DAY FROM t.update_status_closed_resolved) AS day,
                                    COUNT(*) AS count
                                FROM ticket t ',
                                IF(v_id_attendant > 0, ' JOIN user_ticket ut on t.id = ut.ticket_id
                                JOIN company_user cu ON ut.company_user_id = cu.id ', ' '),
                                ' WHERE t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL ',
                                IF(v_id_department > 0, CONCAT(' AND t.company_department_id = ', v_id_department), ''),
                                IF(v_id_attendant > 0, CONCAT(' AND cu.user_auth_id = ', v_id_attendant), ''),
                                IF(pFilterPeriod = 'WEEK', ' AND DATE(t.update_status_closed_resolved) BETWEEN DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())-1 DAY) AND CURDATE() ',
                                    ' AND EXTRACT(YEAR FROM t.update_status_closed_resolved) = EXTRACT(YEAR FROM CURDATE()) '),
                                ' AND t.status != ''CANCELED''
                                GROUP BY 1, 2
                                
                                UNION ALL

                                SELECT
                                    ''Chats_Canceled'' AS `type`, ',
                                    IF(pFilterPeriod = 'WEEK', ' DATE_FORMAT(c.update_status_canceled, \"%d-%m\") as day_, ',
                                        ' DATE_FORMAT(c.update_status_canceled, \"%m-%Y\") as month_year, '),
                                    'EXTRACT(YEAR FROM c.update_status_canceled) AS year,
                                    EXTRACT(MONTH FROM c.update_status_canceled) AS month,
                                    EXTRACT(DAY FROM c.update_status_canceled) AS day,
                                    COUNT(*) AS count
                                FROM chat c ',
                                IF(v_id_attendant > 0, ' JOIN company_user_company_department cucd ON c.comp_user_comp_depart_id_current = cucd.id 
                                JOIN company_user cu ON cucd.company_user_id = cu.id ', ' '),
                                ' WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL AND c.ticket_id IS NULL',
                                IF(v_id_department > 0, CONCAT(' AND c.company_department_id = ', v_id_department), ''),
                                IF(v_id_attendant > 0, CONCAT(' AND cu.user_auth_id = ', v_id_attendant), ''),
                                IF(pFilterPeriod = 'WEEK', ' AND DATE(c.update_status_canceled) BETWEEN DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())-1 DAY) AND CURDATE() ',
                                    ' AND EXTRACT(YEAR FROM c.update_status_canceled) = EXTRACT(YEAR FROM CURDATE()) '),
                                ' GROUP BY 1, 2

                                UNION ALL

                                SELECT
                                    ''Tickets_Canceled'' AS `type`, ',
                                    IF(pFilterPeriod = 'WEEK', ' DATE_FORMAT(t.update_status_canceled, \"%d-%m\") as day_, ',
                                        ' DATE_FORMAT(t.update_status_canceled, \"%m-%Y\") as month_year, '),
                                    'EXTRACT(YEAR FROM t.update_status_canceled) AS year,
                                    EXTRACT(MONTH FROM t.update_status_canceled) AS month,
                                    EXTRACT(DAY FROM t.update_status_canceled) AS day,
                                    COUNT(*) AS count
                                FROM ticket t ',
                                IF(v_id_attendant > 0, ' JOIN user_ticket ut on t.id = ut.ticket_id
                                JOIN company_user cu ON ut.company_user_id = cu.id ', ' '),
                                ' WHERE t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL ',
                                IF(v_id_department > 0, CONCAT(' AND t.company_department_id = ', v_id_department), ''),
                                IF(v_id_attendant > 0, CONCAT(' AND cu.user_auth_id = ', v_id_attendant), ''),
                                IF(pFilterPeriod = 'WEEK', ' AND DATE(t.update_status_canceled) BETWEEN DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())-1 DAY) AND CURDATE() ',
                                    ' AND EXTRACT(YEAR FROM t.update_status_canceled) = EXTRACT(YEAR FROM CURDATE()) '),
                                ' GROUP BY 1, 2;'                             
                            ),
                            CONCAT(' SELECT type, week, COUNT(*) as count
                                FROM ( 
                                    SELECT ''Chats_Opened'' as type,
                                        CASE
                                            WHEN EXTRACT(DAY FROM c.created_at) BETWEEN 1 AND 7 THEN 1
                                            WHEN EXTRACT(DAY FROM c.created_at) BETWEEN 8 AND 14 THEN 2
                                            WHEN EXTRACT(DAY FROM c.created_at) BETWEEN 15 AND 21 THEN 3
                                            WHEN EXTRACT(DAY FROM c.created_at) > 21 THEN 4
                                        END AS week
                                    FROM chat c ',
                                    IF(v_id_attendant > 0, ' JOIN company_user_company_department cucd ON c.comp_user_comp_depart_id_current = cucd.id 
                                    JOIN company_user cu ON cucd.company_user_id = cu.id ', ' '),
                                    ' WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL AND c.ticket_id IS NULL',
                                    IF(v_id_department > 0, CONCAT(' AND c.company_department_id = ', v_id_department), ''),
                                    IF(v_id_attendant > 0, CONCAT(' AND cu.user_auth_id = ', v_id_attendant), ''),
                                    ' AND c.status != ''CANCELED''
                                    AND c.created_at >= DATE_FORMAT(CURDATE(), ''%Y-%m-01'')
                                    
                                    UNION ALL
                                    
                                    SELECT ''Tickets_Opened'' as type, 
                                        CASE
                                            WHEN EXTRACT(DAY FROM t.created_at) BETWEEN 1 AND 7 THEN 1
                                            WHEN EXTRACT(DAY FROM t.created_at) BETWEEN 8 AND 14 THEN 2
                                            WHEN EXTRACT(DAY FROM t.created_at) BETWEEN 15 AND 21 THEN 3
                                            WHEN EXTRACT(DAY FROM t.created_at) > 21 THEN 4
                                        END AS week
                                    from ticket t ',
                                    IF(v_id_attendant > 0, ' JOIN user_ticket ut on t.id = ut.ticket_id
                                    JOIN company_user cu ON ut.company_user_id = cu.id ', ' '),
                                    ' where t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL ',
                                    IF(v_id_department > 0, CONCAT(' AND t.company_department_id = ', v_id_department), ''),
                                    IF(v_id_attendant > 0, CONCAT(' AND cu.user_auth_id = ', v_id_attendant), ''),
                                    ' AND t.status != ''CANCELED''
                                    AND t.created_at >= DATE_FORMAT(CURDATE(), ''%Y-%m-01'')

                                    UNION ALL
                                    
                                    SELECT ''Chats_Closed'' as type,
                                        CASE
                                            WHEN EXTRACT(DAY FROM c.update_status_closed_resolved) BETWEEN 1 AND 7 THEN 1
                                            WHEN EXTRACT(DAY FROM c.update_status_closed_resolved) BETWEEN 8 AND 14 THEN 2
                                            WHEN EXTRACT(DAY FROM c.update_status_closed_resolved) BETWEEN 15 AND 21 THEN 3
                                            WHEN EXTRACT(DAY FROM c.update_status_closed_resolved) > 21 THEN 4
                                        END AS week
                                    FROM chat c ',
                                    IF(v_id_attendant > 0, ' JOIN company_user_company_department cucd ON c.comp_user_comp_depart_id_current = cucd.id 
                                    JOIN company_user cu ON cucd.company_user_id = cu.id ', ' '),
                                    ' WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL AND c.ticket_id IS NULL',   
                                    IF(v_id_department > 0, CONCAT(' AND c.company_department_id = ', v_id_department), ''),
                                    IF(v_id_attendant > 0, CONCAT(' AND cu.user_auth_id = ', v_id_attendant), ''),
                                    ' AND c.status != ''CANCELED''
                                    AND c.update_status_closed_resolved >= DATE_FORMAT(CURDATE(), ''%Y-%m-01'')
                                    
                                    UNION ALL
                                    
                                    SELECT ''Tickets_Closed'' as type, 
                                        CASE
                                            WHEN EXTRACT(DAY FROM t.update_status_closed_resolved) BETWEEN 1 AND 7 THEN 1
                                            WHEN EXTRACT(DAY FROM t.update_status_closed_resolved) BETWEEN 8 AND 14 THEN 2
                                            WHEN EXTRACT(DAY FROM t.update_status_closed_resolved) BETWEEN 15 AND 21 THEN 3
                                            WHEN EXTRACT(DAY FROM t.update_status_closed_resolved) > 21 THEN 4
                                        END AS week
                                    from ticket t ',
                                    IF(v_id_attendant > 0, ' JOIN user_ticket ut on t.id = ut.ticket_id
                                    JOIN company_user cu ON ut.company_user_id = cu.id ', ' '),
                                    ' where t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL ',
                                    IF(v_id_department > 0, CONCAT(' AND t.company_department_id = ', v_id_department), ''),
                                    IF(v_id_attendant > 0, CONCAT(' AND cu.user_auth_id = ', v_id_attendant), ''),
                                    ' AND t.status != ''CANCELED''
                                    AND t.update_status_closed_resolved >= DATE_FORMAT(CURDATE(), ''%Y-%m-01'')

                                    UNION ALL
                                    
                                    SELECT ''Chats_Canceled'' as type,
                                        CASE
                                            WHEN EXTRACT(DAY FROM c.update_status_canceled) BETWEEN 1 AND 7 THEN 1
                                            WHEN EXTRACT(DAY FROM c.update_status_canceled) BETWEEN 8 AND 14 THEN 2
                                            WHEN EXTRACT(DAY FROM c.update_status_canceled) BETWEEN 15 AND 21 THEN 3
                                            WHEN EXTRACT(DAY FROM c.update_status_canceled) > 21 THEN 4
                                        END AS week
                                    FROM chat c ',
                                    IF(v_id_attendant > 0, ' JOIN company_user_company_department cucd ON c.comp_user_comp_depart_id_current = cucd.id 
                                    JOIN company_user cu ON cucd.company_user_id = cu.id ', ' '),
                                    ' WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL AND c.ticket_id IS NULL',   
                                    IF(v_id_department > 0, CONCAT(' AND c.company_department_id = ', v_id_department), ''),
                                    IF(v_id_attendant > 0, CONCAT(' AND cu.user_auth_id = ', v_id_attendant), ''),
                                    ' AND c.update_status_canceled >= DATE_FORMAT(CURDATE(), ''%Y-%m-01'')
                                    
                                    UNION ALL
                                    
                                    SELECT ''Tickets_Canceled'' as type, 
                                        CASE
                                            WHEN EXTRACT(DAY FROM t.update_status_canceled) BETWEEN 1 AND 7 THEN 1
                                            WHEN EXTRACT(DAY FROM t.update_status_canceled) BETWEEN 8 AND 14 THEN 2
                                            WHEN EXTRACT(DAY FROM t.update_status_canceled) BETWEEN 15 AND 21 THEN 3
                                            WHEN EXTRACT(DAY FROM t.update_status_canceled) > 21 THEN 4
                                        END AS week
                                    from ticket t ',
                                    IF(v_id_attendant > 0, ' JOIN user_ticket ut on t.id = ut.ticket_id
                                    JOIN company_user cu ON ut.company_user_id = cu.id ', ' '),
                                    ' where t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL ',
                                    IF(v_id_department > 0, CONCAT(' AND t.company_department_id = ', v_id_department), ''),
                                    IF(v_id_attendant > 0, CONCAT(' AND cu.user_auth_id = ', v_id_attendant), ''),
                                    ' AND t.update_status_canceled >= DATE_FORMAT(CURDATE(), ''%Y-%m-01'')
                                ) sub
                                GROUP BY type, week
                                ORDER BY week, type;')
                            );
                            
                IF (pIsDebugger = 0) THEN
                    PREPARE stmt FROM @SQL_Final;
                    EXECUTE stmt;
                    DEALLOCATE PREPARE stmt;
                ELSE
                    SELECT @SQL_Final;
                END IF;
            END;


            DROP procedure IF EXISTS `pro_dashboard_details_numbers_attendant`;

            CREATE DEFINER=CURRENT_USER PROCEDURE `pro_dashboard_details_numbers_attendant`(IN pIdCompany INT, IN pIdAttendant INT, IN pIdDepartment INT, IN pInitialDate DATETIME, IN pFinalDate DATETIME, IN pIsDebugger INT)
                COMMENT 'Searches for closed, resolved, ongoing chats/tickets and also the average queue / service time.'
            BEGIN
                DECLARE v_id_department INTEGER;
                SET v_id_department = COALESCE(pIdDepartment, 0);

                SET @SQL_Final = CONCAT('SELECT
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
                                ''Chat'' AS `type`,
                                COUNT(c.id) AS count,
                                -- SUM(IF(c.status = ''OPENED'', 1, 0)) AS opened,
                                SUM(IF(c.status = ''IN_PROGRESS'', 1, 0)) AS in_progress,
                                SUM(IF(c.status = ''CLOSED'', 1, 0)) AS closed,
                                SUM(IF(c.status = ''RESOLVED'', 1, 0)) AS resolved,
                                SUM(IF(c.status IN (''IN_PROGRESS'', ''CLOSED'', ''RESOLVED'') AND c.type = ''CHANGED_TO_TICKET'', 1, 0)) AS changed_to_ticket,
                                ROUND(AVG(IFNULL(c.queue_time, 0))) AS avg_queue_time,
                                ROUND(AVG(IFNULL(c.service_time, 0))) AS avg_service_time
                            FROM chat c
                            JOIN company_user_company_department cucd ON c.comp_user_comp_depart_id_current = cucd.id
                            JOIN company_user cu ON cucd.company_user_id = cu.id
                            JOIN user_auth u on cu.user_auth_id = u.id
                            WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL
                            AND (', v_id_department, ' = 0 OR c.company_department_id = ', v_id_department, ')
                            AND cu.user_auth_id = ', pIdAttendant,
                            IF(COALESCE(pInitialDate, 0) > 0, CONCAT(' AND c.created_at >= ''', pInitialDate), ''),
                            IF(COALESCE(pFinalDate, 0) > 0, CONCAT(' AND c.created_at <= ''', pFinalDate), ''),
                            ' AND c.status != ''CANCELED''
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
                                ''Ticket'' AS `type`,
                                COUNT(t.id) AS count,
                                -- SUM(IF(t.status = ''OPENED'', 1, 0)) AS opened,
                                SUM(IF(t.status = ''IN_PROGRESS'', 1, 0)) AS in_progress,
                                SUM(IF(t.status = ''CLOSED'', 1, 0)) AS closed,
                                SUM(IF(t.status = ''RESOLVED'', 1, 0)) AS resolved,
                                0 AS changed_to_ticket,
                                ROUND(AVG(IFNULL(t.queue_time, 0))) AS avg_queue_time, 
                                ROUND(AVG(IFNULL(t.service_time, 0))) AS avg_service_time
                            FROM ticket t
                            JOIN user_ticket ut on t.id = ut.ticket_id
                            JOIN company_user cu ON ut.company_user_id = cu.id
                            WHERE t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL
                            AND (', v_id_department, ' = 0 OR t.company_department_id = ', v_id_department, ')
                            AND cu.user_auth_id = ', pIdAttendant,
                            IF(COALESCE(pInitialDate, 0) > 0, CONCAT(' AND c.created_at >= ''', pInitialDate), ''),
                            IF(COALESCE(pFinalDate, 0) > 0, CONCAT(' AND c.created_at <= ''', pFinalDate), ''),
                            ' AND t.status != ''CANCELED''
                        ) sub_ticket;');

                IF (pIsDebugger = 0) THEN
                    PREPARE stmt FROM @SQL_Final;
                    EXECUTE stmt;
                    DEALLOCATE PREPARE stmt;
                ELSE
                    SELECT @SQL_Final;
                END IF;
            END;

            DROP procedure IF EXISTS `pro_dashboard_details_average_attendants_attendances`;

            CREATE DEFINER=CURRENT_USER PROCEDURE `pro_dashboard_details_average_attendants_attendances`(IN pIdCompany INT, IN pIdAttendant INT, IN pIdDepartment INT, IN pInitialDate DATETIME, IN pFinalDate DATETIME, IN pIsDebugger INT)
                COMMENT 'General average of attendants / attendances.'
            BEGIN
                DECLARE v_id_department INTEGER;
                SET v_id_department = COALESCE(pIdDepartment, 0);

                SET @SQL_Final = CONCAT('SELECT
                        ''Chat'' AS `type`,
                        count_geral,
                        count_1_atendent,
                        count_2_atendent,
                        count_3_atendent,
                        count_4_atendent,
                        count_5_atendent,
                        count_1_service,
                        count_2_service,
                        count_3_service,
                        count_4_service,
                        count_5_service,
                        media_stars_atendent,
                        media_stars_service
                    FROM (
                        SELECT
                            COUNT(DISTINCT c.id) count_geral,
                            SUM(IF(av.stars_atendent = 1, 1, 0)) AS count_1_atendent,
                            SUM(IF(av.stars_atendent = 2, 1, 0)) AS count_2_atendent,
                            SUM(IF(av.stars_atendent = 3, 1, 0)) AS count_3_atendent,
                            SUM(IF(av.stars_atendent = 4, 1, 0)) AS count_4_atendent,
                            SUM(IF(av.stars_atendent = 5, 1, 0)) AS count_5_atendent,
                            SUM(IF(av.stars_service = 1, 1, 0)) AS count_1_service,
                            SUM(IF(av.stars_service = 2, 1, 0)) AS count_2_service,
                            SUM(IF(av.stars_service = 3, 1, 0)) AS count_3_service,
                            SUM(IF(av.stars_service = 4, 1, 0)) AS count_4_service,
                            SUM(IF(av.stars_service = 5, 1, 0)) AS count_5_service,
                            CAST(AVG(av.stars_atendent) AS DECIMAL(10, 2)) AS media_stars_atendent, 
                            CAST(AVG(av.stars_service) AS DECIMAL(10, 2)) AS media_stars_service
                        FROM avaliation av
                        JOIN chat c ON av.chat_id = c.id
                        JOIN company_user_company_department cucd ON c.comp_user_comp_depart_id_current = cucd.id
                        JOIN company_user cu ON cucd.company_user_id = cu.id
                        WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL
                        AND (', v_id_department, ' = 0 OR c.company_department_id = ', v_id_department, ')
                        AND cu.user_auth_id = ', pIdAttendant,
                        IF(COALESCE(pInitialDate, 0) > 0, CONCAT(' AND c.created_at >= ''', pInitialDate), ''),
                        IF(COALESCE(pFinalDate, 0) > 0, CONCAT(' AND c.created_at <= ''', pFinalDate), ''),
                        ' AND c.status != ''CANCELED''
                        AND c.ticket_id IS NULL
                    ) sub
                    
                    UNION
                    
                    SELECT
                        ''Ticket'' AS `type`,
                        count_geral,
                        count_1_atendent,
                        count_2_atendent,
                        count_3_atendent,
                        count_4_atendent,
                        count_5_atendent,
                        count_1_service,
                        count_2_service,
                        count_3_service,
                        count_4_service,
                        count_5_service,
                        media_stars_atendent,
                        media_stars_service
                    FROM (
                        SELECT
                            COUNT(DISTINCT t.id) count_geral,
                            SUM(IF(av.stars_atendent = 1, 1, 0)) AS count_1_atendent,
                            SUM(IF(av.stars_atendent = 2, 1, 0)) AS count_2_atendent,
                            SUM(IF(av.stars_atendent = 3, 1, 0)) AS count_3_atendent,
                            SUM(IF(av.stars_atendent = 4, 1, 0)) AS count_4_atendent,
                            SUM(IF(av.stars_atendent = 5, 1, 0)) AS count_5_atendent,
                            SUM(IF(av.stars_service = 1, 1, 0)) AS count_1_service,
                            SUM(IF(av.stars_service = 2, 1, 0)) AS count_2_service,
                            SUM(IF(av.stars_service = 3, 1, 0)) AS count_3_service,
                            SUM(IF(av.stars_service = 4, 1, 0)) AS count_4_service,
                            SUM(IF(av.stars_service = 5, 1, 0)) AS count_5_service,
                            CAST(AVG(av.stars_atendent) AS DECIMAL(10, 2)) AS media_stars_atendent, 
                            CAST(AVG(av.stars_service) AS DECIMAL(10, 2)) AS media_stars_service
                        FROM avaliation av
                        JOIN ticket t ON av.ticket_id = t.id
                        JOIN user_ticket ut on t.id = ut.ticket_id
                        JOIN company_user cu ON ut.company_user_id = cu.id
                        WHERE t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL
                        AND (', v_id_department, ' = 0 OR t.company_department_id = ', v_id_department, ')
                        AND cu.user_auth_id = ', pIdAttendant,
                        IF(COALESCE(pInitialDate, 0) > 0, CONCAT(' AND c.created_at >= ''', pInitialDate), ''),
                        IF(COALESCE(pFinalDate, 0) > 0, CONCAT(' AND c.created_at <= ''', pFinalDate), ''),
                        ' AND t.status != ''CANCELED''
                    ) sub;');
                
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
        Schema::dropIfExists('pro_dashboard_attendant_tickets_chats_resolved');
        Schema::dropIfExists('pro_dashboard_attendant_tickets_chats_opened_x_closed');
        Schema::dropIfExists('pro_dashboard_details_numbers_attendant');
        Schema::dropIfExists('pro_dashboard_details_average_attendants_attendances');
    }
}
