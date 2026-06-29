<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Workspace;
use App\Models\User;
use App\Models\Staff;
use App\Models\CompanySettings;
use App\Common\CommonApp;
use App\Models\ActivateDeactivateLog;
use Exception;

class UserStaffDetails extends Controller
{
    public function getUserStaffList(Request $request){
        //$request= $request->getContent();
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            "companyId" => 'required|integer',
        ]);
        if($validated->fails()){
           // return response()->json(["status_code"=>401,"status" =>"failed","validation_error"=>$validated->errors()]);
           $res = json_encode((["status_code"=>401,"status" =>"failed","validation_error"=>$validated->errors()]));
           return CommonApp::webEncrypt($res);
        }
        $whereCondition =[
            ['company_id','=',$request->companyId]
        ];
        $whereConditioncm =[
            ['id','=',$request->companyId]
        ];
        $User=User::select()->where($whereCondition)->first();
        $staff=staff::where($whereCondition)->pluck('email');
        // dd($staff);
        $StaffDetails=staff::select('staff.id as staff_id','staff.first_name','staff.last_name','staff.status','last_seen','workspace.name as workspace',
        'staff.email as email','company_settings.id as company_id','company_settings.company_name','roles.name as rolename','staff.otp as staff_otp','staff.otp_generated_time')
        ->leftjoin('company_settings','staff.company_id','company_settings.id')
        ->leftjoin('workspace','staff.workspace_id','workspace.id')
        ->leftjoin('roles','staff.role_id','roles.id')
        ->where("staff.company_id","=",$request->companyId)
        ->whereIn('staff.email',$staff)
        ->get();
        $Company=CompanySettings::where($whereConditioncm)->first();
        $data=[];
        $data['company']=$Company;
        $data['user']=$User;
        $data['staff']=$StaffDetails;
        $res = json_encode(["status_code"=>200,"status" =>"success","data"=>$data]);
        return CommonApp::webEncrypt($res);
    }
    public function getUserStaffStatus(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            "companyId" => 'required|integer',
            "userId" => 'required|integer',
            "userType" => 'required',
            "actionType" => 'required',
            "reason" => 'required',
            "adminId" => 'required|integer',
        ]);
        if($validated->fails()){
           // return response()->json(["status_code"=>401,"status" =>"failed","validation_error"=>$validated->errors()]);
           $res = json_encode((["status_code"=>401,"status" =>"failed","validation_error"=>$validated->errors()]));
           return CommonApp::webEncrypt($res);
        }
        $actionValue = "2";
        if($request->actionType=="active"){
            $actionValue = "1";
        }

        if(strtolower($request->userType)=='user'){
            $whereCondition =[
                ['company_id','=',$request->companyId],
                ['id','=',$request->userId]
            ];
            try{
                $rt= User::where($whereCondition)->update(array('status'=>$actionValue));
                ActivateDeactivateLog::activateDeactivatelogs("User",$request,$request->adminId,"UsersActivation");
                $res = json_encode(["status_code"=>200,"status" =>"success","message"=>"Successfully Updated",
                "dd"=>$actionValue,"ss"=>$whereCondition]);
            }catch(Exception $e){
                $res = json_encode(["status_code"=>401,"error"=>$e->getMessage()]);
            }
            return CommonApp::webEncrypt($res);
        }
        elseif(strtolower($request->userType)=='staff'){
            $whereCondition =[
                ['company_id','=',$request->companyId],
                ['id','=',$request->userId]
            ];
            Staff::where($whereCondition)->update(array('status'=>$actionValue));
            ActivateDeactivateLog::activateDeactivatelogs("Staff",$request,$request->adminId,"UsersActivation");
            $res = json_encode(["status_code"=>200,"status" =>"success","message"=>"Successfully Updated"]);
            return CommonApp::webEncrypt($res);
        }
        $res = json_encode(["status_code"=>400,"status" =>"failed","message"=>"Not Updated"]);
        return CommonApp::webEncrypt($res);
    }
}
