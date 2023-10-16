<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Tools\Builderall\BuilderallAccount;
use Illuminate\Http\Request;

class ContentController extends Controller
{

    public function inactivate(Request $request, $uuid)
    {
        BuilderallAccount::activeOrInactivateAccount($uuid, $request->token ?? '', BuilderallAccount::INACTIVE);
        return response()->json(['success' => true, 'message' => "User '$uuid' was inactivated."]);
    }

    public function activate(Request $request, $uuid)
    {
        BuilderallAccount::activeOrInactivateAccount($uuid, $request->token ?? '', BuilderallAccount::ACTIVE);
        return response()->json(['success' => true, 'message' => "User '$uuid' was activated."]);

    }
}
