<?php

namespace App\Http\Controllers\WebSite\Staff;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\Staff as ModelsStaff;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Staff extends Controller
{
    /* Get Staff Names while typing */
    public static function getLikeStaffs(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            "name" => 'required',
        ]);
        if($validated->fails()){
           $res = json_encode((["status_code"=>401,"status" =>"failed","validation_error"=>$validated->errors()]));
           return CommonApp::webEncrypt($res);
        }
        try{
            $staffs = ModelsStaff::getStaffSimilar($request);
            $res = json_encode(["status_code" => 200,'status'=>"success","data"=>$staffs]);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"error"=>$e->getMessage()]);
        }
        return CommonApp::webEncrypt($res);
    }

    public static function getWorkSpaceUserandStaffList(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            //"workspace_id" => 'required',
            "company_id" => 'required',
        ]);
        if($validated->fails()){
           $res = json_encode((["status_code"=>401,"status" =>"failed","validation_error"=>$validated->errors()]));
           return CommonApp::webEncrypt($res);
        }
        try{
            $staffs = ModelsStaff::getUserandStaff($request);
            $res = json_encode(["status_code" => 200,'status'=>"success","data"=>$staffs]);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"error"=>$e->getMessage()]);
        }
        return CommonApp::webEncrypt($res);
    }
}
