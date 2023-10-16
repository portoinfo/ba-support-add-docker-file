<?php

namespace App\Http\Controllers\HelpDesk;

use App\Http\Controllers\Controller;
use App\Tools\Builderall\DnsManager;
use App\User;
use Builderall\Authenticator\BuilderallAuth;
use Illuminate\Http\Request;

class BuilderallController extends Controller
{
    public function loginByToken(Request $request)
    {
        $request->session()->flush();
        session(['tokenOffice' => $request->token]);
        session(['suporteType' => 'helpdesk']);
        
        $data = BuilderallAuth::getUserDataByToken($request->token);
        
        if (!empty($data)) 
        {
            $user = User::createOrUpdateOfficeUser($data);
            auth()->loginUsingId($user->id);

            if (auth()->user()->getOwnCompanies(null)->count() == 0)
            {
                // redirect inicio rápido
            }

            return redirect()->intended(config('builderall.redirect_after_login'));
        }
    
        return redirect()->back()->withErrors(['credentials' => 'Credentials Error']);
    }

    public function domains()
    {
        $domains = config('app.env') == 'local' ? ['builderall.com', 'eb4us.com'] : (new DnsManager())->getUserConnectedDomains(auth()->user()->email);    
        return response()->json(['data' => $domains]);    
    }
}
