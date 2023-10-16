<?php

namespace App\Http\Controllers;

use App\Chat;
use App\Tools\Builderall\Logger;
use App\Tools\Crypt;
use App\Tools\Tickets\SendRealtime;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerServiceController extends Controller
{
    public function index() {
        return view('functions.customer_service.index');
    }

    public function getCategoryGraphic() { 
    
        $category['success'] = false;
		
        $level = json_decode(request('data'))->level;
        $dateInitial = json_decode(request('data'))->dateInitial;
        $dateFinal = json_decode(request('data'))->dateFinal;
        $search = json_decode(request('data'))->search;
        $departments = [];
        $whereIF = '';

        if(request('departments') != null){
            foreach (request('departments') as $key) {
                array_push($departments, Crypt::decrypt($key));
            }
            $departments = implode(",", $departments);
            $whereIF = 'AND c.company_department_id IN ('.$departments.') ';
        }

        if($dateInitial && $dateFinal){
            $dateInitial = $dateInitial . ' 03:00:00';
            $dateFinal = $dateFinal . ' 03:00:00';
            $whereIF = "AND c.created_at >= '".$dateInitial."' AND c.created_at < '".$dateFinal."' + INTERVAL 1 DAY";
        }
        
        if($level){
            $levels = "AND IF(COALESCE(".$level.", 99) = 99, ultimo_nivel = 1 AND Count > 0, _level = ".$level." AND Count> 0)";
        }else{
            $levels = "";
        }
        
        if($search){
            $searchs = "AND (categoria LIKE CONCAT('%', '".$search."', '%') OR categoria_pai LIKE CONCAT('%', '".$search."', '%'))";
        }else{
            $searchs = "";
        }

		try {
			$arrayName = array(
				'company' => intval(Crypt::decrypt(session('companyselected')['id'])),
			);

			$query = "SELECT category_id, categoria, categoria_pai_id, categoria_pai, Count
                        FROM (
                            WITH final
                            AS
                            (
                                WITH recursive categories
                                AS
                                (
                                    SELECT 0 as _level, cat.company_id, cat.id, JSON_VALUE(cat.description, '$[0].description') AS categoria,
                                        cat.category_id as categoria_pai_id, CAST(NULL AS VARCHAR(500)) AS categoria_pai, CAST(cat.id AS CHAR) as path
                                    FROM category cat 
                                    WHERE cat.category_id IS NULL
                                    UNION ALL
                                    SELECT (_level + 1) as _level, cat.company_id, cat.id, JSON_VALUE(cat.description, '$[0].description') AS categoria,
                                        cat.category_id as categoria_pai_id, pai.categoria as categoria_pai, CONCAT(path,'->', cat.id) as path
                                    FROM category cat 
                                    JOIN categories pai ON cat.category_id = pai.id
                                )
                                SELECT cat.id as category_id, categoria, _level, cat.categoria_pai_id, cat.categoria_pai, SUM(IF(sub.id IS NOT NULL, 1, 0)) Count
                                FROM categories cat
                                LEFT JOIN
                                (
                                    SELECT cc.id, cc.category_id, cc.chat_id, c.created_at, c.deleted_at
                                    FROM chat_category cc 
                                    JOIN chat c ON cc.chat_id = c.id AND c.deleted_at IS NULL
                                    WHERE c.company_id = :company
                                    ".$whereIF."
                                    
                                ) sub ON cat.id = sub.category_id       
                                GROUP BY 1
                                ORDER BY path
                            )
                            SELECT f.category_id, f.categoria, f.categoria_pai_id, f.categoria_pai, f._level, IF(f.Count > 0, 1, 0) AS ultimo_nivel,
                                COALESCE(IF(f.Count > 0, f.Count, (SELECT SUM(Count) FROM final WHERE categoria_pai_id = f.category_id)), 0) AS Count
                            FROM final f
                        
                        ) sub
                        WHERE 0 = 0
                        ".$levels." 
                        ".$searchs."
                        AND COUNT > 0
            ORDER BY Count DESC;";

			$category['result'] = DB::select($query, $arrayName);

			$category['success'] = true;
			
		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['company-controller', 'getCategoryGraphic'], false);
			$category['success'] = false;
		}
		
		return $category;
    }

    public function getCategoryInfo(){
        // $category['success'] = false;

        // $category_id = request('id');
        // $departments_ids = request('departments');
        // $level = json_decode(request('data'))->level;
        // $dateInitial = json_decode(request('data'))->dateInitial;
        // $dateFinal = json_decode(request('data'))->dateFinal;
        // $search = json_decode(request('data'))->search;
        // $departments = [];
        // $whereIF = '';

        // if($departments_ids != null){
        //     foreach ($departments_ids as $key) {
        //         array_push($departments, Crypt::decrypt($key));
        //     }
        //     $departments = implode(",", $departments);
        //     $whereIF = 'WHERE c.company_department_id IN ('.$departments.') ';
        // }

        // if($dateInitial && $dateFinal){
        //     $dateInitial = $dateInitial . ' 00:00:00';
        //     $dateFinal = $dateFinal . ' 00:00:00';
        //     $whereIF = "AND c.created_at >= '".$dateInitial."' AND c.created_at < '".$dateFinal."' + INTERVAL 1 DAY";
        // }
        // // dd($whereIF);
		// try {
		// 	$arrayName = array(
		// 		'company' => intval(Crypt::decrypt(session('companyselected')['id'])),
		// 	);

		// 	$query = "SELECT cc.category_id, cc.chat_id, c.*
        //         FROM chat_category cc 
        //         JOIN chat c ON cc.chat_id = c.id AND c.deleted_at IS NULL
        //         ".$whereIF."
        //         and cc.category_id = ".$category_id.";";
                

        //     $query = "WITH recursive categories
        //             AS
        //             (
        //                 SELECT cat.id as category_id, cat.category_id as categoria_pai_id
        //                 FROM category cat 
        //                 WHERE cat.id = ".$category_id."
                        
        //                 UNION ALL
                        
        //                 SELECT cat.id as category_id, cat.category_id as categoria_pai_id
        //                 FROM category cat 
        //                 JOIN categories pai ON cat.category_id = pai.category_id
        //             )
        //             SELECT cat.category_id, cc.chat_id, c.*
        //             FROM categories cat
        //             JOIN chat_category cc ON cat.category_id = cc.category_id
        //             JOIN chat c ON cc.chat_id = c.id AND c.deleted_at IS NULL
        //             ".$whereIF.";";


		// 	$category['result'] = DB::select($query, $arrayName);

		// 	$category['success'] = true;

        try {
        $category_id = request('id');
        $departments_ids = request('departments');
        $level = json_decode(request('data'))->level;
        $dateInitial = json_decode(request('data'))->dateInitial;
        $dateFinal = json_decode(request('data'))->dateFinal;
        $search = json_decode(request('data'))->search;
        $departments = [];
        $whereIF = '';

        if(request('departments') != null){
            foreach (request('departments') as $key) {
                array_push($departments, Crypt::decrypt($key));
            }
            $departments = implode(",", $departments);
            $whereIF = 'AND c.company_department_id IN ('.$departments.') ';
        }

        if($dateInitial && $dateFinal){
            $dateInitial = $dateInitial . ' 03:00:00';
            $dateFinal = $dateFinal . ' 03:00:00';
            $whereIF = "AND c.created_at >= '".$dateInitial."' AND c.created_at < '".$dateFinal."' + INTERVAL 1 DAY";
        }

			$arrayName = array(
				'company' => intval(Crypt::decrypt(session('companyselected')['id'])),
			);


            $query = "WITH recursive categories
            AS
            (
            SELECT cat.id as category_id, cat.category_id as categoria_pai_id
            FROM category cat 
            WHERE cat.id = ".$category_id."

            UNION ALL

            SELECT cat.id as category_id, cat.category_id as categoria_pai_id
            FROM category cat 
            JOIN categories pai ON cat.category_id = pai.category_id
            )
            SELECT cat.category_id, cc.chat_id, c.*
            FROM categories cat
            JOIN chat_category cc ON cat.category_id = cc.category_id
            JOIN chat c ON cc.chat_id = c.id AND c.deleted_at IS NULL
            ".$whereIF.";";

            $query = "WITH recursive categories
                        AS
                        (
                            SELECT cat.id as category_id, cat.category_id as categoria_pai_id
                            FROM category cat 
                            WHERE cat.id = ".$category_id."
                            
                            UNION ALL
                            
                            SELECT cat.id as category_id, cat.category_id as categoria_pai_id
                            FROM category cat 
                            JOIN categories pai ON cat.category_id = pai.category_id
                        )
                        SELECT 
                            ua_agent.name,
                            c.id AS chat_number,
                            c.id AS chat_id,
                            cd.name AS department,
                            cd.id AS department_id,
                            c.status,
                            t.status AS ticket_status,
                            c.created_at AS chat_created_at,
                            c.updated_at AS chat_updated_at,
                            t.id AS ticket_number,
                            t.id AS ticket_id,
                            t.created_at AS ticket_created_at,
                            t.updated_at AS ticket_updated_at,
                            t.description,
                            t.comments,
                            t.priority,
                            c.type,
                            c.comp_user_comp_depart_id_current
                        FROM categories cat
                        JOIN chat_category cc ON cat.category_id = cc.category_id
                        JOIN chat c ON cc.chat_id = c.id AND c.deleted_at IS NULL
                        JOIN user_client_chat ucc ON c.id = ucc.chat_id
                        JOIN user_client AS uc ON uc.id = ucc.user_client_id
                        JOIN user_auth AS ua_client ON ua_client.id = uc.user_auth_id
                        JOIN company_department cd ON cd.id = c.company_department_id
                        LEFT JOIN company_user_company_department cucd ON cucd.id = c.comp_user_comp_depart_id_current
                        LEFT JOIN company_user cu ON cu.id = cucd.company_user_id
                        LEFT JOIN user_auth AS ua_agent ON ua_agent.id = cu.user_auth_id
                        LEFT JOIN ticket t ON t.id = c.ticket_id
                        WHERE (1 = 1) 
                        ".$whereIF."
                        GROUP BY c.id ASC;";

			$category['result'] = DB::select($query, $arrayName);

            foreach($category['result'] as $row) {
                $row->chat_id = Crypt::encrypt($row->chat_id);
            }

			$category['success'] = true;


		} catch (\Exception $e) {
			echo $e;
			Logger::reportException($e, [], ['company-controller', 'getCategoryInfo'], false);
			$category['success'] = false;
		}
		
		return $category;
    }

}
