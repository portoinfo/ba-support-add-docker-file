<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProDashboardDepartmentTicketsChatsResolvedByAttendant extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "
        CREATE DEFINER=CURRENT_USER PROCEDURE `pro_dashboard_department_tickets_chats_resolved_by_attendant` (IN pIdCompany INT, IN pIdDepartment INT, IN pFilterPeriod ENUM('WEEK', 'MONTH', 'YEAR'), IN pIdsAttendants VARCHAR(500))
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
                                IF(pFilterPeriod = 'WEEK', ' AND c.update_status_closed_resolved between DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())-1 DAY) AND CURDATE() ',
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
                                IF(pFilterPeriod = 'WEEK', ' AND t.update_status_closed_resolved BETWEEN DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())-1 DAY) AND CURDATE() ',
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
            PREPARE stmt FROM @SQL_Final;
            EXECUTE stmt;
            DEALLOCATE PREPARE stmt;
            
            -- select @SQL_Final;                
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
        DB::unprepared('DROP procedure IF EXISTS `pro_dashboard_department_tickets_chats_resolved_by_attendant`;');
    }
}
