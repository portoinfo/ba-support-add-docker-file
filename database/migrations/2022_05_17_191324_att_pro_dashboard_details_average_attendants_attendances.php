<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AttProDashboardDetailsAverageAttendantsAttendances extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
        DROP procedure IF EXISTS `pro_dashboard_details_average_attendants_attendances`;

        CREATE DEFINER=CURRENT_USER PROCEDURE `pro_dashboard_details_average_attendants_attendances`(IN pIdCompany INT, IN pIdAttendant INT, IN pIdDepartment INT, IN pInitialDate DATETIME, IN pFinalDate DATETIME)
            COMMENT 'General average of attendants / attendances.'
        BEGIN
            DECLARE v_id_department INTEGER;
            SET v_id_department = COALESCE(pIdDepartment, 0);
        
            SELECT
                'Chat' AS `type`,
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
                WHERE t.company_id = pIdCompany AND t.deleted_at IS NULL
                AND (v_id_department = 0 OR t.company_department_id = v_id_department)
                AND cu.user_auth_id = pIdAttendant
                AND (pInitialDate IS NULL OR t.created_at >= pInitialDate)
                AND (pFinalDate IS NULL OR DATE(t.created_at) <= pFinalDate)
                AND t.status != 'CANCELED'
            ) sub;
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
        Schema::dropIfExists('pro_times_of_attendants_in_service');
    }
}
