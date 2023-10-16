<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NewProGetChatsTicketsWaitingResponsesCustomerAttendant extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
            DROP procedure IF EXISTS `pro_get_chats_tickets_waiting_responses_customer_attendant`;

            CREATE DEFINER=CURRENT_USER PROCEDURE `pro_get_chats_tickets_waiting_responses_customer_attendant`(IN pType ENUM('CHAT', 'TICKET'), IN pIdCompany INT, IN pIdDepartment INT, IN pIdAttendant INT, IN pFilterPeriod ENUM('LAST_24_HOURS', 'LAST_7_DAYS', 'LAST_30_DAYS', 'LAST_365_DAYS'))
            COMMENT 'Busca a qtd de chats/tickets que estão aguardando resposta do cliente ou do atendente'
            BEGIN
                DECLARE v_id_department INTEGER;
                DECLARE v_id_attendant INTEGER;
                SET v_id_department = COALESCE(pIdDepartment, 0);
                SET v_id_attendant = COALESCE(pIdAttendant, 0);
                    
                SET @SQL_Final = IF(pType = 'CHAT', 
                    CONCAT('SELECT chat_id, company_user_company_department_id FROM (
                        SELECT h.id, h.chat_id, h.company_user_company_department_id, c.status, h.type, h.created_at
                        FROM chat_history h
                        JOIN chat c ON h.chat_id = c.id
                        JOIN company_user_company_department cucd ON c.comp_user_comp_depart_id_current = cucd.id
                        WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL ',
                        IF(v_id_department > 0, CONCAT(' AND c.company_department_id = ', v_id_department), ''),
                        IF(v_id_attendant > 0, CONCAT(' AND cucd.company_user_id = ', v_id_attendant), ''),
                        ' AND c.ticket_id IS NULL
                        AND c.status = ''IN_PROGRESS''
                        AND h.type != ''EVENT''
                        AND h.type != ''ROBOT''
                        AND h.type != ''OPEN''
                        AND h.type != ''CLOSE''
                        AND h.created_at >= DATE_SUB(CURDATE(), INTERVAL ',
                            IF(pFilterPeriod = 'LAST_24_HOURS', ' 24 HOUR)', IF(pFilterPeriod = 'LAST_7_DAYS', ' 7 DAY)',
                                IF(pFilterPeriod = 'LAST_30_DAYS', ' 30 DAY)', IF(pFilterPeriod = 'LAST_365_DAYS', ' 1 YEAR)', '')))), 
                        ' ORDER BY h.chat_id, h.id DESC
                        LIMIT 999999999
                    ) sub
                    GROUP BY sub.chat_id;'),
                    CONCAT('SELECT ticket_id, company_user_company_department_id FROM (
                        SELECT h.id, c.ticket_id, h.company_user_company_department_id, t.status, h.type, h.created_at
                        FROM chat_history h
                        JOIN chat c ON h.chat_id = c.id
                        JOIN ticket t ON c.ticket_id = t.id
                        JOIN company_user_company_department cucd ON c.comp_user_comp_depart_id_current = cucd.id
                        WHERE t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL ',
                        IF(v_id_department > 0, CONCAT(' AND t.company_department_id = ', v_id_department), ''),
                        IF(v_id_attendant > 0, CONCAT(' AND cucd.company_user_id = ', v_id_attendant), ''),
                        ' AND t.status = ''IN_PROGRESS''
                        AND h.type != ''EVENT''
                        AND h.type != ''ROBOT''
                        AND h.type != ''OPEN''
                        AND h.type != ''CLOSE''
                        AND h.created_at >= DATE_SUB(CURDATE(), INTERVAL ',
                            IF(pFilterPeriod = 'LAST_24_HOURS', ' 24 HOUR)', IF(pFilterPeriod = 'LAST_7_DAYS', ' 7 DAY)',
                                IF(pFilterPeriod = 'LAST_30_DAYS', ' 30 DAY)', IF(pFilterPeriod = 'LAST_365_DAYS', ' 1 YEAR)', '')))), 
                        ' ORDER BY h.chat_id, h.id DESC
                        LIMIT 999999999
                    ) sub
                    GROUP BY sub.ticket_id;')
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
        DB::unprepared("DROP procedure IF EXISTS `pro_get_chats_tickets_waiting_responses_customer_attendant`;");
    }
}
