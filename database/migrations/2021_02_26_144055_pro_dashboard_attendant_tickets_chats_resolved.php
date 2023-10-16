<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProDashboardAttendantTicketsChatsResolved extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "
        CREATE DEFINER=CURRENT_USER PROCEDURE `pro_dashboard_attendant_tickets_chats_resolved` (IN pIdCompany INT, IN pIdDepartment INT, IN pIdAttendant INT, IN pFilterPeriod ENUM('WEEK', 'MONTH', 'YEAR'))
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
        DB::unprepared("DROP procedure IF EXISTS `pro_dashboard_attendant_tickets_chats_resolved`;");
    }
}
