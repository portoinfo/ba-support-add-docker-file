<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NewProGetAttendantResponseTimes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
            DROP procedure IF EXISTS `pro_get_attendant_response_times`;

            CREATE DEFINER=CURRENT_USER PROCEDURE `pro_get_attendant_response_times`(IN pType ENUM('CHAT', 'TICKET'), IN pIdCompany INT, IN pIdDepartment INT, IN pFilterPeriod ENUM('LAST_24_HOURS', 'LAST_7_DAYS', 'LAST_30_DAYS', 'LAST_365_DAYS'))
            COMMENT 'Tempo médio de resposta do atendente para o cliente'
            BEGIN
                DECLARE v_id_department INTEGER;
                SET v_id_department = COALESCE(pIdDepartment, 0);

                SET @SQL_Final = CONCAT('SELECT next_created_by as user_auth_id, CAST(AVG(media) AS DECIMAL(10, 2)) as average FROM (
                        SELECT sub.*, AVG(IF(next_created_at = created_at, NULL, TIME_TO_SEC(TIMEDIFF(next_created_at, created_at)))) media
                        FROM
                        (
                            SELECT h.id, h.chat_id, h.company_user_company_department_id, c.status, c.company_department_id, h.type, h.created_at,
                            LEAD(h.created_by) OVER w as next_created_by,
                            LEAD(h.created_at) OVER w as next_created_at, LEAD(h.company_user_company_department_id) OVER w as next_company_user_company_department_id
                            FROM chat_history h
                            JOIN chat c ON h.chat_id = c.id
                            WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL ',
                            IF(v_id_department > 0, CONCAT(' AND c.company_department_id = ', v_id_department), ''),
                            ' AND h.type IN (''TEXT'', ''IMAGE'')', 
                            -- chats sem tickets atrelados
                            IF(pType = 'CHAT', ' AND c.ticket_id IS NULL', ''),
                            -- chats com tickets atrelados
                            IF(pType = 'TICKET', ' AND c.ticket_id IS NOT NULL ', ''), 
                            ' AND h.created_at >= DATE_SUB(CURDATE(), INTERVAL ',
                                IF(pFilterPeriod = 'LAST_24_HOURS', ' 24 HOUR)', IF(pFilterPeriod = 'LAST_7_DAYS', ' 7 DAY)',
                                    IF(pFilterPeriod = 'LAST_30_DAYS', ' 30 DAY)', IF(pFilterPeriod = 'LAST_365_DAYS', ' 1 YEAR)', '')))), 
                            ' WINDOW w AS (PARTITION BY h.chat_id ORDER BY h.chat_id, h.id)
                        ) sub
                        WHERE sub.next_created_at IS NOT NULL
                        AND COALESCE(sub.company_user_company_department_id, 0) != COALESCE(sub.next_company_user_company_department_id, 0)
                        AND sub.company_user_company_department_id IS NULL AND sub.next_company_user_company_department_id IS NOT NULL
                        GROUP BY sub.chat_id, sub.next_created_by
                    ) sub2
                    GROUP BY sub2.next_created_by;');

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
        DB::unprepared("DROP procedure IF EXISTS `pro_get_attendant_response_times`;");
    }
}
