<?php

namespace App\Http\Controllers\WebSite\Company;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\CompanySettings;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class Company extends Controller
{
    /* Get Companies Names while typing */
    public static function getLikeCompanies(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            "name" => 'required',
        ]);
        if($validated->fails()){
           $res = json_encode((["status_code"=>401,"status" =>"failed","validation_error"=>$validated->errors()]));
           return CommonApp::webEncrypt($res);
        }
        try{
            $companies = CompanySettings::getCompanySimilar($request);
            $res = json_encode(["status_code" => 200,'status'=>"success","data"=>$companies]);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"error"=>$e->getMessage()]);
        }
        return CommonApp::webEncrypt($res);
    }

    public static function getComapnayStorageList(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());

        try{
            $companies = CompanySettings::getCompanyStorage($request);
            $res = json_encode(["status_code" => 200,'status'=>"success","data"=>$companies]);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"error"=>$e->getMessage()]);
        }
        return CommonApp::webEncrypt($res);
    }
    public static function updateComapnaySettings(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            "companyId" => 'required',
            "no_of_user" => 'required',
            "no_of_style" => 'required',
            "no_of_workspace" => 'required',
            "max_storage_size" => 'required',
            "account_expire_at"=> 'required',
        ]);
        if($validated->fails()){
           $res = json_encode((["status_code"=>401,"status" =>"failed","validation_error"=>$validated->errors()]));
           return CommonApp::webEncrypt($res);
        }
        try{
            $update_arr['no_of_user']=$request->no_of_user;
            $update_arr['no_of_style']=$request->no_of_style;
            $update_arr['no_of_workspace']=$request->no_of_workspace;
            $update_arr['max_storage_size']=$request->max_storage_size;
            $update_arr['account_expire_at']=date('Y-m-d',strtotime($request->account_expire_at));
            CompanySettings::where('id',$request->companyId)->update($update_arr);
            $res = json_encode(["status_code" => 200,'status'=>"success","message"=>"Details updated successfully"]);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"error"=>$e->getMessage()]);
        }
        return CommonApp::webEncrypt($res);
    }
}
