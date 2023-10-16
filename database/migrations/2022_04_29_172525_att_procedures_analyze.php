<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AttProceduresAnalyze extends Migration
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

            CREATE DEFINER=CURRENT_USER PROCEDURE `pro_get_attendant_response_times`(IN pType ENUM('CHAT', 'TICKET'), IN pIdCompany INT, IN pIdDepartment INT, IN pInitialDate DATETIME, IN pFinalDate DATETIME, IN pIsDebugger INT)
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
                            ' AND h.created_at BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, '''', 
                            ' WINDOW w AS (PARTITION BY h.chat_id ORDER BY h.chat_id, h.id)
                        ) sub
                        WHERE sub.next_created_at IS NOT NULL
                        AND COALESCE(sub.company_user_company_department_id, 0) != COALESCE(sub.next_company_user_company_department_id, 0)
                        AND sub.company_user_company_department_id IS NULL AND sub.next_company_user_company_department_id IS NOT NULL
                        GROUP BY sub.chat_id, sub.next_created_by
                    ) sub2
                    GROUP BY sub2.next_created_by;');

                IF (pIsDebugger = 0) THEN
                    PREPARE stmt FROM @SQL_Final;
                    EXECUTE stmt;
                    DEALLOCATE PREPARE stmt;
                ELSE
                    SELECT @SQL_Final;
                END IF;
            END;

            DROP procedure IF EXISTS `pro_get_avaliations_attendants`;

            CREATE DEFINER=CURRENT_USER PROCEDURE `pro_get_avaliations_attendants`(IN pType ENUM('CHAT', 'TICKET'), IN pIdCompany INT, IN pIdDepartment INT, IN pIdAttendant INT, IN pInitialDate DATETIME, IN pFinalDate DATETIME, IN pIsDebugger INT)
                COMMENT 'Busca as avaliações dos atendentes conforme os filtros informados'
            BEGIN
                DECLARE v_id_department INTEGER;
                DECLARE v_id_attendant INTEGER;
                SET v_id_department = COALESCE(pIdDepartment, 0);
                SET v_id_attendant = COALESCE(pIdAttendant, 0);
                    
                SET @SQL_Final = IF(pType = 'CHAT', 
                    CONCAT('SELECT ua.name, a.chat_id, a.ticket_id, a.stars_atendent, 
                            a.stars_service, a.`comment` as message, cucd.company_user_id, a.created_at as hour
                        FROM avaliation a
                        JOIN user_client_chat ucc ON ucc.chat_id = a.chat_id
                        JOIN user_client uc ON uc.id = ucc.user_client_id
                        JOIN user_auth ua ON ua.id = uc.user_auth_id
                        JOIN chat c ON a.chat_id = c.id
                        JOIN company_user_company_department cucd ON c.comp_user_comp_depart_id_current = cucd.id
                        WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL ',
                        IF(v_id_department > 0, CONCAT(' AND c.company_department_id = ', v_id_department), ''),
                        IF(v_id_attendant > 0, CONCAT(' AND cucd.company_user_id = ', v_id_attendant), ''),
                        ' AND c.ticket_id IS NULL
                        AND c.created_at BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, '''', 
                        ' ORDER BY a.chat_id; '),
                    CONCAT('SELECT ua.name, a.chat_id, a.ticket_id, a.stars_atendent, 
                            a.stars_service, a.`comment` as message, cucd.company_user_id, a.created_at as hour
                        FROM avaliation a
                        JOIN user_client_chat ucc ON ucc.chat_id = a.chat_id
                        JOIN user_client uc ON uc.id = ucc.user_client_id
                        JOIN user_auth ua ON ua.id = uc.user_auth_id
                        JOIN chat c ON a.chat_id = c.id
                        JOIN ticket t ON c.ticket_id = t.id
                        JOIN company_user_company_department cucd ON c.comp_user_comp_depart_id_current = cucd.id
                        WHERE t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL ',
                        IF(v_id_department > 0, CONCAT(' AND t.company_department_id = ', v_id_department), ''),
                        IF(v_id_attendant > 0, CONCAT(' AND cucd.company_user_id = ', v_id_attendant), ''),
                        ' AND t.created_at BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, '''', 
                        ' ORDER BY a.chat_id; ')
                );
            
                IF (pIsDebugger = 0) THEN
                    PREPARE stmt FROM @SQL_Final;
                    EXECUTE stmt;
                    DEALLOCATE PREPARE stmt;
                ELSE
                    SELECT @SQL_Final;
                END IF;
            END;
            
            DROP procedure IF EXISTS `pro_get_chats_tickets_waiting_responses_customer_attendant`;

            CREATE DEFINER=CURRENT_USER PROCEDURE `pro_get_chats_tickets_waiting_responses_customer_attendant`(IN pType ENUM('CHAT', 'TICKET'), IN pIdCompany INT, IN pIdDepartment INT, IN pIdAttendant INT, IN pInitialDate DATETIME, IN pFinalDate DATETIME, IN pIsDebugger INT)
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
                        AND h.created_at BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, '''', 
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
                        AND h.created_at BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, '''', 
                        ' ORDER BY h.chat_id, h.id DESC
                        LIMIT 999999999
                    ) sub
                    GROUP BY sub.ticket_id;')
                );

                IF (pIsDebugger = 0) THEN
                    PREPARE stmt FROM @SQL_Final;
                    EXECUTE stmt;
                    DEALLOCATE PREPARE stmt;
                ELSE
                    SELECT @SQL_Final;
                END IF;
            END;

            DROP procedure IF EXISTS `pro_times_of_attendants_in_service`;

            CREATE DEFINER=CURRENT_USER PROCEDURE `pro_times_of_attendants_in_service`(IN pType ENUM('CHAT', 'TICKET'), IN pIdCompany INT, IN pIdDepartment INT, IN pIdAttendant INT, IN pInitialDate DATETIME, IN pFinalDate DATETIME, IN pIsDebugger INT)
                COMMENT 'Tempo total de atendimento do atendente'
            BEGIN
                DECLARE v_id_department INTEGER;
                DECLARE v_id_attendant INTEGER;
                SET v_id_department = COALESCE(pIdDepartment, 0);
                SET v_id_attendant = COALESCE(pIdAttendant, 0);
            
                SET @SQL_Final = IF(pType = 'CHAT', 
                    CONCAT('SELECT 
                        COUNT(DISTINCT cwt.chat_id) count,
                        COALESCE(SUM(TIME_TO_SEC(TIMEDIFF(cwt.final_date, cwt.initial_date))), 0) tempo_segundos
                            FROM chat c
                            JOIN chat_working_times cwt ON c.id = cwt.chat_id
                            JOIN company_user_company_department cucd ON cwt.company_user_company_department_id = cucd.id
                            WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL ',
                            IF(v_id_department > 0, CONCAT(' AND c.company_department_id = ', v_id_department), ''),
                            IF(v_id_attendant > 0, CONCAT(' AND cucd.company_user_id = ', v_id_attendant), ''),
                            ' AND cwt.ticket_id IS NULL AND cwt.final_date IS NOT NULL
                            AND c.created_at BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, '''', 
                            ' ORDER BY cwt.initial_date;'
                        ),
                        CONCAT('SELECT 
                        COUNT(DISTINCT cwt.ticket_id) count,
                        COALESCE(SUM(TIME_TO_SEC(TIMEDIFF(cwt.final_date, cwt.initial_date))), 0) tempo_segundos
                            FROM chat c
                            JOIN ticket t ON c.ticket_id = t.id
                            JOIN chat_working_times cwt ON c.id = cwt.chat_id
                            JOIN company_user_company_department cucd ON cwt.company_user_company_department_id = cucd.id
                            WHERE t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL ',
                            IF(v_id_department > 0, CONCAT(' AND t.company_department_id = ', v_id_department), ''),
                            IF(v_id_attendant > 0, CONCAT(' AND cucd.company_user_id = ', v_id_attendant), ''),
                            ' AND cwt.ticket_id IS NOT NULL AND cwt.final_date IS NOT NULL
                            AND t.created_at BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, '''',
                            ' ORDER BY cwt.initial_date;')
                        );
                        
                IF (pIsDebugger = 0) THEN
                    PREPARE stmt FROM @SQL_Final;
                    EXECUTE stmt;
                    DEALLOCATE PREPARE stmt;
                ELSE
                    SELECT @SQL_Final;
                END IF;
            END;
            
            DROP procedure IF EXISTS `pro_get_chats_tickets_without_attendant_customer_interaction`;

            CREATE DEFINER=CURRENT_USER PROCEDURE `pro_get_chats_tickets_without_attendant_customer_interaction`(IN pType ENUM('CHAT', 'TICKET'), IN pIdCompany INT, IN pIdDepartment INT, IN pIdAttendant INT, IN pInitialDate DATETIME, IN pFinalDate DATETIME, IN pIsDebugger INT)
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
                                AND h.created_at BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, '''', 
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
                            AND h.created_at BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, '''', 
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
                            AND h.created_at BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, '''', 
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
                                AND h.created_at BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, '''', 
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
                            AND h.created_at BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, '''', 
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
                            AND h.created_at BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, '''', 
                            ' AND h.type = ''TEXT'' AND h.company_user_company_department_id IS NULL
                        ) sub_client ON sub2.chat_id = sub_client.chat_id2 AND sub_client.id_hist2 > sub2.id_hist
                    ) sub
                    GROUP BY sub.ticket_id;')
                );

                IF (pIsDebugger = 0) THEN
                    PREPARE stmt FROM @SQL_Final;
                    EXECUTE stmt;
                    DEALLOCATE PREPARE stmt;
                ELSE
                    SELECT @SQL_Final;
                END IF;
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
        Schema::dropIfExists('pro_get_attendant_response_times');
        Schema::dropIfExists('pro_get_avaliations_attendants');
        Schema::dropIfExists('pro_get_chats_tickets_waiting_responses_customer_attendant');
        Schema::dropIfExists('pro_times_of_attendants_in_service');
        Schema::dropIfExists('pro_get_chats_tickets_without_attendant_customer_interaction');
    }
}
