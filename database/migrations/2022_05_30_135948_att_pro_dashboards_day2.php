<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AttProDashboardsDay2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
            DROP procedure IF EXISTS `pro_dashboard_company_indicators`;

            CREATE DEFINER=CURRENT_USER PROCEDURE `pro_dashboard_company_indicators`(IN pIdCompany INT, IN pIsDebugger INT)
            BEGIN
                SET @SQL_Final = 
                    CONCAT('SELECT ''Departments'' AS `type`, COUNT(id) AS Count
                    FROM company_department
                    WHERE company_id = ', pIdCompany, ' AND deleted_at IS NULL AND is_active = 1

                    UNION

                    SELECT ''Groups'' AS `type`, COUNT(id) AS Count
                    FROM user_group
                    WHERE company_id = ', pIdCompany, ' AND deleted_at IS NULL AND is_active = 1

                    UNION

                    SELECT ''Atendents'' AS `type`, COUNT(id) AS Count
                    FROM company_user
                    WHERE company_id = ', pIdCompany, ' AND deleted_at IS NULL AND is_active = 1

                    UNION

                    SELECT ''Clients'' AS `type`, COUNT(user_client_id) AS Count
                    FROM company_user_client
                    WHERE company_id = ', pIdCompany, ' AND deleted_at IS NULL;');

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
                            AND status != ''CANCELED''
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
                            ' AND status != ''CANCELED''
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
                    ' AND status != ''CANCELED''',
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

            DROP procedure IF EXISTS `pro_dashboard_company_chats_average_time_queue_and_service`;

            CREATE DEFINER=CURRENT_USER PROCEDURE `pro_dashboard_company_chats_average_time_queue_and_service`(IN pIdCompany INT, IN pIdDepartment INT, IN pFilterPeriod ENUM('LAST_24_HOURS', 'LAST_7_DAYS', 'LAST_30_DAYS', 'LAST_365_DAYS'), IN pIsDebugger INT)
            BEGIN
                DECLARE v_id_department INTEGER;
                SET v_id_department = COALESCE(pIdDepartment, 0);
                    
                SET @SQL_Final = 
                    CONCAT('SELECT
                        ROUND(AVG(IFNULL(queue_time, 0))) AS avg_queue_time, 
                        ROUND(AVG(IFNULL(service_time, 0))) AS avg_service_time
                    FROM chat
                    WHERE company_id = ', pIdCompany, ' AND deleted_at IS NULL ',
                    IF(v_id_department > 0, CONCAT(' AND company_department_id = ', v_id_department), ''),
                    ' AND status != ''CANCELED''',
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
                        AND t.status != ''CANCELED'' ',
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
                                AND t.status != ''CANCELED''',
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
                                AND t.status != ''CANCELED''',
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
                                    AND t.status != ''CANCELED''',
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
                                    AND t.status != ''CANCELED''',
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
                                    AND t.status != ''CANCELED''',
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
                                    AND t.status != ''CANCELED''', 
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
                        AND status != ''CANCELED'' ',
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


            



        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pro_dashboard_company_indicators');
        Schema::dropIfExists('pro_dashboard_company_occurrences_by_department');
        Schema::dropIfExists('pro_dashboard_company_tickets_average_time_queue_and_service');
        Schema::dropIfExists('pro_dashboard_company_chats_average_time_queue_and_service');
        Schema::dropIfExists('pro_dashboard_department_totals_and_percentages');
        Schema::dropIfExists('pro_dashboard_department_general_average_for_attendances');
        Schema::dropIfExists('pro_dashboard_department_tickets_chats_opened_x_closed');
        Schema::dropIfExists('pro_dashboard_department_tickets_chats_resolved_by_attendant');
    }
}
