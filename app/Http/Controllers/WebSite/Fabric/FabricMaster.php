<?php

namespace App\Http\Controllers\WebSite\Fabric;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\FabricLog;
use App\Models\FabricInquiryMasterModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FabricMaster extends Controller
{
    /* To List all the non-default Inquiry Values */
    public static function inquiryMaster(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $whereConditions[] =['fabric_master.reference_id','!=',"0"];
        if(isset($request->type) && $request->type!=""){
            $whereConditions[]=['type','=',$request->type];
        }
        if(isset($request->startDate) && isset($request->endDate) && $request->startDate != "" && $request->endDate == ""){
            $from = date('Y-m-d 00:00:00',strtotime($request->startDate));
            $to = date('Y-m-d 23:59:59');
            $whereConditions[]=['fabric_master.created_at','>=',$from];
            $whereConditions[]=['fabric_master.created_at','<=',$to];
        }
        if(isset($request->startDate) && isset($request->endDate) && $request->startDate != "" && $request->endDate != ""){
            $from = date('Y-m-d 00:00:00',strtotime($request->startDate));
            $to = date('Y-m-d 23:59:59',strtotime($request->endDate));
            $whereConditions[]=['fabric_master.created_at','>=',$from];
            $whereConditions[]=['fabric_master.created_at','<=',$to];
        }
        $request->page = $request->page ?? 1;
        $inquiryMaster = FabricInquiryMasterModel::inquiryMasterDetails($whereConditions,$request);

        $res = json_encode(["status_code"=>200,"status" =>"success","data"=>$inquiryMaster]);
        return CommonApp::webEncrypt($res);
    }

    /* To Add a non-default value into default value */
    public static function inquiryMasterDefault(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            "id" => 'required',
        ]);
        if($validated->fails()){
           $res = json_encode((["status_code"=>401,"status" =>"failed","validation_error"=>$validated->errors()]));
           return CommonApp::webEncrypt($res);
        }
        try{
            FabricInquiryMasterModel::inquiryDefault($request);
            $res = json_encode(["status_code"=>200,"status" =>"success","data"=>"Added Successfully"]);
            return CommonApp::webEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>400,"status" =>"success","error"=>$e]);
            return CommonApp::webEncrypt($res);
        }
    }


    /* To Get the inquiry logs */
    public static function inquiryLogs(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $logs = FabricLog::inquiry_logs($request);
        $res = json_encode(["status_code"=>200,"status" =>"success","data"=>$logs]);
        return CommonApp::webEncrypt($res);
    }

    /* To Get the inquiry Ids */
    public static function getInquiryIds(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $ids = FabricLog::getInquiryIds($request);
        $res = json_encode(["status_code"=>200,"status" =>"success","data"=>$ids]);
        return CommonApp::webEncrypt($res);
    }
    /* To Download the inquiry logs */
    public static function download_inquiryLogs(Request $request){
        ($request->getContent()!="") ? $request= CommonApp::webDecrypt($request->getContent()):$request;
        $result = FabricLog::download_inquiry_logs($request);
        view()->share(["result"=>$result,"request"=>$request]);
        $pdf = Pdf::loadView('FabricLogPDF');
        $pdf->getOptions()->setIsFontSubsettingEnabled(true);
        $pdf->setOption(['defaultFont' => 'arialuni','poppins','notoSansJP']);
        return $pdf->download();
    }
}
