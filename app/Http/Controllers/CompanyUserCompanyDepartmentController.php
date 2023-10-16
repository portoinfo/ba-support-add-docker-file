<?php

namespace App\Http\Controllers;

//use App\CompanyUserCompanyDepartment;

use App\CompanyUserCompanyDepartment;
use App\Models\Company_department;
use App\Tools\Builderall\Logger;
use App\Tools\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

//use Illuminate\Http\Request;

class CompanyUserCompanyDepartmentController extends Controller
{
    public function getDepartmentsByAgent()
    {
        try {
            
            $result = Company_department::join('company_user_company_department', 'company_user_company_department.company_department_id', 'company_department.id')
                ->join('company_user', 'company_user_company_department.company_user_id', 'company_user.id')
                ->select('company_department.id', 'company_department.name', 'company_department.type', 'company_department.name as text', 'company_department.id as value')
                ->where('company_user.id', Crypt::decrypt(session('companyselected')['company_user_id']))
                ->where('company_user_company_department.is_active', 1)
                ->orderBy('name')
                ->get();

            foreach ($result as $row) {
                $row->id = Crypt::encrypt($row->id);
                $row->value = $row->id;
            }

            return response()->json($result);

        } catch (\Exception $e) {
            Logger::reportException($e, [], ['company_user_company_department-controller', 'getDepartmentsByAgent'], false);
        }
    }

    public function addAgentToDepartment(Request $request, $session = null)
    {
        /** begin */
        try {
            /** validator */
            $validator = Validator::make($request->all(), [
                'company_department_id' => 'required|string',
            ]);
            
            if ($validator->fails()) {
                return response($validator->errors(), 400);
            }
            /** create register */
            $company_user_company_department_id = DB::table('company_user_company_department')->insertGetId([
                'company_user_id' => is_null($session) ? (int) Crypt::decrypt(session('companyselected')['company_user_id']) : (int) Crypt::decrypt($session[0]->company_user_id),
                'company_department_id' => (int) Crypt::decrypt($request->company_department_id),
            ]);
            /** update session */
            if ($company_user_company_department_id) {
                /** store session on a variable */
                $session = is_null($session) ? session('company_user_company_departments') : $session;
                /** push the new value do array */
                array_push($session, (object) [
                    "company_department_id" => $request->company_department_id,
                    "company_user_company_department_id" => Crypt::encrypt($company_user_company_department_id),
                ]);
                /** update session */
                is_null($session) ? session(['company_user_company_departments' => $session]) : null;
            }
            /** response */
            return response()->json([
                'session_user_cucd' => is_null($session) ? session(['company_user_company_departments' => $session]) : null,
                'cucdic' => Crypt::encrypt($company_user_company_department_id),
            ]);
            /** send exception to logger */
        } catch (\Exception $e) {
            Logger::reportException($e, [], ['company_user_company_department-controller', 'addAgentToDepartment'], false);
        }
    }
}
