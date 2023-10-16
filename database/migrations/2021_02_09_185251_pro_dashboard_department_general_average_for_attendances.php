<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProDashboardDepartmentGeneralAverageForAttendances extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = "
        CREATE DEFINER=CURRENT_USER PROCEDURE `pro_dashboard_department_general_average_for_attendances`(IN pIdCompany INT, IN pIdDepartment INT)
        BEGIN
            DECLARE v_id_department INTEGER;
            SET v_id_department = COALESCE(pIdDepartment, 0);
                
                SELECT
                    -- type, 
                    CAST(AVG(stars_atendent) AS DECIMAL(10, 2)) AS media_stars_atendent, 
                    CAST(AVG(stars_service) AS DECIMAL(10, 2)) AS media_stars_service
                FROM (
                    SELECT
                        'Chat' AS `type`,
                        av.stars_atendent, av.stars_service
                    FROM avaliation av
                    JOIN chat c ON av.chat_id = c.id
                    WHERE c.company_id = pIdCompany AND c.deleted_at IS NULL 
                    AND c.status != 'CANCELED'
                    AND (v_id_department = 0 OR c.company_department_id = v_id_department)
        
                    UNION
        
                    SELECT
                        'Ticket' AS `type`,
                        av.stars_atendent, av.stars_service
                    FROM avaliation av
                    JOIN ticket t ON av.ticket_id = t.id
                    WHERE t.company_id = pIdCompany AND t.deleted_at IS NULL
                    AND t.status != 'CANCELED'
                    AND (v_id_department = 0 OR t.company_department_id = v_id_department)
                ) sub;
                -- GROUP BY type;	
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
        DB::unprepared('DROP procedure IF EXISTS `pro_dashboard_department_general_average_for_attendances`;');
    }
}
