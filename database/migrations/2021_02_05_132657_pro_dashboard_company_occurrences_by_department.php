<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProDashboardCompanyOccurrencesByDepartment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "
        CREATE DEFINER=CURRENT_USER PROCEDURE `pro_dashboard_company_occurrences_by_department`(IN pIdCompany INT, IN pIdDepartment INT, IN pFilterPeriod ENUM('WEEK', 'MONTH', 'YEAR'))
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
                        IF(pFilterPeriod = 'WEEK', ' AND created_at BETWEEN DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())-1 DAY) AND CURDATE() ',
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
                        IF(pFilterPeriod = 'WEEK', ' AND created_at BETWEEN DATE_SUB(CURDATE(), INTERVAL DAYOFWEEK(CURDATE())-1 DAY) AND CURDATE() ',
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
        DB::unprepared('DROP procedure IF EXISTS `pro_dashboard_company_occurrences_by_department`;');
    }
}
