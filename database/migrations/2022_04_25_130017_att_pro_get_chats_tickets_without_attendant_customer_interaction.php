<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AttProGetChatsTicketsWithoutAttendantCustomerInteraction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
        DROP procedure IF EXISTS `pro_get_chats_tickets_without_attendant_customer_interaction`;
        
        CREATE DEFINER=CURRENT_USER PROCEDURE `pro_get_chats_tickets_without_attendant_customer_interaction`(IN pType ENUM('CHAT', 'TICKET'), IN pIdCompany INT, IN pIdDepartment INT, IN pIdAttendant INT, IN pFilterPeriod ENUM('LAST_24_HOURS', 'LAST_7_DAYS', 'LAST_30_DAYS', 'LAST_365_DAYS'))
            COMMENT 'Busca a qtd de chats/ticketS sem interação do atendente/cliente'
        BEGIN
            DECLARE v_id_department INTEGER;
            DECLARE v_id_attendant INTEGER;
            SET v_id_department = COALESCE(pIdDepartment, 0);
            SET v_id_attendant = COALESCE(pIdAttendant, 0);
                
            SET @SQL_Final = IF(pType = 'CHAT', 
                CONCAT('SELECT sub.chat_id, COUNT(DISTINCT id_hist1) AS hist_atend, COUNT(DISTINCT id_hist2) AS hist_client FROM (
                    SELECT sub2.*, sub_atend.*, sub_client.* FROM (
                        SELECT * FROM (
                            SELECT h.id as id_hist, h.chat_id, h.company_user_company_department_id, c.status, h.type, h.content, h.created_at
                            FROM chat_history h
                            JOIN chat c ON h.chat_id = c.id
                            JOIN company_user_company_department cucd ON c.comp_user_comp_depart_id_current = cucd.id
                            WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL ',
                            IF(v_id_department > 0, CONCAT(' AND c.company_department_id = ', v_id_department), ''),
                            IF(v_id_attendant > 0, CONCAT(' AND cucd.company_user_id = ', v_id_attendant), ''),
                            ' AND c.ticket_id IS NULL
                            AND c.status = ''IN_PROGRESS''
                            AND h.created_at >= DATE_SUB(CURDATE(), INTERVAL ', 
                            IF(pFilterPeriod = 'LAST_24_HOURS', ' 24 HOUR)', IF(pFilterPeriod = 'LAST_7_DAYS', ' 7 DAY)',
                                IF(pFilterPeriod = 'LAST_30_DAYS', ' 30 DAY)', IF(pFilterPeriod = 'LAST_365_DAYS', ' 1 YEAR)', '')))), 
                            ' AND h.content IN (''bs-joined-the-chat'', ''bs-transferred-the-chat-to-another-agent'', ''bs-took-over-the-chat'')
                            ORDER BY h.chat_id, h.id DESC
                            LIMIT 999999999
                        ) sub1
                        GROUP BY sub1.chat_id
                    ) sub2
                    LEFT JOIN
                    (
                        SELECT h.id as id_hist1, h.chat_id as chat_id1, c.ticket_id as ticket_id1, h.company_user_company_department_id as company_user_company_department_id1,
                            h.type as type1, h.content as content1
                        FROM chat_history h
                        JOIN chat c ON h.chat_id = c.id
                        JOIN company_user_company_department cucd ON c.comp_user_comp_depart_id_current = cucd.id
                        WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL ',
                        IF(v_id_department > 0, CONCAT(' AND c.company_department_id = ', v_id_department), ''),
                        IF(v_id_attendant > 0, CONCAT(' AND cucd.company_user_id = ', v_id_attendant), ''),
                        ' AND c.ticket_id IS NULL
                        AND c.status = ''IN_PROGRESS''
                        AND h.created_at >= DATE_SUB(CURDATE(), INTERVAL ', 
                            IF(pFilterPeriod = 'LAST_24_HOURS', ' 24 HOUR)', IF(pFilterPeriod = 'LAST_7_DAYS', ' 7 DAY)',
                                IF(pFilterPeriod = 'LAST_30_DAYS', ' 30 DAY)', IF(pFilterPeriod = 'LAST_365_DAYS', ' 1 YEAR)', '')))), 
                        ' AND h.type = ''TEXT'' AND h.company_user_company_department_id IS NOT NULL
                    ) sub_atend ON sub2.chat_id = sub_atend.chat_id1 AND sub_atend.id_hist1 > sub2.id_hist
                    LEFT JOIN
                    (
                        SELECT h.id as id_hist2, h.chat_id as chat_id2, c.ticket_id as ticket_id2, h.company_user_company_department_id as company_user_company_department_id2,
                            h.type as type2, h.content as content2
                        FROM chat_history h
                        JOIN chat c ON h.chat_id = c.id
                        JOIN company_user_company_department cucd ON c.comp_user_comp_depart_id_current = cucd.id
                        WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL ',
                        IF(v_id_department > 0, CONCAT(' AND c.company_department_id = ', v_id_department), ''),
                        IF(v_id_attendant > 0, CONCAT(' AND cucd.company_user_id = ', v_id_attendant), ''),
                        ' AND c.ticket_id IS NULL
                        AND c.status = ''IN_PROGRESS''
                        AND h.created_at >= DATE_SUB(CURDATE(), INTERVAL ', 
                            IF(pFilterPeriod = 'LAST_24_HOURS', ' 24 HOUR)', IF(pFilterPeriod = 'LAST_7_DAYS', ' 7 DAY)',
                                IF(pFilterPeriod = 'LAST_30_DAYS', ' 30 DAY)', IF(pFilterPeriod = 'LAST_365_DAYS', ' 1 YEAR)', '')))), 
                        ' AND h.type = ''TEXT'' AND h.company_user_company_department_id IS NULL
                    ) sub_client ON sub2.chat_id = sub_client.chat_id2 AND sub_client.id_hist2 > sub2.id_hist
                ) sub
                GROUP BY sub.chat_id;'),

                CONCAT('SELECT sub.ticket_id, COUNT(DISTINCT id_hist1) AS hist_atend, COUNT(DISTINCT id_hist2) AS hist_client FROM (
                    SELECT sub2.*, sub_atend.*, sub_client.* FROM (
                        SELECT * FROM (
                            SELECT h.id as id_hist, h.chat_id, c.ticket_id, h.company_user_company_department_id, cucd.company_user_id, t.status, h.type, h.content, h.created_at
                            FROM chat_history h
                            JOIN chat c ON h.chat_id = c.id
                            JOIN ticket t ON c.ticket_id = t.id
                            JOIN company_user_company_department cucd ON c.comp_user_comp_depart_id_current = cucd.id
                            WHERE t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL ',
                            IF(v_id_department > 0, CONCAT(' AND t.company_department_id = ', v_id_department), ''),
                            IF(v_id_attendant > 0, CONCAT(' AND cucd.company_user_id = ', v_id_attendant), ''),
                            ' AND t.status = ''IN_PROGRESS''
                            AND h.created_at >= DATE_SUB(CURDATE(), INTERVAL ', 
                                IF(pFilterPeriod = 'LAST_24_HOURS', ' 24 HOUR)', IF(pFilterPeriod = 'LAST_7_DAYS', ' 7 DAY)',
                                    IF(pFilterPeriod = 'LAST_30_DAYS', ' 30 DAY)', IF(pFilterPeriod = 'LAST_365_DAYS', ' 1 YEAR)', '')))), 
                            ' AND h.content IN (''bs-joined-the-ticket'', ''bs-transferred-the-ticket-to-another-agent'', ''bs-ticket-transferred-department-and-attendan'')
                            ORDER BY c.ticket_id, h.id DESC
                            LIMIT 9999999999999
                        ) sub1
                        GROUP BY sub1.ticket_id
                    ) sub2
                    LEFT JOIN
                    (
                        SELECT h.id as id_hist1, h.chat_id as chat_id1, c.ticket_id as ticket_id1, h.company_user_company_department_id as company_user_company_department_id1,
                            h.type as type1, h.content as content1
                        FROM chat_history h
                        JOIN chat c ON h.chat_id = c.id
                        JOIN ticket t ON c.ticket_id = t.id
                        JOIN company_user_company_department cucd ON c.comp_user_comp_depart_id_current = cucd.id
                        WHERE t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL ',
                            IF(v_id_department > 0, CONCAT(' AND t.company_department_id = ', v_id_department), ''),
                            IF(v_id_attendant > 0, CONCAT(' AND cucd.company_user_id = ', v_id_attendant), ''),
                        ' AND t.status = ''IN_PROGRESS''
                        AND h.created_at >= DATE_SUB(CURDATE(), INTERVAL ', 
                            IF(pFilterPeriod = 'LAST_24_HOURS', ' 24 HOUR)', IF(pFilterPeriod = 'LAST_7_DAYS', ' 7 DAY)',
                                IF(pFilterPeriod = 'LAST_30_DAYS', ' 30 DAY)', IF(pFilterPeriod = 'LAST_365_DAYS', ' 1 YEAR)', '')))), 
                        ' AND h.type = ''TEXT'' AND h.company_user_company_department_id IS NOT NULL
                    ) sub_atend ON sub2.chat_id = sub_atend.chat_id1 AND sub_atend.id_hist1 > sub2.id_hist
                    LEFT JOIN
                    (
                        SELECT h.id as id_hist2, h.chat_id as chat_id2, c.ticket_id as ticket_id2, h.company_user_company_department_id as company_user_company_department_id2,
                            h.type as type2, h.content as content2
                        FROM chat_history h
                        JOIN chat c ON h.chat_id = c.id
                        JOIN ticket t ON c.ticket_id = t.id
                        JOIN company_user_company_department cucd ON c.comp_user_comp_depart_id_current = cucd.id
                        WHERE t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL ',
                            IF(v_id_department > 0, CONCAT(' AND t.company_department_id = ', v_id_department), ''),
                            IF(v_id_attendant > 0, CONCAT(' AND cucd.company_user_id = ', v_id_attendant), ''),
                        ' AND t.status = ''IN_PROGRESS''
                        AND h.created_at >= DATE_SUB(CURDATE(), INTERVAL ', 
                            IF(pFilterPeriod = 'LAST_24_HOURS', ' 24 HOUR)', IF(pFilterPeriod = 'LAST_7_DAYS', ' 7 DAY)',
                                IF(pFilterPeriod = 'LAST_30_DAYS', ' 30 DAY)', IF(pFilterPeriod = 'LAST_365_DAYS', ' 1 YEAR)', '')))), 
                        ' AND h.type = ''TEXT'' AND h.company_user_company_department_id IS NULL
                    ) sub_client ON sub2.chat_id = sub_client.chat_id2 AND sub_client.id_hist2 > sub2.id_hist
                ) sub
                GROUP BY sub.ticket_id;')
            );

            PREPARE stmt FROM @SQL_Final;
            EXECUTE stmt;
            DEALLOCATE PREPARE stmt;
            
            -- select @SQL_Final;
        END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pro_get_chats_tickets_without_attendant_customer_interaction');
    }
}
