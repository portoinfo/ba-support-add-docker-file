<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AttSpsAddTicketMerged extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
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
                        ' AND t.status != ''CANCELED'' AND t.status != ''MERGED'' 
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
                    ' AND t.status != ''CANCELED'' AND t.status != ''MERGED'' ',
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
                                ' AND t.status != ''CANCELED'' AND t.status != ''MERGED'' 
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
                                ' AND t.status != ''CANCELED'' AND t.status != ''MERGED'' 
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
                                ' WHERE t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL AND t.status != ''MERGED'' ',
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
                                    ' AND t.status != ''CANCELED'' AND t.status != ''MERGED'' 
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
                                    ' AND t.status != ''CANCELED'' AND t.status != ''MERGED'' 
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
                                    ' where t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL AND t.status != ''MERGED'' ',
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
                                    ' AND t.status != ''CANCELED'' AND t.status != ''MERGED''
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
                                    AND t.status != ''CANCELED'' AND t.status != ''MERGED''
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
                            ' AND t.status != ''CANCELED'' AND t.status != ''MERGED''
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

            DROP procedure IF EXISTS `pro_dashboard_company_occurrences_by_department`;
            CREATE DEFINER=CURRENT_USER PROCEDURE `pro_dashboard_company_occurrences_by_department`(IN pIdCompany INT, IN pIdDepartment INT, IN pFilterPeriod ENUM('WEEK', 'MONTH', 'YEAR'), IN pIsDebugger INT)
            BEGIN
                SET @SQL_Final = IF(pFilterPeriod != 'MONTH',
                            CONCAT('SELECT
                                ''Chat'' AS `type`, ', 
                                IF(pFilterPeriod = 'WEEK', ' DATE_FORMAT(created_at, \"%d-%m\") as dia, ', ' DATE_FORMAT(created_at, \"%m-%Y\") as mesano, '),
                                'EXTRACT(YEAR FROM created_at) AS year, EXTRACT(MONTH FROM created_at) AS month, EXTRACT(DAY FROM created_at) AS day, 
                                COUNT(*) AS count
                            FROM chat
                            WHERE company_id = ', pIdCompany, ' AND deleted_at IS NULL
                            AND status != ''CANCELED''
                            AND company_department_id = ', pIdDepartment, 
                            IF(pFilterPeriod = 'WEEK', ' AND created_at BETWEEN DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())-1 DAY) AND CURDATE() + INTERVAL 1 DAY ',
                            ' AND EXTRACT(YEAR FROM created_at) = EXTRACT(YEAR FROM curdate()) '),
                            ' GROUP BY 1, 2

                            UNION

                            SELECT 
                                ''Ticket'' AS `type`, ',
                                IF(pFilterPeriod = 'WEEK', ' DATE_FORMAT(created_at, \"%d-%m\") as dia, ', ' DATE_FORMAT(created_at, \"%m-%Y\") as mesano, '), 
                                'EXTRACT(YEAR FROM created_at) AS year, EXTRACT(MONTH FROM created_at) AS month, EXTRACT(DAY FROM created_at) AS day, 
                                COUNT(*) AS count
                            FROM ticket
                            WHERE company_id = ', pIdCompany, ' AND deleted_at IS NULL
                            AND status != ''CANCELED'' AND status != ''MERGED''
                            AND company_department_id = ', pIdDepartment, 
                            IF(pFilterPeriod = 'WEEK', ' AND created_at BETWEEN DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())-1 DAY) AND CURDATE() + INTERVAL 1 DAY ',
                            ' AND EXTRACT(YEAR FROM created_at) = EXTRACT(YEAR FROM curdate()) '),
                            ' GROUP BY 1, 2;'),
                        CONCAT(' SELECT type, semana, count(*) as Count
                        FROM ( 
                            SELECT ''Chat'' as type,
                                CASE
                                    WHEN (DATEDIFF(CURDATE(), created_at) + 1) BETWEEN 1 AND 7 THEN ''4''
                                    WHEN (DATEDIFF(CURDATE(), created_at) + 1) BETWEEN 8 AND 14 THEN ''3''
                                    WHEN (DATEDIFF(CURDATE(), created_at) + 1) BETWEEN 15 AND 21 THEN ''2''
                                    WHEN (DATEDIFF(CURDATE(), created_at) + 1) BETWEEN 22 AND 28 THEN ''1''
                                END AS semana
                            FROM chat
                            WHERE company_id = ', pIdCompany, 
                            ' AND status != ''CANCELED''
                            AND company_department_id = ', pIdDepartment, 
                            ' and created_at >= DATE_SUB(curdate(), INTERVAL 27 DAY)
                            and deleted_at IS NULL
                            
                            UNION ALL
                            
                            SELECT ''Ticket'' as type, 
                                CASE
                                    WHEN (DATEDIFF(CURDATE(), created_at) + 1) BETWEEN 1 AND 7 THEN ''4''
                                    WHEN (DATEDIFF(CURDATE(), created_at) + 1) BETWEEN 8 AND 14 THEN ''3''
                                    WHEN (DATEDIFF(CURDATE(), created_at) + 1) BETWEEN 15 AND 21 THEN ''2''
                                    WHEN (DATEDIFF(CURDATE(), created_at) + 1) BETWEEN 22 AND 28 THEN ''1''
                                END AS semana
                            from ticket
                            WHERE company_id = ', pIdCompany, 
                            ' AND status != ''CANCELED'' AND status != ''MERGED''
                            AND company_department_id = ', pIdDepartment,
                            ' and created_at >= DATE_SUB(curdate(), INTERVAL 27 DAY)
                            and deleted_at IS NULL
                        ) sub
                        GROUP BY type, semana
                        ORDER BY semana, type;')
                        );

                IF (pIsDebugger = 0) THEN
                    PREPARE stmt FROM @SQL_Final;
                    EXECUTE stmt;
                    DEALLOCATE PREPARE stmt;
                ELSE
                    SELECT @SQL_Final;
                END IF;
            END;

            DROP procedure IF EXISTS `pro_dashboard_company_tickets_average_time_queue_and_service`;
            CREATE DEFINER=CURRENT_USER PROCEDURE `pro_dashboard_company_tickets_average_time_queue_and_service`(IN pIdCompany INT, IN pIdDepartment INT, IN pFilterPeriod ENUM('LAST_24_HOURS', 'LAST_7_DAYS', 'LAST_30_DAYS', 'LAST_365_DAYS'), IN pIsDebugger INT)
            BEGIN
                DECLARE v_id_department INTEGER;
                SET v_id_department = COALESCE(pIdDepartment, 0);
                    
                SET @SQL_Final = 
                    CONCAT('SELECT
                        ROUND(AVG(IFNULL(queue_time, 0))) AS avg_queue_time, 
                        ROUND(AVG(IFNULL(service_time, 0))) AS avg_service_time
                    FROM ticket
                    WHERE company_id = ', pIdCompany, ' AND deleted_at IS NULL ',
                    IF(v_id_department > 0, CONCAT(' AND company_department_id = ', v_id_department), ''),
                    ' AND status != ''CANCELED'' AND status != ''MERGED''',
                    IF(pFilterPeriod = 'LAST_24_HOURS', ' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 24 HOUR);',
                        IF(pFilterPeriod = 'LAST_7_DAYS', ' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY);',
                            IF(pFilterPeriod = 'LAST_30_DAYS', ' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY);',
                                IF(pFilterPeriod = 'LAST_365_DAYS', ' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR);', '')
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

            DROP procedure IF EXISTS `pro_dashboard_department_general_average_for_attendances`;
            CREATE DEFINER=CURRENT_USER PROCEDURE `pro_dashboard_department_general_average_for_attendances`(IN pIdCompany INT, IN pIdDepartment INT, IN pIsDebugger INT)
            BEGIN
            DECLARE v_id_department INTEGER;
            SET v_id_department = COALESCE(pIdDepartment, 0);
            
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
                    WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL 
                    AND c.status != ''CANCELED'' ',
                    IF(v_id_department > 0, CONCAT(' AND c.company_department_id = ', v_id_department), ''),

                    ' UNION

                    SELECT
                        ''Ticket'' AS `type`,
                        av.stars_atendent, av.stars_service
                    FROM avaliation av
                    JOIN ticket t ON av.ticket_id = t.id
                    WHERE t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL
                    AND t.status != ''CANCELED'' AND t.status != ''MERGED'' ',
                    IF(v_id_department > 0, CONCAT(' AND t.company_department_id = ', v_id_department), ''),
                ') sub;
                -- GROUP BY type;');
                
            IF (pIsDebugger = 0) THEN
                PREPARE stmt FROM @SQL_Final;
                EXECUTE stmt;
                DEALLOCATE PREPARE stmt;
            ELSE
                SELECT @SQL_Final;
            END IF;
        END;

                DROP procedure IF EXISTS `pro_dashboard_department_tickets_chats_opened_x_closed`;
                CREATE DEFINER=CURRENT_USER PROCEDURE `pro_dashboard_department_tickets_chats_opened_x_closed`(IN pIdCompany INT, IN pIdDepartment INT, IN pFilterPeriod ENUM('WEEK', 'MONTH', 'YEAR'), IN pIsDebugger INT)
                BEGIN
                DECLARE v_id_department INTEGER; 
                SET v_id_department = COALESCE(pIdDepartment, 0);
                    
                SET @SQL_Final = IF(pFilterPeriod != 'MONTH',
                            CONCAT(' SELECT
                                    ''Chats_Opened'' AS `type`, ',
                                    IF(pFilterPeriod = 'WEEK', ' DATE_FORMAT(c.created_at, \"%d-%m\") as day_, ',
                                        ' DATE_FORMAT(c.created_at, \"%m-%Y\") AS month_year, '),
                                    'EXTRACT(YEAR FROM c.created_at) AS year,
                                    EXTRACT(MONTH FROM c.created_at) AS month,
                                    EXTRACT(DAY FROM c.created_at) AS day,
                                    COUNT(*) AS count
                                FROM chat c
                                WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL
                                AND c.status != ''CANCELED''',
                                IF(v_id_department > 0, CONCAT(' AND c.company_department_id = ', v_id_department), ''),
                                IF(pFilterPeriod = 'WEEK', ' AND c.created_at BETWEEN DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())-1 DAY) AND CURDATE() + INTERVAL 1 DAY ',
                                    ' AND EXTRACT(YEAR FROM c.created_at) = EXTRACT(YEAR FROM CURDATE()) '),
                                ' GROUP BY 1, 2

                                UNION

                                SELECT
                                    ''Tickets_Opened'' AS `type`, ',
                                    IF(pFilterPeriod = 'WEEK', ' DATE_FORMAT(t.created_at, \"%d-%m\") as day_, ',
                                        ' DATE_FORMAT(t.created_at, \"%m-%Y\") AS month_year, '),
                                    'EXTRACT(YEAR FROM t.created_at) AS year,
                                    EXTRACT(MONTH FROM t.created_at) AS month,
                                    EXTRACT(DAY FROM t.created_at) AS day,
                                    COUNT(*) AS count
                                FROM ticket t
                                WHERE t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL
                                AND t.status != ''CANCELED'' AND t.status != ''MERGED''',
                                IF(v_id_department > 0, CONCAT(' AND t.company_department_id = ', v_id_department), ''),
                                IF(pFilterPeriod = 'WEEK', ' AND t.created_at BETWEEN DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())-1 DAY) AND CURDATE() + INTERVAL 1 DAY ',
                                    ' AND EXTRACT(YEAR FROM t.created_at) = EXTRACT(YEAR FROM CURDATE()) '),
                                ' GROUP BY 1, 2

                                UNION

                                SELECT
                                    ''Chats_Closed'' AS `type`, ',
                                    IF(pFilterPeriod = 'WEEK', ' DATE_FORMAT(c.update_status_closed_resolved, \"%d-%m\") as day_, ',
                                        ' DATE_FORMAT(c.update_status_closed_resolved, \"%m-%Y\") as month_year, '),
                                    'EXTRACT(YEAR FROM c.update_status_closed_resolved) AS year,
                                    EXTRACT(MONTH FROM c.update_status_closed_resolved) AS month,
                                    EXTRACT(DAY FROM c.update_status_closed_resolved) AS day,
                                    COUNT(*) AS count
                                FROM chat c
                                WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL
                                AND c.status != ''CANCELED''',
                                IF(v_id_department > 0, CONCAT(' AND c.company_department_id = ', v_id_department), ''),
                                IF(pFilterPeriod = 'WEEK', ' AND c.update_status_closed_resolved BETWEEN DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())-1 DAY) AND CURDATE() + INTERVAL 1 DAY ',
                                    ' AND EXTRACT(YEAR FROM c.update_status_closed_resolved) = EXTRACT(YEAR FROM CURDATE()) '),
                                ' GROUP BY 1, 2

                                UNION

                                SELECT
                                    ''Tickets_Closed'' AS `type`, ',
                                    IF(pFilterPeriod = 'WEEK', ' DATE_FORMAT(t.update_status_closed_resolved, \"%d-%m\") as day_, ',
                                        ' DATE_FORMAT(t.update_status_closed_resolved, \"%m-%Y\") as month_year, '),
                                    'EXTRACT(YEAR FROM t.update_status_closed_resolved) AS year,
                                    EXTRACT(MONTH FROM t.update_status_closed_resolved) AS month,
                                    EXTRACT(DAY FROM t.update_status_closed_resolved) AS day,
                                    COUNT(*) AS count
                                FROM ticket t
                                WHERE t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL
                                AND t.status != ''CANCELED'' AND t.status != ''MERGED''',
                                IF(v_id_department > 0, CONCAT(' AND t.company_department_id = ', v_id_department), ''),
                                IF(pFilterPeriod = 'WEEK', ' AND t.update_status_closed_resolved BETWEEN DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())-1 DAY) AND CURDATE() + INTERVAL 1 DAY ',
                                    ' AND EXTRACT(YEAR FROM t.update_status_closed_resolved) = EXTRACT(YEAR FROM CURDATE()) '),
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
                                    FROM chat c
                                    WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL
                                    AND c.status != ''CANCELED''',
                                    IF(v_id_department > 0, CONCAT(' AND c.company_department_id = ', v_id_department), ''),
                                    ' AND c.created_at >= DATE_FORMAT(CURDATE(), ''%Y-%m-01'')    
                                    
                                    UNION ALL
                                    
                                    SELECT ''Tickets_Opened'' as type, 
                                        CASE
                                            WHEN EXTRACT(DAY FROM t.created_at) BETWEEN 1 AND 7 THEN 1
                                            WHEN EXTRACT(DAY FROM t.created_at) BETWEEN 8 AND 14 THEN 2
                                            WHEN EXTRACT(DAY FROM t.created_at) BETWEEN 15 AND 21 THEN 3
                                            WHEN EXTRACT(DAY FROM t.created_at) > 21 THEN 4
                                        END AS week
                                    from ticket t
                                    where t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL
                                    AND t.status != ''CANCELED'' AND t.status != ''MERGED''',
                                    IF(v_id_department > 0, CONCAT(' AND t.company_department_id = ', v_id_department), ''),
                                    ' AND t.created_at >= DATE_FORMAT(CURDATE(), ''%Y-%m-01'')

                                    UNION
                                    
                                    SELECT ''Chats_Closed'' as type,
                                        CASE
                                            WHEN EXTRACT(DAY FROM c.update_status_closed_resolved) BETWEEN 1 AND 7 THEN 1
                                            WHEN EXTRACT(DAY FROM c.update_status_closed_resolved) BETWEEN 8 AND 14 THEN 2
                                            WHEN EXTRACT(DAY FROM c.update_status_closed_resolved) BETWEEN 15 AND 21 THEN 3
                                            WHEN EXTRACT(DAY FROM c.update_status_closed_resolved) > 21 THEN 4
                                        END AS week
                                    FROM chat c
                                    WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL
                                    AND c.status != ''CANCELED''',
                                    IF(v_id_department > 0, CONCAT(' AND c.company_department_id = ', v_id_department), ''),
                                    ' AND c.update_status_closed_resolved >= DATE_FORMAT(CURDATE(), ''%Y-%m-01'')
                                    
                                    UNION ALL
                                    
                                    SELECT ''Tickets_Closed'' as type, 
                                        CASE
                                            WHEN EXTRACT(DAY FROM t.update_status_closed_resolved) BETWEEN 1 AND 7 THEN 1
                                            WHEN EXTRACT(DAY FROM t.update_status_closed_resolved) BETWEEN 8 AND 14 THEN 2
                                            WHEN EXTRACT(DAY FROM t.update_status_closed_resolved) BETWEEN 15 AND 21 THEN 3
                                            WHEN EXTRACT(DAY FROM t.update_status_closed_resolved) > 21 THEN 4
                                        END AS week
                                    from ticket t
                                    where t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL
                                    AND t.status != ''CANCELED'' AND t.status != ''MERGED''',
                                    IF(v_id_department > 0, CONCAT(' AND t.company_department_id = ', v_id_department), ''),
                                    ' AND t.update_status_closed_resolved >= DATE_FORMAT(CURDATE(), ''%Y-%m-01'')
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

            DROP procedure IF EXISTS `pro_dashboard_department_tickets_chats_resolved_by_attendant`;
            CREATE DEFINER=CURRENT_USER PROCEDURE `pro_dashboard_department_tickets_chats_resolved_by_attendant`(IN pIdCompany INT, IN pIdDepartment INT, IN pFilterPeriod ENUM('WEEK', 'MONTH', 'YEAR'), IN pIdsAttendants VARCHAR(500), IN pIsDebugger INT)
            BEGIN
            DECLARE v_id_department INTEGER;
            SET v_id_department = COALESCE(pIdDepartment, 0);
                
            SET @SQL_Final = IF(pFilterPeriod != 'MONTH',
                        CONCAT(' SELECT user_auth_id, name, ', IF(pFilterPeriod = 'WEEK', 'day_, ', 'month_year, '), ' year, month, day, SUM(count) AS count FROM (
                                SELECT
                                    ''Chat'' AS `type`,
                                    cu.user_auth_id,
                                    u.name, ',
                                    IF(pFilterPeriod = 'WEEK', ' DATE_FORMAT(c.update_status_closed_resolved, \"%d-%m\") as day_, ',
                                        ' DATE_FORMAT(c.update_status_closed_resolved, \"%m-%Y\") as month_year, '),
                                    'EXTRACT(YEAR FROM c.update_status_closed_resolved) AS year,
                                    EXTRACT(MONTH FROM c.update_status_closed_resolved) AS month,
                                    EXTRACT(DAY FROM c.update_status_closed_resolved) AS day,
                                    COUNT(*) AS count
                                FROM chat c
                                JOIN company_user_company_department cucd ON c.comp_user_comp_depart_id_current = cucd.id
                                JOIN company_user cu ON cucd.company_user_id = cu.id
                                JOIN user_auth u on cu.user_auth_id = u.id
                                WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL
                                AND c.status != ''CANCELED''',
                                IF(v_id_department > 0, CONCAT(' AND c.company_department_id = ', v_id_department), ''),
                                ' AND cu.user_auth_id IN (', pIdsAttendants, ')',
                                IF(pFilterPeriod = 'WEEK', ' AND c.update_status_closed_resolved between DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())-1 DAY) AND CURDATE() + INTERVAL 1 DAY ',
                                    ' AND EXTRACT(YEAR FROM c.update_status_closed_resolved) = EXTRACT(YEAR FROM CURDATE()) '),
                                ' GROUP BY 1, 2, 4
        
                                UNION
        
                                SELECT
                                    ''Ticket'' AS `type`,
                                    cu.user_auth_id,
                                    u.name, ',
                                    IF(pFilterPeriod = 'WEEK', ' DATE_FORMAT(t.update_status_closed_resolved, \"%d-%m\") as day_, ',
                                        ' DATE_FORMAT(t.update_status_closed_resolved, \"%m-%Y\") as month_year, '),
                                    'EXTRACT(YEAR FROM t.update_status_closed_resolved) AS year,
                                    EXTRACT(MONTH FROM t.update_status_closed_resolved) AS month,
                                    EXTRACT(DAY FROM t.update_status_closed_resolved) AS day,
                                    COUNT(*) AS count
                                FROM ticket t
                                JOIN user_ticket ut on t.id = ut.ticket_id
                                JOIN company_user cu ON ut.company_user_id = cu.id
                                -- JOIN company_user_company_department cucd ON cu.id = cucd.company_user_id
                                -- JOIN company_department cd on cucd.company_department_id = cd.id
                                JOIN user_auth u on cu.user_auth_id = u.id
                                WHERE t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL
                                AND t.status != ''CANCELED'' AND t.status != ''MERGED''',
                                IF(v_id_department > 0, CONCAT(' AND t.company_department_id = ', v_id_department), ''),
                                ' AND cu.user_auth_id IN (', pIdsAttendants, ')',
                                IF(pFilterPeriod = 'WEEK', ' AND t.update_status_closed_resolved BETWEEN DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())-1 DAY) AND CURDATE() + INTERVAL 1 DAY ',
                                    ' AND EXTRACT(YEAR FROM t.update_status_closed_resolved) = EXTRACT(YEAR FROM curdate()) '),
                                ' GROUP BY 1, 2, 4
                            ) sub
                            GROUP BY 1, 3;'
                        ),
                        CONCAT(' SELECT user_auth_id, week, count(*) as count
                            FROM ( 
                                SELECT ''Chat'' as type,
                                    cu.user_auth_id,
                                    u.name,
                                    CASE
                                        WHEN EXTRACT(DAY FROM c.update_status_closed_resolved) BETWEEN 1 AND 7 THEN 1
                                        WHEN EXTRACT(DAY FROM c.update_status_closed_resolved) BETWEEN 8 AND 14 THEN 2
                                        WHEN EXTRACT(DAY FROM c.update_status_closed_resolved) BETWEEN 15 AND 21 THEN 3
                                        WHEN EXTRACT(DAY FROM c.update_status_closed_resolved) > 21 THEN 4
                                    END AS week
                                FROM chat c
                                JOIN company_user_company_department cucd ON c.comp_user_comp_depart_id_current = cucd.id
                                JOIN company_user cu ON cucd.company_user_id = cu.id
                                JOIN user_auth u on cu.user_auth_id = u.id
                                WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL
                                AND c.status != ''CANCELED''',
                                IF(v_id_department > 0, CONCAT(' AND c.company_department_id = ', v_id_department), ''),
                                ' AND cu.user_auth_id IN (', pIdsAttendants, ')',
                                ' AND c.update_status_closed_resolved >= DATE_FORMAT(CURDATE(), ''%Y-%m-01'')
                                
                                UNION ALL
                                
                                SELECT ''Ticket'' as type, 
                                    cu.user_auth_id,
                                    u.name,
                                    CASE
                                        WHEN EXTRACT(DAY FROM t.update_status_closed_resolved) BETWEEN 1 AND 7 THEN 1
                                        WHEN EXTRACT(DAY FROM t.update_status_closed_resolved) BETWEEN 8 AND 14 THEN 2
                                        WHEN EXTRACT(DAY FROM t.update_status_closed_resolved) BETWEEN 15 AND 21 THEN 3
                                        WHEN EXTRACT(DAY FROM t.update_status_closed_resolved) > 21 THEN 4
                                    END AS week
                                FROM ticket t
                                JOIN user_ticket ut on t.id = ut.ticket_id
                                JOIN company_user cu ON ut.company_user_id = cu.id
                                JOIN user_auth u on cu.user_auth_id = u.id
                                WHERE t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL
                                AND t.status != ''CANCELED'' AND t.status != ''MERGED''', 
                                IF(v_id_department > 0, CONCAT(' AND t.company_department_id = ', v_id_department), ''),
                                ' AND cu.user_auth_id IN (', pIdsAttendants, ')',
                                ' AND t.update_status_closed_resolved >= DATE_FORMAT(CURDATE(), ''%Y-%m-01'')
                            ) sub
                            GROUP BY 1, 2
                            ORDER BY week, type;'
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

                DROP procedure IF EXISTS `pro_dashboard_department_totals_and_percentages`;
                CREATE DEFINER=CURRENT_USER PROCEDURE `pro_dashboard_department_totals_and_percentages`(IN pIdCompany INT, IN pIdDepartment INT, IN pIsDebugger INT)
                BEGIN

                DECLARE v_id_department INTEGER;
                SET v_id_department = COALESCE(pIdDepartment, 0);
                    
                SET @SQL_Final = 
                    CONCAT('SELECT
                        `type`, 
                        count, 
                        IFNULL( ROUND((in_progress * 100 / (in_progress + opened))), 0) as perc_em_atendimento,
                        IFNULL( ROUND((closed * 100 / count)), 0) as perc_fechados
                    FROM (
                        SELECT
                            ''Chat'' AS `type`,
                            COUNT(id) AS count,
                            SUM(IF(status IN (''CLOSED'', ''RESOLVED''), 1, 0)) AS closed,
                            SUM(IF(status = ''OPENED'', 1, 0)) AS opened,
                            SUM(IF(status = ''IN_PROGRESS'', 1, 0)) AS in_progress
                        FROM chat
                        WHERE company_id = ', pIdCompany, ' AND deleted_at IS NULL
                        AND status != ''CANCELED'' ',
                        IF(v_id_department > 0, CONCAT(' AND company_department_id = ', v_id_department), ''),

                        ' UNION
                        
                        SELECT
                            ''Ticket'' AS `type`,
                            COUNT(id) AS count,
                            SUM(IF(status IN (''CLOSED'', ''RESOLVED''), 1, 0)) AS closed,
                            SUM(IF(status = ''OPENED'', 1, 0)) AS opened,
                            SUM(IF(status = ''IN_PROGRESS'', 1, 0)) AS in_progress
                        FROM ticket
                        WHERE company_id = ', pIdCompany, ' AND deleted_at IS NULL
                        AND status != ''CANCELED'' AND status != ''MERGED'' ',
                        IF(v_id_department > 0, CONCAT(' AND company_department_id = ', v_id_department), ''),
                    ') sub
                    GROUP BY 1;');

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
                    ' AND t.status != ''CANCELED'' AND t.status != ''MERGED''
                ) sub;');
            
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
                        ' AND t.status != ''CANCELED'' AND t.status != ''MERGED'' 
                    ) sub_ticket;');

            IF (pIsDebugger = 0) THEN
                PREPARE stmt FROM @SQL_Final;
                EXECUTE stmt;
                DEALLOCATE PREPARE stmt;
            ELSE
                SELECT @SQL_Final;
            END IF;
        END;

        DROP procedure IF EXISTS `pro_get_statistics_chats_tickets_for_attendant`;
        CREATE DEFINER=CURRENT_USER PROCEDURE `pro_get_statistics_chats_tickets_for_attendant`(IN pType ENUM('CHAT', 'TICKET'), IN pIdCompany INT, IN pIdDepartment INT, IN pInitialDate DATETIME, IN pFinalDate DATETIME, IN pIsDebugger INT)
            COMMENT 'Busca a qtd de chats/tickets abertos/finalizados/cancelados, tempo médio de fila, média de avaliação do atendente e do serviço'
        BEGIN
                    
            DECLARE v_id_department INTEGER;
            SET v_id_department = COALESCE(pIdDepartment, 0);

            SET @SQL_Final = 
                IF(pType = 'CHAT',
                    CONCAT('SELECT
                            user_auth.id, user_auth.name as attendant, user_auth.email, (company_user.deleted_at IS NOT NULL) AS user_removed, company_user.last_login, company_user.id as company_user_id, 
                            SUM(COALESCE(sub_opened.opened, 0)) as opened,
                            (SUM(COALESCE(sub_work_times.interation, 0)) + SUM(COALESCE(sub_atend_curr.interation, 0))) AS interation, SUM(COALESCE(sub_chat.finished, 0)) AS finished,
                            SUM(COALESCE(sub_chat.canceled, 0)) AS canceled, SUM(COALESCE(sub_chat.moved_to_ticket, 0)) AS moved_to_ticket,
                            CAST(AVG(sub_chat.avg_queue_time) AS DECIMAL(10, 2)) AS avg_queue_time,
                            CAST(AVG(sub_avaliation.media_stars_atendent) AS DECIMAL(10, 2)) AS media_stars_atendent,
                            CAST(AVG(sub_avaliation.media_stars_service) AS DECIMAL(10, 2)) AS media_stars_service
                        FROM user_auth
                        INNER JOIN company_user ON user_auth.id = company_user.user_auth_id
                        INNER JOIN company_user_company_department AS cucd ON company_user.id = cucd.company_user_id
                        LEFT JOIN
                        (
                            SELECT c.comp_user_comp_depart_id_current,
                            SUM(IF(c.update_status_closed_resolved BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, ''', 1, 0)) AS finished,
                            SUM(IF(c.update_status_canceled BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, ''', 1, 0)) AS canceled,
                            SUM(IF(t.created_at > c.created_at AND t.created_at BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, ''', 1, 0)) AS moved_to_ticket,
                            AVG(IF(c.created_at BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, ''', c.queue_time, NULL)) AS avg_queue_time
                            FROM chat c
                            LEFT JOIN ticket t ON c.ticket_id = t.id
                            WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL ',
                            IF(v_id_department > 0, CONCAT(' AND c.company_department_id = ', v_id_department), ''),
                            ' GROUP BY c.comp_user_comp_depart_id_current
                        ) sub_chat ON cucd.id = sub_chat.comp_user_comp_depart_id_current
                        
                        LEFT JOIN
                        (
                            SELECT company_user_company_department_id, COUNT(*) interation FROM (
                                SELECT cwt.chat_id, cwt.company_user_company_department_id
                                FROM chat c
                                JOIN chat_working_times cwt ON c.id = cwt.chat_id
                                WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL
                                AND cwt.ticket_id IS NULL
                                AND c.update_status_in_progress BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, '''
                                GROUP BY cwt.chat_id, cwt.company_user_company_department_id
                            ) sub_interno
                            GROUP BY 1
                        ) sub_work_times ON cucd.id = sub_work_times.company_user_company_department_id
                        
                        LEFT JOIN
                        (
                            SELECT c.comp_user_comp_depart_id_current, COUNT(*) interation
                            FROM chat c
                            WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL
                            AND c.ticket_id IS NULL
                            AND c.update_status_in_progress < ''2022-04-11 11:26:59''
                            AND c.update_status_in_progress BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, '''
                            GROUP BY c.comp_user_comp_depart_id_current
                        ) sub_atend_curr ON cucd.id = sub_atend_curr.comp_user_comp_depart_id_current

                        LEFT JOIN
                        (
                            SELECT company_user_company_department_id, COUNT(*) opened
                            FROM chat c
                            JOIN chat_history ch ON c.id = ch.chat_id
                            WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL
                            AND c.ticket_id IS NULL
                            AND c.update_status_in_progress BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, '''
                            AND ch.content = ''bs-joined-the-chat''
                            GROUP BY ch.company_user_company_department_id
                        ) sub_opened ON cucd.id = sub_opened.company_user_company_department_id

                        LEFT JOIN
                        (
                            SELECT c.comp_user_comp_depart_id_current,
                                CAST(AVG(IF(c.created_at BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, ''', av.stars_atendent, NULL)) AS DECIMAL(10, 2)) AS media_stars_atendent,
                                CAST(AVG(IF(c.created_at BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, ''', av.stars_service, NULL)) AS DECIMAL(10, 2)) AS media_stars_service
                            FROM avaliation av
                            JOIN chat c ON av.chat_id = c.id
                            WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL ',
                            IF(v_id_department > 0, CONCAT(' AND c.company_department_id = ', v_id_department), ''),
                            ' AND c.status != ''CANCELED''', 
                            ' GROUP BY c.comp_user_comp_depart_id_current 
                        ) sub_avaliation ON cucd.id = sub_avaliation.comp_user_comp_depart_id_current', 
                        ' WHERE company_id = ', pIdCompany, 
                        IF(v_id_department > 0, CONCAT(' AND cucd.company_department_id = ', v_id_department), ''),
                        ' GROUP BY user_auth.id ORDER BY user_auth.name;'),
                        CONCAT('SELECT
                            user_auth.id, user_auth.name as attendant, user_auth.email, (company_user.deleted_at IS NOT NULL) AS user_removed, company_user.last_login, company_user.id as company_user_id, 
                            SUM(COALESCE(sub_opened.opened, 0)) as opened,
                            (SUM(COALESCE(sub_work_times.interation, 0)) + SUM(COALESCE(sub_atend_curr.interation, 0))) AS interation, SUM(COALESCE(sub_ticket.finished, 0)) AS finished,
                            SUM(COALESCE(sub_ticket.canceled, 0)) AS canceled, 
                            CAST(AVG(sub_ticket.avg_queue_time) AS DECIMAL(10, 2)) AS avg_queue_time,
                            CAST(AVG(sub_avaliation.media_stars_atendent) AS DECIMAL(10, 2)) AS media_stars_atendent,
                            CAST(AVG(sub_avaliation.media_stars_service) AS DECIMAL(10, 2)) AS media_stars_service
                        FROM user_auth
                        INNER JOIN company_user ON user_auth.id = company_user.user_auth_id
                        INNER JOIN company_user_company_department AS cucd ON company_user.id = cucd.company_user_id

                        LEFT JOIN
                        (
                            SELECT c.comp_user_comp_depart_id_current,
                            SUM(IF(t.update_status_in_progress BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, ''', 1, 0)) AS opened,
                            SUM(IF(t.update_status_closed_resolved BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, ''', 1, 0)) AS finished,
                            SUM(IF(t.update_status_canceled BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, ''', 1, 0)) AS canceled,
                            AVG(IF(t.created_at BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, ''', t.queue_time, NULL)) AS avg_queue_time
                            FROM ticket t
                            JOIN chat c ON t.id = c.ticket_id
                            WHERE t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL AND t.status != ''MERGED'' ',
                            IF(v_id_department > 0, CONCAT(' AND c.company_department_id = ', v_id_department), ''),
                            ' GROUP BY c.comp_user_comp_depart_id_current
                        ) sub_ticket ON cucd.id = sub_ticket.comp_user_comp_depart_id_current
                        
                        LEFT JOIN
                        (
                            SELECT company_user_company_department_id, COUNT(*) interation FROM (
                                SELECT cwt.ticket_id, cwt.company_user_company_department_id
                                FROM chat c
                                JOIN ticket t ON c.ticket_id = t.id
                                JOIN chat_working_times cwt ON c.id = cwt.chat_id
                                WHERE t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL AND t.status != ''MERGED'' 
                                AND cwt.ticket_id IS NOT NULL
                                AND t.update_status_in_progress BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, '''
                                GROUP BY cwt.ticket_id, cwt.company_user_company_department_id
                            ) sub_interno
                            GROUP BY 1
                        ) sub_work_times ON cucd.id = sub_work_times.company_user_company_department_id

                        LEFT JOIN
                        (
                            SELECT c.comp_user_comp_depart_id_current, COUNT(*) interation
                            FROM chat c
                            JOIN ticket t ON c.ticket_id = t.id
                            WHERE t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL AND t.status != ''MERGED'' 
                            AND t.update_status_in_progress < ''2022-04-11 11:26:59''
                            AND t.update_status_in_progress BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, '''
                            GROUP BY c.comp_user_comp_depart_id_current
                        ) sub_atend_curr ON cucd.id = sub_atend_curr.comp_user_comp_depart_id_current

                        LEFT JOIN
                        (
                            SELECT ch.company_user_company_department_id, COUNT(*) opened
                            FROM chat c
                            JOIN ticket t ON c.ticket_id = t.id
                            JOIN chat_history ch ON c.id = ch.chat_id
                            WHERE t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL AND t.status != ''MERGED'' 
                            AND t.update_status_in_progress BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, '''
                            AND ch.content = ''bs-joined-the-ticket''
                            GROUP BY ch.company_user_company_department_id
                        ) sub_opened ON cucd.id = sub_opened.company_user_company_department_id

                        LEFT JOIN
                        (
                            SELECT c.comp_user_comp_depart_id_current,
                                CAST(AVG(IF(c.created_at BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, ''', av.stars_atendent, NULL)) AS DECIMAL(10, 2)) AS media_stars_atendent,
                                CAST(AVG(IF(c.created_at BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, ''', av.stars_service, NULL)) AS DECIMAL(10, 2)) AS media_stars_service
                            FROM avaliation av
                            JOIN ticket t ON av.ticket_id = t.id
                            JOIN chat c ON t.id = c.ticket_id
                            WHERE t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL AND t.status != ''MERGED'' ',
                            IF(v_id_department > 0, CONCAT(' AND t.company_department_id = ', v_id_department), ''),
                            ' AND t.status != ''CANCELED''', 
                            ' GROUP BY c.comp_user_comp_depart_id_current 
                        ) sub_avaliation ON cucd.id = sub_avaliation.comp_user_comp_depart_id_current', 
                        ' WHERE company_id = ', pIdCompany, 
                        IF(v_id_department > 0, CONCAT(' AND cucd.company_department_id = ', v_id_department), ''),
                        ' GROUP BY user_auth.id ORDER BY user_auth.name;')
                    );
                        
                IF (pIsDebugger = 0) THEN
                    PREPARE stmt FROM @SQL_Final;
                    EXECUTE stmt;
                    DEALLOCATE PREPARE stmt;
                ELSE
                    SELECT @SQL_Final;
                END IF;
            END;

            DROP procedure IF EXISTS `pro_real_time_company_tickets_average_time_queue_and_service`;
            CREATE DEFINER=CURRENT_USER PROCEDURE `pro_real_time_company_tickets_average_time_queue_and_service`(IN pIdCompany INT, IN pIdDepartment INT, IN pInitialDate DATETIME, IN pFinalDate DATETIME)
                BEGIN
                DECLARE v_id_department INTEGER;
                SET v_id_department = COALESCE(pIdDepartment, 0);
                SET @SQL_Final =
                    CONCAT('SELECT
                        COUNT(*) count,
                        SUM(IFNULL(queue_time, TIMESTAMPDIFF(SECOND, created_at, UTC_TIMESTAMP))) AS sum_queue_time,
                        ROUND(AVG(IFNULL(queue_time, TIMESTAMPDIFF(SECOND, created_at, UTC_TIMESTAMP)))) AS avg_queue_time,
                        ROUND(AVG(IFNULL(service_time, 0))) AS avg_service_time
                    FROM ticket
                    WHERE company_id = ', pIdCompany, ' AND deleted_at IS NULL AND t.status != ''MERGED'' ',
                    IF(v_id_department > 0, CONCAT(' AND company_department_id = ', v_id_department), ''),
                    ' AND created_at >= ''', pInitialDate, '''',
                    ' AND created_at <= ''', pFinalDate, ''''
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
        //
    }
}
