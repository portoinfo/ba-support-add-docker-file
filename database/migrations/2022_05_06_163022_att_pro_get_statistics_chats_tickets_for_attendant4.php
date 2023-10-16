<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AttProGetStatisticsChatsTicketsForAttendant4 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
            DROP procedure IF EXISTS `pro_get_statistics_chats_tickets_for_attendant`;
            
            CREATE DEFINER=CURRENT_USER PROCEDURE `pro_get_statistics_chats_tickets_for_attendant`(IN pType ENUM('CHAT', 'TICKET'), IN pIdCompany INT, IN pIdDepartment INT, IN pInitialDate DATETIME, IN pFinalDate DATETIME, IN pIsDebugger INT)
                COMMENT 'Busca a qtd de chats/tickets abertos/finalizados/cancelados, tempo médio de fila, média de avaliação do atendente e do serviço'
            BEGIN
            
            DECLARE v_id_department INTEGER;
            SET v_id_department = COALESCE(pIdDepartment, 0);

            SET @SQL_Final = 
                IF(pType = 'CHAT',
                    CONCAT('SELECT
                            user_auth.id, user_auth.name as attendant, user_auth.email, (company_user.deleted_at IS NOT NULL) AS user_removed, company_user.last_login, company_user.id as company_user_id, 
                            (SUM(COALESCE(sub_work_times.opened, 0)) + SUM(COALESCE(sub_atend_curr.opened, 0))) AS opened, SUM(COALESCE(sub_chat.finished, 0)) AS finished,
                            SUM(COALESCE(sub_chat.canceled, 0)) AS canceled, SUM(COALESCE(sub_chat.moved_to_ticket, 0)) AS moved_to_ticket,
                            CAST(AVG(sub_chat.avg_queue_time) AS DECIMAL(10, 2)) AS avg_queue_time,
                            CAST(AVG(sub_avaliation.media_stars_atendent) AS DECIMAL(10, 2)) AS media_stars_atendent,
                            CAST(AVG(sub_avaliation.media_stars_service) AS DECIMAL(10, 2)) AS media_stars_service
                        FROM user_auth
                        INNER JOIN company_user ON user_auth.id = company_user.user_auth_id
                        INNER JOIN company_user_company_department AS cucd ON company_user.id = cucd.company_user_id
                        LEFT JOIN
                        (
                            SELECT c.comp_user_comp_depart_id_current,
                            SUM(IF(c.update_status_closed_resolved BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, ''', 1, 0)) AS finished,
                            SUM(IF(c.update_status_canceled BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, ''', 1, 0)) AS canceled,
                            SUM(IF(t.created_at > c.created_at AND t.created_at BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, ''', 1, 0)) AS moved_to_ticket,
                            AVG(IF(c.created_at BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, ''', c.queue_time, NULL)) AS avg_queue_time
                            FROM chat c
                            LEFT JOIN ticket t ON c.ticket_id = t.id
                            WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL ',
                            IF(v_id_department > 0, CONCAT(' AND c.company_department_id = ', v_id_department), ''),
                            ' GROUP BY c.comp_user_comp_depart_id_current
                        ) sub_chat ON cucd.id = sub_chat.comp_user_comp_depart_id_current
                        
                        LEFT JOIN
                        (
                            SELECT company_user_company_department_id, COUNT(*) opened FROM (
                                SELECT cwt.chat_id, cwt.company_user_company_department_id
                                FROM chat c
                                JOIN chat_working_times cwt ON c.id = cwt.chat_id
                                WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL
                                AND cwt.ticket_id IS NULL
                                AND c.update_status_in_progress BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, '''
                                GROUP BY cwt.chat_id, cwt.company_user_company_department_id
                            ) sub_interno
                            GROUP BY 1
                        ) sub_work_times ON cucd.id = sub_work_times.company_user_company_department_id
                        
                        LEFT JOIN
                        (
                            SELECT c.comp_user_comp_depart_id_current, COUNT(*) opened
                            FROM chat c
                            WHERE c.company_id = 1 AND c.deleted_at IS NULL
                            AND c.ticket_id IS NULL
                            AND c.update_status_in_progress < ''2022-04-11 11:26:59''
                            AND c.update_status_in_progress BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, '''
                            GROUP BY c.comp_user_comp_depart_id_current
                        ) sub_atend_curr ON cucd.id = sub_atend_curr.comp_user_comp_depart_id_current

                        LEFT JOIN
                        (
                            SELECT c.comp_user_comp_depart_id_current,
                                CAST(AVG(IF(c.created_at BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, ''', av.stars_atendent, NULL)) AS DECIMAL(10, 2)) AS media_stars_atendent,
                                CAST(AVG(IF(c.created_at BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, ''', av.stars_service, NULL)) AS DECIMAL(10, 2)) AS media_stars_service
                            FROM avaliation av
                            JOIN chat c ON av.chat_id = c.id
                            WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL ',
                            IF(v_id_department > 0, CONCAT(' AND c.company_department_id = ', v_id_department), ''),
                            ' AND c.status != ''CANCELED''', 
                            ' GROUP BY c.comp_user_comp_depart_id_current 
                        ) sub_avaliation ON cucd.id = sub_avaliation.comp_user_comp_depart_id_current', 
                        ' WHERE company_id = ', pIdCompany, 
                        IF(v_id_department > 0, CONCAT(' AND cucd.company_department_id = ', v_id_department), ''),
                        ' GROUP BY user_auth.id ORDER BY user_auth.name;'),
                        CONCAT('SELECT
                            user_auth.id, user_auth.name as attendant, user_auth.email, (company_user.deleted_at IS NOT NULL) AS user_removed, company_user.last_login, company_user.id as company_user_id, 
                            (SUM(COALESCE(sub_work_times.opened, 0)) + SUM(COALESCE(sub_atend_curr.opened, 0))) AS opened, SUM(COALESCE(sub_ticket.finished, 0)) AS finished,
                            SUM(COALESCE(sub_ticket.canceled, 0)) AS canceled, 
                            CAST(AVG(sub_ticket.avg_queue_time) AS DECIMAL(10, 2)) AS avg_queue_time,
                            CAST(AVG(sub_avaliation.media_stars_atendent) AS DECIMAL(10, 2)) AS media_stars_atendent,
                            CAST(AVG(sub_avaliation.media_stars_service) AS DECIMAL(10, 2)) AS media_stars_service
                        FROM user_auth
                        INNER JOIN company_user ON user_auth.id = company_user.user_auth_id
                        INNER JOIN company_user_company_department AS cucd ON company_user.id = cucd.company_user_id

                        LEFT JOIN
                        (
                            SELECT c.comp_user_comp_depart_id_current,
                            SUM(IF(t.update_status_in_progress BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, ''', 1, 0)) AS opened,
                            SUM(IF(t.update_status_closed_resolved BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, ''', 1, 0)) AS finished,
                            SUM(IF(t.update_status_canceled BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, ''', 1, 0)) AS canceled,
                            AVG(IF(t.created_at BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, ''', t.queue_time, NULL)) AS avg_queue_time
                            FROM ticket t
                            JOIN chat c ON t.id = c.ticket_id
                            WHERE t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL ',
                            IF(v_id_department > 0, CONCAT(' AND c.company_department_id = ', v_id_department), ''),
                            ' GROUP BY c.comp_user_comp_depart_id_current
                        ) sub_ticket ON cucd.id = sub_ticket.comp_user_comp_depart_id_current
                        
                        LEFT JOIN
                        (
                            SELECT company_user_company_department_id, COUNT(*) opened FROM (
                                SELECT cwt.ticket_id, cwt.company_user_company_department_id
                                FROM chat c
                                JOIN ticket t ON c.ticket_id = t.id
                                JOIN chat_working_times cwt ON c.id = cwt.chat_id
                                WHERE t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL
                                AND cwt.ticket_id IS NOT NULL
                                AND t.update_status_in_progress BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, '''
                                GROUP BY cwt.ticket_id, cwt.company_user_company_department_id
                            ) sub_interno
                            GROUP BY 1
                        ) sub_work_times ON cucd.id = sub_work_times.company_user_company_department_id

                        LEFT JOIN
                        (
                            SELECT c.comp_user_comp_depart_id_current, COUNT(*) opened
                            FROM chat c
                            JOIN ticket t ON c.ticket_id = t.id
                            WHERE t.company_id = 1 AND t.deleted_at IS NULL
                            AND t.update_status_in_progress < ''2022-04-11 11:26:59''
                            AND t.update_status_in_progress BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, '''
                            GROUP BY c.comp_user_comp_depart_id_current
                        ) sub_atend_curr ON cucd.id = sub_atend_curr.comp_user_comp_depart_id_current

                        LEFT JOIN
                        (
                            SELECT c.comp_user_comp_depart_id_current,
                                CAST(AVG(IF(c.created_at BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, ''', av.stars_atendent, NULL)) AS DECIMAL(10, 2)) AS media_stars_atendent,
                                CAST(AVG(IF(c.created_at BETWEEN ''', pInitialDate, ''' AND ''', pFinalDate, ''', av.stars_service, NULL)) AS DECIMAL(10, 2)) AS media_stars_service
                            FROM avaliation av
                            JOIN ticket t ON av.ticket_id = t.id
                            JOIN chat c ON t.id = c.ticket_id
                            WHERE t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL ',
                            IF(v_id_department > 0, CONCAT(' AND t.company_department_id = ', v_id_department), ''),
                            ' AND t.status != ''CANCELED''', 
                            ' GROUP BY c.comp_user_comp_depart_id_current 
                        ) sub_avaliation ON cucd.id = sub_avaliation.comp_user_comp_depart_id_current', 
                        ' WHERE company_id = ', pIdCompany, 
                        IF(v_id_department > 0, CONCAT(' AND cucd.company_department_id = ', v_id_department), ''),
                        ' GROUP BY user_auth.id ORDER BY user_auth.name;')
                    );
                        
            IF (pIsDebugger = 0) THEN
                PREPARE stmt FROM @SQL_Final;
                EXECUTE stmt;
                DEALLOCATE PREPARE stmt;
            ELSE
                SELECT @SQL_Final;
            END IF;
        END");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pro_get_statistics_chats_tickets_for_attendant');
    }
}
