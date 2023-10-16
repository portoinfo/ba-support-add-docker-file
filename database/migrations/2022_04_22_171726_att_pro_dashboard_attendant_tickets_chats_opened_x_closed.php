<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AttProDashboardAttendantTicketsChatsOpenedXClosed extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
        DROP procedure IF EXISTS `pro_dashboard_attendant_tickets_chats_opened_x_closed`;

        CREATE DEFINER=CURRENT_USER PROCEDURE `pro_dashboard_attendant_tickets_chats_opened_x_closed`(IN pIdCompany INT, IN pIdDepartment INT, IN pIdAttendant INT, IN pFilterPeriod ENUM('WEEK', 'MONTH', 'YEAR'))
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
                        
            PREPARE stmt FROM @SQL_Final;
            EXECUTE stmt;
            DEALLOCATE PREPARE stmt;
            
            -- select @SQL_Final;                
        END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pro_dashboard_attendant_tickets_chats_opened_x_closed');
    }
}
