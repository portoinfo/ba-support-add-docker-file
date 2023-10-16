<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProDashboardDetailsAverageAttendantsAttendances extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "
            CREATE DEFINER=CURRENT_USER PROCEDURE `pro_dashboard_details_average_attendants_attendances` (IN pIdCompany INT, IN pIdAttendant INT, IN pIdDepartment INT, IN pInitialDate DATETIME, IN pFinalDate DATETIME)
            COMMENT 'General average of attendants / attendances.'
            BEGIN
                DECLARE v_id_department INTEGER;
                SET v_id_department = COALESCE(pIdDepartment, 0);
            
                SELECT
                    'Chat' AS `type`,
                    count_positive_atendent,
                    count_negative_atendent,
                    media_stars_atendent,
                    IFNULL(ROUND((count_positive_atendent / (count_positive_atendent + count_negative_atendent)) * 100), 0) approval_percentage_atendent,
                    count_positive_service,
                    count_negative_service,
                    media_stars_service,
                    IFNULL(ROUND((count_positive_service / (count_positive_service + count_negative_service)) * 100), 0) approval_percentage_service
                FROM (
                    SELECT
                        COUNT(DISTINCT c.id) count,
                        SUM(IF(av.stars_atendent = 5, 1, 0)) AS count_positive_atendent,
                        SUM(IF(av.stars_atendent = 0, 1, 0)) AS count_negative_atendent,
                        SUM(IF(av.stars_service = 5, 1, 0)) AS count_positive_service,
                        SUM(IF(av.stars_service = 0, 1, 0)) AS count_negative_service,
                        CAST(AVG(av.stars_atendent) AS DECIMAL(10, 2)) AS media_stars_atendent, 
                        CAST(AVG(av.stars_service) AS DECIMAL(10, 2)) AS media_stars_service
                    FROM avaliation av
                    JOIN chat c ON av.chat_id = c.id
                    JOIN company_user_company_department cucd ON c.comp_user_comp_depart_id_current = cucd.id
                    JOIN company_user cu ON cucd.company_user_id = cu.id
                    WHERE c.company_id = pIdCompany AND c.deleted_at IS NULL
                    AND (v_id_department = 0 OR c.company_department_id = v_id_department)
                    AND cu.user_auth_id = pIdAttendant
                    AND (pInitialDate IS NULL OR c.created_at >= pInitialDate)
                    AND (pFinalDate IS NULL OR DATE(c.created_at) <= pFinalDate)
                    AND c.status != 'CANCELED'
                    AND c.ticket_id IS NULL
                ) sub
                
                UNION
                
                SELECT
                    'Ticket' AS `type`,
                    count_positive_atendent,
                    count_negative_atendent,
                    media_stars_atendent,
                    IFNULL(ROUND((count_positive_atendent / (count_positive_atendent + count_negative_atendent)) * 100), 0) approval_percentage_atendent,
                    count_positive_service,
                    count_negative_service,
                    media_stars_service,
                    IFNULL(ROUND((count_positive_service / (count_positive_service + count_negative_service)) * 100), 0) approval_percentage_service
                FROM (
                    SELECT
                        COUNT(DISTINCT t.id) count,
                        SUM(IF(av.stars_atendent = 5, 1, 0)) AS count_positive_atendent,
                        SUM(IF(av.stars_atendent = 0, 1, 0)) AS count_negative_atendent,
                        SUM(IF(av.stars_service = 5, 1, 0)) AS count_positive_service,
                        SUM(IF(av.stars_service = 0, 1, 0)) AS count_negative_service,
                        CAST(AVG(av.stars_atendent) AS DECIMAL(10, 2)) AS media_stars_atendent, 
                        CAST(AVG(av.stars_service) AS DECIMAL(10, 2)) AS media_stars_service
                    FROM avaliation av
                    JOIN ticket t ON av.ticket_id = t.id
                    JOIN user_ticket ut on t.id = ut.ticket_id
                    JOIN company_user cu ON ut.company_user_id = cu.id
                    WHERE t.company_id = pIdCompany AND t.deleted_at IS NULL
                    AND (v_id_department = 0 OR t.company_department_id = v_id_department)
                    AND cu.user_auth_id = pIdAttendant
                    AND (pInitialDate IS NULL OR t.created_at >= pInitialDate)
                    AND (pFinalDate IS NULL OR DATE(t.created_at) <= pFinalDate)
                    AND t.status != 'CANCELED'
                ) sub;
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
        DB::unprepared("DROP procedure IF EXISTS `pro_dashboard_details_average_attendants_attendances`;");
    }
}
