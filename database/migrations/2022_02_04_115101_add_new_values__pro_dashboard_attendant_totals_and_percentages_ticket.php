<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddNewValuesProDashboardAttendantTotalsAndPercentagesTicket extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    public function up()
    {
        DB::unprepared("
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
                    WHERE company_id = ', pIdCompany, ' AND deleted_at IS NULL ',
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
        DB::unprepared("DROP procedure IF EXISTS `pro_real_time_company_tickets_average_time_queue_and_service`;");
    }
}
