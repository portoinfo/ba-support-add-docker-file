<?php

namespace App\Http\Middleware;

use Closure;
use Cookie;
use Session;

class RoutesAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      
        // SERVER_NAME REQUEST_URI
        $actual_link = basename("$_SERVER[REQUEST_URI]");
        
        
        if($actual_link == 'client' || $actual_link == 'client-chat' || $actual_link == 'client-ticket') {

            if (session('is_admin') == 0 && session('is_client') == 0) {
               return redirect('/home');
            }

            if (session('is_admin') == 1) {
               return redirect('/home');
            }

            //VERIFICAR SE NÃO ESTÁ BLOQUEADO A ROTA DE ACORDO COM A CONFIGURAÇÃO DE RESTRIÇÃO
            if($actual_link == 'client-chat' || $actual_link == 'client-ticket'){
                if (session('restriction_client')[0]->chat_hide && session('restriction_client')[0]->ticket_hide) {
                    return redirect('/home');
                }
            }
            //VERIFICAR SE NÃO ESTÁ BLOQUEADO A ROTA DE ACORDO COM A CONFIGURAÇÃO DE RESTRIÇÃO
            if($actual_link == 'client-chat'){
                if (session('restriction_client')[0]->chat_hide) {
                    return redirect('/client-ticket');
                }
            }
            //VERIFICAR SE NÃO ESTÁ BLOQUEADO A ROTA DE ACORDO COM A CONFIGURAÇÃO DE RESTRIÇÃO
            if($actual_link == 'client-ticket'){
                if (session('restriction_client')[0]->ticket_hide) {
                    return redirect('/client-chat');
                }
            }

        }else if($request->dtype == 'checkout') {
            
            //BLOQUEIA DE ROTAS QUE NAO SEJA DO CLIENTE 
            $url = explode('?', $actual_link);

            if($url[0] == 'client' || $url[0] == 'client-chat' || $url[0] == 'client-ticket') {
                if (session('is_admin') == 0 && session('is_client') == 0) {
                   return redirect('/home');
                }
            }else if(session('is_client') == 1) {
                return redirect('/client'); 
            }

        }else if(session('is_client') == 1) {            
            if($actual_link == 'list-departments'){
                return redirect('/home'); 
            }
            if($actual_link == 'list-agents'){
                return redirect('/home'); 
            }
            if($actual_link == 'edit-company'){
                return redirect('/home'); 
            }
            if($actual_link == 'select-company'){
                return redirect('/home'); 
            }

            if($actual_link == 'company'){
                return redirect('/home'); 
            }else if($actual_link == 'department'){
                return redirect('/home'); 
            }else if($actual_link == 'group'){
                return redirect('/home'); 
            }else if($actual_link == 'agents'){
                return redirect('/home'); 
            }else if($actual_link == 'company-integration'){
                return redirect('/home'); 
            }else if($actual_link == 'user-client'){
                return redirect('/home'); 
            }else if($actual_link == 'monitoring'){
                return redirect('/home'); 
            }
        }

        if (session('is_admin') == 0) {
            if($actual_link == 'company'){
                if(session('restriction')[0]->company == 1){
                }else{
                   return redirect('/home'); 
                }
            }else if($actual_link == 'department'){
                if(session('restriction')[0]->department == 1){
                }else{
                   return redirect('/home'); 
                }
            }else if($actual_link == 'group'){
                if(session('restriction')[0]->group == 1){
                }else{
                   return redirect('/home'); 
                }
            }else if($actual_link == 'agents'){
                if(session('restriction')[0]->agents == 1){
                }else{
                    return redirect('/home'); 
                }
            }else if($actual_link == 'company-integration'){
                if(session('restriction')[0]->integration == 1){
                }else{
                    return redirect('/home'); 
                }
            }else if($actual_link == 'user-client'){
                if(session('restriction')[0]->client == 1){
                }else{
                    return redirect('/home'); 
                }
            }else if($actual_link == 'monitoring'){
                if(session('restriction')[0]->monitoring == 1){
                }else{
                    return redirect('/home'); 
                }
            }else if($actual_link == 'analyze'){
                if(session('restriction')[0]->analyze == 1){
                }else{
                    return redirect('/home'); 
                }
            }
        }
        
        return $next($request);
    }
}
//{"permissions":{"company":{"view":false,"insert":false,"edit":false,"delete":false},"department":{"view":true,"insert":false,"edit":false,"delete":false,"config":{"general":{"view":true,"insert":false,"edit":false,"delete":false},"management":{"view":false,"insert":false,"edit":false,"delete":false},"autoAnswer":{"view":false,"insert":false,"edit":false,"delete":false},"quantLimitation":{"view":false,"insert":false,"edit":false,"delete":false},"robot":{"view":false,"insert":false,"edit":false,"delete":false},"evalution":{"view":false,"insert":false,"edit":false,"delete":false},"chat":{"view":false,"insert":false,"edit":false,"delete":false},"ticket":{"view":false,"insert":false,"edit":false,"delete":false}}},"group":{"view":false,"insert":false,"edit":false,"delete":false},"agents":{"view":false,"insert":false,"edit":false,"delete":false},"chat":{"open":true,"solution":true,"close":true,"moved":true},"ticket":{"open":true,"solution":true,"close":true,"moved":true}}}
