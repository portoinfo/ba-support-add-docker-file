<?php
    namespace App\Tools;
    /**
    * @docs
    * https://phpspreadsheet.readthedocs.io/en/latest/
    */

    use App\Tools\Tickets\Feedback;
use Carbon\Carbon;
use DateTimeImmutable;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx as WriterXlsx;
    
    class exportExcel {

        private $array;

        // public function __construct($array)
        // {
        //     $this->array = $array;
        // }

        public static function generate($alias,$colunm,$array,$avaliation,$ChatorTicket){

            foreach ($array as $key) {
                if($key->media_stars_atendent == 0){
                    $key->media_stars_atendent = '0';
                }
                if($key->media_stars_service == 0){
                    $key->media_stars_service = '0';
                }
                if($key->media_stars_atendent != 0){
                    $key->media_stars_atendent = ($key->media_stars_atendent * 100 / 5) . '%';
                }
                if($key->media_stars_service != 0){
                    $key->media_stars_service = ($key->media_stars_service * 100 / 5) . '%';
                }
            }

            // TRANSFORMANDO O OBJETO EM ARRAY
            $spreadSheet = new Spreadsheet();
            $arr = json_decode(json_encode ( $array ) , true);
            $filterArray = [];
            $count = 0;

            // CRIANDO POSIÇÕES NA VARIAVEL
            for ($i=0; $i < count($arr); $i++) { 
                array_push($filterArray,[]);
            }

            // PEGANDO SÓ AS COLUNAS DESEJADAS
            foreach ($arr as $key => $value) {
                foreach ($colunm as $key2 => $value2) {
                    array_push($filterArray[$count], $value[$value2]);
                }
                $count++;
            }
            
            // ADICIONADO O ALIAS NO EXCEL
            $ca = 0;
            foreach ($alias as $key => $value) {
                $alias[$ca] = Feedback::t($value);
                $ca++;
            }
            array_unshift($filterArray, $alias);
            $styleArray = [
                'font' => [
                    'bold' => true,
                ],
            ];

            $aba0 = $spreadSheet->getActiveSheet()
            ->fromArray(
                $filterArray,  // The data to set
                NULL,        // Array values with this value will not be set
                'A1'         // Top left coordinate of the worksheet range where
                                //    we want to set these values (default is A1)
            );
            $aba0->setTitle(Feedback::t('bs-agents'));
            $aba0->getStyle('A1:Z1')->applyFromArray($styleArray);
            $spreadSheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $spreadSheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $spreadSheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
            $spreadSheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $spreadSheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
            $spreadSheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
            $spreadSheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);

            // TRANSFORMANDO O OBJETO EM ARRAY - ABA 1 - AVALIAÇÕES
            if(count($avaliation) > 0){

                $aux = [];
                foreach ($avaliation as $key3) {
                    if($aux == null){
                        array_push($aux, $key3->attendant_id);
                        if($ChatorTicket == 'ALL'){
                            $stringaux = Feedback::t('bs-all');
                        }else{
                            $stringaux = $ChatorTicket;
                        }
                        $aba1 = new Worksheet($spreadSheet, $key3->attendant_name.'-'.$stringaux);
                        $spreadSheet->addSheet($aba1)->getStyle('A1:Z1')->applyFromArray($styleArray);
                        $count = 1;

                        $aba1->setCellValue('A1', Feedback::t('bs-agents'));
                        if($ChatorTicket == 'ALL'){
                            $aba1->setCellValue('B1', Feedback::t('bs-chat'));
                            $aba1->setCellValue('C1', Feedback::t('bs-ticket'));
                            $aba1->setCellValue('D1', Feedback::t('bs-client-name'));
                            $aba1->setCellValue('E1', Feedback::t('bs-message'));
                            $aba1->setCellValue('F1', Feedback::t('bs-stars-atendent'));
                            $aba1->setCellValue('G1', Feedback::t('bs-stars-service'));
                        }else if($ChatorTicket == 'CHAT'){
                            $aba1->setCellValue('B1', Feedback::t('bs-chat'));
                            $aba1->setCellValue('C1', Feedback::t('bs-client-name'));
                            $aba1->setCellValue('D1', Feedback::t('bs-message'));
                            $aba1->setCellValue('E1', Feedback::t('bs-stars-atendent'));
                            $aba1->setCellValue('F1', Feedback::t('bs-stars-service'));
                        }else{
                            $aba1->setCellValue('B1', Feedback::t('bs-ticket'));
                            $aba1->setCellValue('C1', Feedback::t('bs-client-name'));
                            $aba1->setCellValue('D1', Feedback::t('bs-message'));
                            $aba1->setCellValue('E1', Feedback::t('bs-stars-atendent'));
                            $aba1->setCellValue('F1', Feedback::t('bs-stars-service'));
                        }
                       
                        foreach ($avaliation as $key) {
                            if($key->attendant_id == $key3->attendant_id){
                                $count++;
                                $aba1->setCellValue('A'.$count, $key->attendant_name);
                                if($ChatorTicket == 'ALL'){
                                    $aba1->setCellValue('B'.$count, $key->chat_id);
                                    $aba1->setCellValue('C'.$count, $key->ticket_id);
                                    $aba1->setCellValue('D'.$count, $key->name);
                                    $aba1->setCellValue('E'.$count, '"'.$key->message.'"');
                                    $aba1->setCellValue('F'.$count, $key->stars_atendent);
                                    $aba1->setCellValue('G'.$count, $key->stars_service);
                                }else if($ChatorTicket == 'CHAT'){
                                    $aba1->setCellValue('B'.$count, $key->chat_id);
                                    $aba1->setCellValue('C'.$count, $key->name);
                                    $aba1->setCellValue('D'.$count, '"'.$key->message.'"');
                                    $aba1->setCellValue('E'.$count, $key->stars_atendent);
                                    $aba1->setCellValue('F'.$count, $key->stars_service);
                                }else{
                                    $aba1->setCellValue('B'.$count, $key->ticket_id);
                                    $aba1->setCellValue('C'.$count, $key->name);
                                    $aba1->setCellValue('D'.$count, '"'.$key->message.'"');
                                    $aba1->setCellValue('E'.$count, $key->stars_atendent);
                                    $aba1->setCellValue('F'.$count, $key->stars_service);
                                }
                            }
                        }
                        $spreadSheet->getActiveSheet()->setAutoFilter('A1:F'.$count);
                        $spreadSheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
                        $spreadSheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
                        $spreadSheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
                        $spreadSheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
                        $spreadSheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
                        $spreadSheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
                        
                    }else{
                        if (in_array($key3->attendant_id, $aux)){

                        }else{
                            array_push($aux, $key3->attendant_id);
                            if($ChatorTicket == 'ALL'){
                                $stringaux = Feedback::t('bs-all');
                            }else{
                                $stringaux = $ChatorTicket;
                            }
                            $aba1 = new Worksheet($spreadSheet, $key3->attendant_name.'-'.$stringaux);
                            $spreadSheet->addSheet($aba1)->getStyle('A1:Z1')->applyFromArray($styleArray);
                            $count = 1;

                            $aba1->setCellValue('A1', Feedback::t('bs-agents'));
                            if($ChatorTicket == 'ALL'){
                                $aba1->setCellValue('B1', Feedback::t('bs-chat'));
                                $aba1->setCellValue('C1', Feedback::t('bs-ticket'));
                                $aba1->setCellValue('D1', Feedback::t('bs-client-name'));
                                $aba1->setCellValue('E1', Feedback::t('bs-message'));
                                $aba1->setCellValue('F1', Feedback::t('bs-stars-atendent'));
                                $aba1->setCellValue('G1', Feedback::t('bs-stars-service'));
                            }else if($ChatorTicket == 'CHAT'){
                                $aba1->setCellValue('B1', Feedback::t('bs-chat'));
                                $aba1->setCellValue('C1', Feedback::t('bs-client-name'));
                                $aba1->setCellValue('D1', Feedback::t('bs-message'));
                                $aba1->setCellValue('E1', Feedback::t('bs-stars-atendent'));
                                $aba1->setCellValue('F1', Feedback::t('bs-stars-service'));
                            }else{
                                $aba1->setCellValue('B1', Feedback::t('bs-ticket'));
                                $aba1->setCellValue('C1', Feedback::t('bs-client-name'));
                                $aba1->setCellValue('D1', Feedback::t('bs-message'));
                                $aba1->setCellValue('E1', Feedback::t('bs-stars-atendent'));
                                $aba1->setCellValue('F1', Feedback::t('bs-stars-service'));
                            }
                            foreach ($avaliation as $key) {
                                if($key->attendant_id == $key3->attendant_id){
                                    $count++;
                                    $aba1->setCellValue('A'.$count, $key->attendant_name);
                                    if($ChatorTicket == 'ALL'){
                                        $aba1->setCellValue('B'.$count, $key->chat_id);
                                        $aba1->setCellValue('C'.$count, $key->ticket_id);
                                        $aba1->setCellValue('D'.$count, $key->name);
                                        $aba1->setCellValue('E'.$count, '"'.$key->message.'"');
                                        $aba1->setCellValue('F'.$count, $key->stars_atendent);
                                        $aba1->setCellValue('G'.$count, $key->stars_service);
                                    }else if($ChatorTicket == 'CHAT'){
                                        $aba1->setCellValue('B'.$count, $key->chat_id);
                                        $aba1->setCellValue('C'.$count, $key->name);
                                        $aba1->setCellValue('D'.$count, '"'.$key->message.'"');
                                        $aba1->setCellValue('E'.$count, $key->stars_atendent);
                                        $aba1->setCellValue('F'.$count, $key->stars_service);
                                    }else{
                                        $aba1->setCellValue('B'.$count, $key->ticket_id);
                                        $aba1->setCellValue('C'.$count, $key->name);
                                        $aba1->setCellValue('D'.$count, '"'.$key->message.'"');
                                        $aba1->setCellValue('E'.$count, $key->stars_atendent);
                                        $aba1->setCellValue('F'.$count, $key->stars_service);
                                    }
                                    
                                }
                            }
                            $spreadSheet->getActiveSheet()->setAutoFilter('A1:F'.$count);
                            $spreadSheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
                            $spreadSheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
                            $spreadSheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
                            $spreadSheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
                            $spreadSheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
                            $spreadSheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
                        }
                    }
                }
            }
            
           

            // VERIFICA SE EXISTE A PASTA SE NÃO CRIA.
            $dir = '..' . DIRECTORY_SEPARATOR . 'storage' . 
            DIRECTORY_SEPARATOR . 'app' . 
            DIRECTORY_SEPARATOR . 'public' . 
            DIRECTORY_SEPARATOR . 'document' . DIRECTORY_SEPARATOR .session('companyselected')['id']. DIRECTORY_SEPARATOR;

            if (is_dir($dir)) {
            } else {
                mkdir($dir, 0755, true);
            }
            // (new \DateTime())->format('Y-m-d-H-i-s')
            $writer = new WriterXlsx($spreadSheet);
            $writer->save($dir.'agentsInfos.xlsx');

            $src = 'public' . DIRECTORY_SEPARATOR . 'document' . DIRECTORY_SEPARATOR . 'agentsInfos';

            if(config('app.env') == 'sandbox'){ // IO
                $link = 'https://ba-support.builderall.io/'.$src;
                // $link = 'http://localhost:8000/'.$src;
            }else if(config('app.is_helpdesk')){ //.HS
                $link = 'https://hs.builderall.com/'.$src;
            }else{ //.COM
                $link = 'https://ba-support.builderall.com/'.$src;
            }

            return $link;
        }

        public function generateListCT($id, $type){
            // MODELO PARA GERAR EXCEL - MURILO
            $file = new Spreadsheet();
            
            $chat['result'] = DB::table('chat_history')
            ->leftJoin("chat" , "chat.id" , "chat_history.chat_id")
			->leftJoin("user_auth as ua_agent"  , "ua_agent.id" , "chat_history.created_by")
            ->select('chat_history.id', 'chat_history.chat_id', 'chat_history.content', 'chat_history.type', 'chat_history.content_translated',
            'chat_history.created_at', 'ua_agent.name')
            ->where('hidden_for_client',0)
            ->where('chat_id',$id)
            // ->where('chat_history.id',628814)
            ->get();

            foreach ($chat['result'] as $key) {
                $date=  gmdate('H:i:s', strtotime($key->created_at));
                $date = new DateTimeImmutable($key->created_at);
                $key->created_at = $date->format('H:i:s d-m-Y');
            }

            $styleArray = [
                'font' => [
                    'bold' => true,
                ],
            ];

            $aba0 = $file->getActiveSheet()
            ->fromArray(
                [1, 2, 3],  // The data to set
                NULL,        // Array values with this value will not be set
                'A1'         // Top left coordinate of the worksheet range where
                                //    we want to set these values (default is A1)
            );
            $aba0->setTitle(Feedback::t('bs-agents'));
            $aba0->getStyle('A1:Z1')->applyFromArray($styleArray);

            $aba0 = $file->getActiveSheet();
            $aba0->setTitle('Chat '. $id);
            $aba0->setCellValue('A1', Feedback::t('bs-chat'));
            $aba0->setCellValue('B1', Feedback::t('bs-date'));
            $aba0->setCellValue('C1', Feedback::t('bs-text'));
            $aba0->setCellValue('D1', Feedback::t('bs-translation'));
            $count = 1;

            // SE FOR TICKET - ADICIONAR DESCRIPTION
            if($type == 'TICKET'){
                $count++;  
                $description = DB::table('chat')
                ->leftJoin("ticket" , "chat.ticket_id" , "ticket.id")
                ->select('chat.id as chat_id', 'ticket.description', 'ticket.created_at')
                ->where('chat.id',$id)
                ->first();

                $date=  gmdate('H:i:s', strtotime($description->created_at));
                $date = new DateTimeImmutable($description->created_at);
                $description->created_at = $date->format('H:i:s d-m-Y');
       
                $aba0->setCellValue('A'.$count, $description->chat_id);
                $aba0->setCellValue('B'.$count, $description->created_at);
                $aba0->setCellValue('C'.$count, strip_tags($description->description));

            }

            // PERGUNTAS INICIAIS 
            $ticket_chat_answer = DB::table('ticket_chat_answer as tt')
            ->join('company_depart_settings_question as cc', 'tt.company_depart_settings_question_id', 'cc.id')
            ->select('tt.id', 'tt.chat_id', 'question', 'answer', 'tt.created_at')
            ->where('tt.chat_id', $id)
            ->get();
            
            foreach ($ticket_chat_answer as $key) {
                $date=  gmdate('H:i:s', strtotime($key->created_at));
                $date = new DateTimeImmutable($key->created_at);
                $key->created_at = $date->format('H:i:s d-m-Y');
            }

            foreach ($ticket_chat_answer as $key) {
                $count++;  
                $aba0->setCellValue('A'.$count, $key->chat_id);
                $aba0->setCellValue('B'.$count, $key->created_at);
                $aba0->setCellValue('C'.$count, $key->question);
                $count++;  
                $aba0->setCellValue('A'.$count, $key->chat_id);
                $aba0->setCellValue('B'.$count, $key->created_at);
                $aba0->setCellValue('C'.$count, strip_tags($key->answer));
            }
            // PERGUNTAS INICIAIS 
            
            //CHAT_HISTORY
            foreach ($chat['result'] as $key) {

                $count++;
                $aba0->setCellValue('A'.$count, $key->chat_id);
                $aba0->setCellValue('B'.$count, $key->created_at);
                $aba0->setCellValue('D'.$count, $key->content_translated);
                

                if($key->type == 'EVENT'){
                    $aba0->setCellValue('C'.$count, Feedback::t(strip_tags($key->content)));
                }else if($key->type == 'TEXT'){
                    if (str_contains($key->content, '<img')) {
                        $html = $key->content;
                        $doc = new DOMDocument();
                        $doc->loadHTML($html);
                        $xpath = new DOMXPath($doc);
                        $src = $xpath->evaluate("string(//img/@src)"); # "/images/image.jpg"

                        if(config('app.env') == 'sandbox'){ // IO
                            $key->content = 'https://ba-support.builderall.io/public/'.$src;
                        }else if(config('app.is_helpdesk')){ //.HS
                            $key->content = 'https://hs.builderall.com/public/'.$src;
                        }else{ //.COM
                            $key->content = 'https://ba-support.builderall.com/public/'.$src;
                            // $key->content = 'http://localhost:8000/public/'.$src;
                        }
                    }
                    $aba0->setCellValue('C'.$count, $key->content);
                }else if($key->type == 'FILE'){
                    $auxlink = "";
                    if(json_decode($key->content) != null){ 
                        // PEGA AS IMAGENS NO CTRL + V
                        if (str_contains(json_decode($key->content)->message, '<img')) {
                            $doc = new DOMDocument();   
                            $doc->loadHTML(json_decode($key->content)->message);    
                            $xpath = new DOMXPath($doc);    
                            $images = $xpath->evaluate("//img");
                            $text = strip_tags(json_decode($key->content)->message);
                        
                            foreach ($images as $image) {
                                $src = $image->getAttribute('src');
                                if(config('app.env') == 'sandbox'){ // IO
                                    $auxlink = $text.' '.$auxlink.' '.'https://ba-support.builderall.io/public/'.$src;
                                    // $auxlink = 'http://localhost:8000/public/'.$src;
                                }else if(config('app.is_helpdesk')){ //.HS
                                    $auxlink = $text.' '.$auxlink.' '.'https://hs.builderall.com/public/'.$src;
                                }else{ //.COM
                                    $auxlink = $auxlink.' '.'https://ba-support.builderall.com/public/'.$src;
                                    // $auxlink = $text.' '.$auxlink.' '.'http://localhost:8000/public/'.$src;
                                }
                            }
                        }
                        // PEGA AS IMAGENS IMPORTADAS PELO BOTÃO
                        foreach(json_decode($key->content)->files as $image){
                            if(config('app.env') == 'sandbox'){ // IO
                                // $key->content = json_decode($key->content)->message.' '.'https://ba-support.builderall.io/public/'.Crypt::encrypt($key->chat_id).'/'.$image->unique_name;
                                $auxlink = $auxlink.' '.'http://localhost:8000/public/chat/files/'.Crypt::encrypt($id).'/'.$image->unique_name;
                            }else if(config('app.is_helpdesk')){ //.HS
                                $auxlink = $auxlink.' '.'https://hs.builderall.com/public/chat/files/'.Crypt::encrypt($id).'/'.$image->unique_name;
                            }else{ //.COM
                                $auxlink = $auxlink.' '.'https://ba-support.builderall.com/public/chat/files/'.Crypt::encrypt($id).'/'.$image->unique_name;
                                // $auxlink = $auxlink.' '.'http://localhost:8000/public/chat/files/'.Crypt::encrypt($id).'/'.$image->unique_name;
                            }
                        }
                        $key->content = $auxlink;
                        
                    }
                    $aba0->setCellValue('C'.$count, strip_tags($key->content));

                }else if($key->type == 'ROBOT'){
                    $aba0->setCellValue('C'.$count, json_decode($key->content)->text);
                }else{
                    $aba0->setCellValue('C'.$count, strip_tags($key->content));
                }

            }


            // $aba1 = new Worksheet($file, 'Nome 1');
            // $file->addSheet($aba1);
            // $aba1->setCellValue('A1', 'Hello World Aba 1!');
            
            // $aba2 = new Worksheet($file, 'Nome 2');
            // $file->addSheet($aba2);
            // $aba2->setCellValue('A1', 'Hello World Aba 2!');
            /**
             * Remove sheet
             */
            // $file->removeSheetByIndex($sheetIndex);
           

            // VERIFICA SE EXISTE A PASTA SE NÃO CRIA.
            $dir = '..' . DIRECTORY_SEPARATOR . 'storage' . 
            DIRECTORY_SEPARATOR . 'app' . 
            DIRECTORY_SEPARATOR . 'public' . 
            DIRECTORY_SEPARATOR . 'document' . DIRECTORY_SEPARATOR;

            if (is_dir($dir)) {
            } else {
                mkdir($dir, 0755, true);
            }
            // (new \DateTime())->format('Y-m-d-H-i-s')
            $writer = new WriterXlsx($file);
            $writer->save($dir.'history.xlsx');

            $src = 'public' . DIRECTORY_SEPARATOR . 'document' . DIRECTORY_SEPARATOR . 'history';

            if(config('app.env') == 'sandbox'){ // IO
                $link = 'https://ba-support.builderall.io/'.$src;
            }else if(config('app.is_helpdesk')){ //.HS
                $link = 'https://hs.builderall.com/'.$src;
            }else{ //.COM
                $link = 'https://ba-support.builderall.com/'.$src;
                // $link = 'http://localhost:8000/'.$src;
            }

            return $link;
        }

        public function teste(){
            // MODELO PARA GERAR EXCEL - MURILO
            $file = new Spreadsheet();
            $aba0 = $file->getActiveSheet();
            $aba0->setTitle('Nome 0');
            $aba0->setCellValue('A1', 'Nome');
            $aba0->setCellValue('A2', 'SobreNome');
            $aba1 = new Worksheet($file, 'Nome 1');
            $file->addSheet($aba1);
            $aba1->setCellValue('A1', 'Hello World Aba 1!');
            $aba2 = new Worksheet($file, 'Nome 2');
            $file->addSheet($aba2);
            $aba2->setCellValue('A1', 'Hello World Aba 2!');
            /**
            * Remove sheet
            */
            // $file->removeSheetByIndex($sheetIndex);
            $writer = new WriterXlsx($file);
            $writer->save('document/file.xlsx');
        }
    }
    

?>