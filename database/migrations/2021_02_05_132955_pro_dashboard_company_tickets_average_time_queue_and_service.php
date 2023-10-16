<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ProDashboardCompanyTicketsAverageTimeQueueAndService extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "
        CREATE DEFINER=CURRENT_USER PROCEDURE `pro_dashboard_company_tickets_average_time_queue_and_service`(IN pIdCompany INT, IN pFilterPeriod ENUM('LAST_24_HOURS', 'LAST_7_DAYS', 'LAST_30_DAYS', 'LAST_365_DAYS'))
        BEGIN
            SET @SQL_Final = 
                CONCAT('SELECT
                    ROUND(AVG(IFNULL(queue_time, 0))) AS avg_queue_time, 
                    ROUND(AVG(IFNULL(service_time, 0))) AS avg_service_time
                FROM ticket
                WHERE company_id = ', pIdCompany, ' AND deleted_at IS NULL
                AND status != ''CANCELED''',
                IF(pFilterPeriod = 'LAST_24_HOURS', ' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 24 HOUR);',
                    IF(pFilterPeriod = 'LAST_7_DAYS', ' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY);',
                        IF(pFilterPeriod = 'LAST_30_DAYS', ' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY);',
                            IF(pFilterPeriod = 'LAST_365_DAYS', ' AND created_at >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR);', '')
                        )
                    )
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
        DB::unprepared("DROP procedure IF EXISTS `pro_dashboard_company_tickets_average_time_queue_and_service`;");
    }
}
