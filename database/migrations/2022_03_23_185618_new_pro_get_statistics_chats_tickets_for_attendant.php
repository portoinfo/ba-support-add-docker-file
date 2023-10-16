<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NewProGetStatisticsChatsTicketsForAttendant extends Migration
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

            CREATE DEFINER=CURRENT_USER PROCEDURE `pro_get_statistics_chats_tickets_for_attendant`(IN pType ENUM('CHAT', 'TICKET'), IN pIdCompany INT, IN pIdDepartment INT, IN pFilterPeriod ENUM('LAST_24_HOURS', 'LAST_7_DAYS', 'LAST_30_DAYS', 'LAST_365_DAYS'))
                COMMENT 'Busca a qtd de chats/tickets abertos/finalizados/cancelados, tempo médio de fila, média de avaliação do atendente e do serviço'
            BEGIN
                DECLARE v_id_department INTEGER;
                SET v_id_department = COALESCE(pIdDepartment, 0);

                SET @SQL_Final = 
                    IF(pType = 'CHAT',
                        CONCAT('SELECT
                                user_auth.id, user_auth.name as attendant, company_user.last_login, company_user.id as company_user_id, 
                                SUM(COALESCE(sub_chat.opened, 0)) AS opened, SUM(COALESCE(sub_chat.finished, 0)) AS finished,
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
                                SUM(IF(c.update_status_in_progress >= DATE_SUB(CURDATE(), INTERVAL ', 
                                    IF(pFilterPeriod = 'LAST_24_HOURS', ' 24 HOUR)', IF(pFilterPeriod = 'LAST_7_DAYS', ' 7 DAY)',
                                        IF(pFilterPeriod = 'LAST_30_DAYS', ' 30 DAY)', IF(pFilterPeriod = 'LAST_365_DAYS', ' 1 YEAR)', '')))),
                                ', 1, 0)) AS opened,
                                SUM(IF(c.update_status_closed_resolved >= DATE_SUB(CURDATE(), INTERVAL ', 
                                    IF(pFilterPeriod = 'LAST_24_HOURS', ' 24 HOUR)', IF(pFilterPeriod = 'LAST_7_DAYS', ' 7 DAY)',
                                        IF(pFilterPeriod = 'LAST_30_DAYS', ' 30 DAY)', IF(pFilterPeriod = 'LAST_365_DAYS', ' 1 YEAR)', '')))),
                                ', 1, 0)) AS finished,
                                SUM(IF(c.update_status_canceled >= DATE_SUB(CURDATE(), INTERVAL ',
                                    IF(pFilterPeriod = 'LAST_24_HOURS', ' 24 HOUR)', IF(pFilterPeriod = 'LAST_7_DAYS', ' 7 DAY)',
                                        IF(pFilterPeriod = 'LAST_30_DAYS', ' 30 DAY)', IF(pFilterPeriod = 'LAST_365_DAYS', ' 1 YEAR)', '')))),
                                ', 1, 0)) AS canceled,
                                SUM(IF(t.created_at > c.created_at AND t.created_at >= DATE_SUB(CURDATE(), INTERVAL ',
                                    IF(pFilterPeriod = 'LAST_24_HOURS', ' 24 HOUR)', IF(pFilterPeriod = 'LAST_7_DAYS', ' 7 DAY)',
                                        IF(pFilterPeriod = 'LAST_30_DAYS', ' 30 DAY)', IF(pFilterPeriod = 'LAST_365_DAYS', ' 1 YEAR)', '')))),
                                ', 1, 0)) as moved_to_ticket,
                                AVG(IF(c.created_at >= DATE_SUB(CURDATE(), INTERVAL ',
                                    IF(pFilterPeriod = 'LAST_24_HOURS', ' 24 HOUR)', IF(pFilterPeriod = 'LAST_7_DAYS', ' 7 DAY)',
                                        IF(pFilterPeriod = 'LAST_30_DAYS', ' 30 DAY)', IF(pFilterPeriod = 'LAST_365_DAYS', ' 1 YEAR)', '')))),
                                ', c.queue_time, NULL)) AS avg_queue_time
                                FROM chat c
                                LEFT JOIN ticket t ON c.ticket_id = t.id
                                WHERE c.company_id = ', pIdCompany, ' AND c.deleted_at IS NULL ',
                                IF(v_id_department > 0, CONCAT(' AND c.company_department_id = ', v_id_department), ''),
                                ' GROUP BY c.comp_user_comp_depart_id_current
                            ) sub_chat ON cucd.id = sub_chat.comp_user_comp_depart_id_current
                            LEFT JOIN
                            (
                                SELECT c.comp_user_comp_depart_id_current,
                                    CAST(AVG(IF(c.created_at >= DATE_SUB(CURDATE(), INTERVAL ',
                                        IF(pFilterPeriod = 'LAST_24_HOURS', ' 24 HOUR)', IF(pFilterPeriod = 'LAST_7_DAYS', ' 7 DAY)',
                                            IF(pFilterPeriod = 'LAST_30_DAYS', ' 30 DAY)', IF(pFilterPeriod = 'LAST_365_DAYS', ' 1 YEAR)', '')))),
                                    ', av.stars_atendent, NULL)) AS DECIMAL(10, 2)) AS media_stars_atendent,
                                    CAST(AVG(IF(c.created_at >= DATE_SUB(CURDATE(), INTERVAL ',
                                        IF(pFilterPeriod = 'LAST_24_HOURS', ' 24 HOUR)', IF(pFilterPeriod = 'LAST_7_DAYS', ' 7 DAY)',
                                            IF(pFilterPeriod = 'LAST_30_DAYS', ' 30 DAY)', IF(pFilterPeriod = 'LAST_365_DAYS', ' 1 YEAR)', '')))),
                                    ', av.stars_service, NULL)) AS DECIMAL(10, 2)) AS media_stars_service
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
                                user_auth.id, user_auth.name as attendant, company_user.last_login, company_user.id as company_user_id, 
                                SUM(COALESCE(sub_ticket.opened, 0)) AS opened, SUM(COALESCE(sub_ticket.finished, 0)) AS finished,
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
                                SUM(IF(t.update_status_in_progress >= DATE_SUB(CURDATE(), INTERVAL ', 
                                    IF(pFilterPeriod = 'LAST_24_HOURS', ' 24 HOUR)', IF(pFilterPeriod = 'LAST_7_DAYS', ' 7 DAY)',
                                        IF(pFilterPeriod = 'LAST_30_DAYS', ' 30 DAY)', IF(pFilterPeriod = 'LAST_365_DAYS', ' 1 YEAR)', '')))),
                                ', 1, 0)) AS opened,
                                SUM(IF(t.update_status_closed_resolved >= DATE_SUB(CURDATE(), INTERVAL ', 
                                    IF(pFilterPeriod = 'LAST_24_HOURS', ' 24 HOUR)', IF(pFilterPeriod = 'LAST_7_DAYS', ' 7 DAY)',
                                        IF(pFilterPeriod = 'LAST_30_DAYS', ' 30 DAY)', IF(pFilterPeriod = 'LAST_365_DAYS', ' 1 YEAR)', '')))),
                                ', 1, 0)) AS finished,
                                SUM(IF(t.update_status_canceled >= DATE_SUB(CURDATE(), INTERVAL ',
                                    IF(pFilterPeriod = 'LAST_24_HOURS', ' 24 HOUR)', IF(pFilterPeriod = 'LAST_7_DAYS', ' 7 DAY)',
                                        IF(pFilterPeriod = 'LAST_30_DAYS', ' 30 DAY)', IF(pFilterPeriod = 'LAST_365_DAYS', ' 1 YEAR)', '')))),
                                ', 1, 0)) AS canceled,
                                AVG(IF(t.created_at >= DATE_SUB(CURDATE(), INTERVAL ',
                                    IF(pFilterPeriod = 'LAST_24_HOURS', ' 24 HOUR)', IF(pFilterPeriod = 'LAST_7_DAYS', ' 7 DAY)',
                                        IF(pFilterPeriod = 'LAST_30_DAYS', ' 30 DAY)', IF(pFilterPeriod = 'LAST_365_DAYS', ' 1 YEAR)', '')))),
                                ', t.queue_time, NULL)) AS avg_queue_time
                                FROM ticket t
                                JOIN chat c ON t.id = c.ticket_id
                                WHERE t.company_id = ', pIdCompany, ' AND t.deleted_at IS NULL ',
                                IF(v_id_department > 0, CONCAT(' AND c.company_department_id = ', v_id_department), ''),
                                ' GROUP BY c.comp_user_comp_depart_id_current
                            ) sub_ticket ON cucd.id = sub_ticket.comp_user_comp_depart_id_current
                            LEFT JOIN
                            (
                                SELECT c.comp_user_comp_depart_id_current,
                                    CAST(AVG(IF(c.created_at >= DATE_SUB(CURDATE(), INTERVAL ',
                                        IF(pFilterPeriod = 'LAST_24_HOURS', ' 24 HOUR)', IF(pFilterPeriod = 'LAST_7_DAYS', ' 7 DAY)',
                                            IF(pFilterPeriod = 'LAST_30_DAYS', ' 30 DAY)', IF(pFilterPeriod = 'LAST_365_DAYS', ' 1 YEAR)', '')))),
                                    ', av.stars_atendent, NULL)) AS DECIMAL(10, 2)) AS media_stars_atendent,
                                    CAST(AVG(IF(c.created_at >= DATE_SUB(CURDATE(), INTERVAL ',
                                        IF(pFilterPeriod = 'LAST_24_HOURS', ' 24 HOUR)', IF(pFilterPeriod = 'LAST_7_DAYS', ' 7 DAY)',
                                            IF(pFilterPeriod = 'LAST_30_DAYS', ' 30 DAY)', IF(pFilterPeriod = 'LAST_365_DAYS', ' 1 YEAR)', '')))),
                                    ', av.stars_service, NULL)) AS DECIMAL(10, 2)) AS media_stars_service
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
        DB::unprepared("DROP procedure IF EXISTS `pro_get_statistics_chats_tickets_for_attendant`;");
    }
}
