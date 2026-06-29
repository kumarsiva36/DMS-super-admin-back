<?php

namespace App\Http\Controllers\WebSite\Inquiry;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\FabricType;
use App\Models\InquiryLog;
use App\Models\InquiryMasterModel;
use App\Models\OrderArticles;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InquiryMaster extends Controller
{
    /* To List all the non-default Inquiry Values */
    public static function inquiryMaster(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $whereConditions[] =['inq_reference_id','!=',"0"];
        if(isset($request->type) && $request->type!=""){
            $whereConditions[]=['type','=',$request->type];
        }
        if(isset($request->startDate) && isset($request->endDate) && $request->startDate != "" && $request->endDate == ""){
            $from = date('Y-m-d 00:00:00',strtotime($request->startDate));
            $to = date('Y-m-d 23:59:59');
            $whereConditions[]=['inquiry_master.created_at','>=',$from];
            $whereConditions[]=['inquiry_master.created_at','<=',$to];
        }
        if(isset($request->startDate) && isset($request->endDate) && $request->startDate != "" && $request->endDate != ""){
            $from = date('Y-m-d 00:00:00',strtotime($request->startDate));
            $to = date('Y-m-d 23:59:59',strtotime($request->endDate));
            $whereConditions[]=['inquiry_master.created_at','>=',$from];
            $whereConditions[]=['inquiry_master.created_at','<=',$to];
        }
        $inquiryMaster = InquiryMasterModel::inquiryMasterDetails($whereConditions,$request);

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
            InquiryMasterModel::inquiryDefault($request);
            $res = json_encode(["status_code"=>200,"status" =>"success","data"=>"Added Successfully"]);
            return CommonApp::webEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>400,"status" =>"success","error"=>$e]);
            return CommonApp::webEncrypt($res);
        }
    }

    /* To Get the inquiry Articles */
    public static function getInquiryArticles(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $whereConditions[] =['inquiry_reference_id','!=',"0"];
       // $whereConditions[] =['is_default','!=',"0"];

        $articles = OrderArticles::inquiryArticle($whereConditions,$request);

        $res = json_encode(["status_code"=>200,"status" =>"success","data"=>$articles]);
        return CommonApp::webEncrypt($res);
    }

    /* Add Article as a Default */
    public static function addInquiryArticlesDefault(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            "id" => 'required',
        ]);
        if($validated->fails()){
           $res = json_encode((["status_code"=>401,"status" =>"failed","validation_error"=>$validated->errors()]));
           return CommonApp::webEncrypt($res);
        }
        try{
            OrderArticles::defaultArticle($request);
            $res = json_encode(["status_code"=>200,"status" =>"success","data"=>"Added Successfully"]);
            return CommonApp::webEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>400,"status" =>"success","error"=>$e]);
            return CommonApp::webEncrypt($res);
        }
    }

    /* To Get the inquiry Fabric */
    public static function getInquiryFabrics(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $whereConditions[] =['inquiry_reference_id','!=',"0"];
        $whereConditions[] =['is_default','!=',"0"];

        $fabrics = FabricType::inquiryFabric($whereConditions,$request);

        $res = json_encode(["status_code"=>200,"status" =>"success","data"=>$fabrics]);
        return CommonApp::webEncrypt($res);
    }

    /* Add Fabric as a Default */
    public static function addInquiryFabricsDefault(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            "id" => 'required',
        ]);
        if($validated->fails()){
           $res = json_encode((["status_code"=>401,"status" =>"failed","validation_error"=>$validated->errors()]));
           return CommonApp::webEncrypt($res);
        }
        try{
            FabricType::defaultFabric($request);
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
        $logs = InquiryLog::inquiry_logs($request);
        $res = json_encode(["status_code"=>200,"status" =>"success","data"=>$logs]);
        return CommonApp::webEncrypt($res);
    }

    /* To Get the inquiry Ids */
    public static function getInquiryIds(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $ids = InquiryLog::getInquiryIds($request);
        $res = json_encode(["status_code"=>200,"status" =>"success","data"=>$ids]);
        return CommonApp::webEncrypt($res);
    }

    /* To Download the inquiry logs */
    public static function download_inquiryLogs(Request $request){
        ($request->getContent()!="") ? $request= CommonApp::webDecrypt($request->getContent()):$request;
        $result = InquiryLog::download_inquiry_logs($request);
        view()->share(["result"=>$result,"request"=>$request]);
        $pdf = Pdf::loadView('InquiryLogPDF');
        $pdf->getOptions()->setIsFontSubsettingEnabled(true);
        $pdf->setOption(['defaultFont' => 'arialuni','poppins','notoSansJP']);
        return $pdf->download();
    }

    /* To List all the non-default Inquiry Values */
    public static function poMaster(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $whereConditions[] =['inq_reference_id','!=',"0"];
        if(isset($request->type) && $request->type!=""){
            $whereConditions[]=['type','=',$request->type];
        }
        if(isset($request->startDate) && isset($request->endDate) && $request->startDate != "" && $request->endDate == ""){
            $from = date('Y-m-d 00:00:00',strtotime($request->startDate));
            $to = date('Y-m-d 23:59:59');
            $whereConditions[]=['inquiry_master.created_at','>=',$from];
            $whereConditions[]=['inquiry_master.created_at','<=',$to];
        }
        if(isset($request->startDate) && isset($request->endDate) && $request->startDate != "" && $request->endDate != ""){
            $from = date('Y-m-d 00:00:00',strtotime($request->startDate));
            $to = date('Y-m-d 23:59:59',strtotime($request->endDate));
            $whereConditions[]=['inquiry_master.created_at','>=',$from];
            $whereConditions[]=['inquiry_master.created_at','<=',$to];
        }
        $inquiryMaster = InquiryMasterModel::poMasterDetails($whereConditions,$request);

        $res = json_encode(["status_code"=>200,"status" =>"success","data"=>$inquiryMaster]);
        return CommonApp::webEncrypt($res);
    }

    /* To Get the PO Articles */
    public static function getPOArticles(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $whereConditions[] =['inquiry_reference_id','!=',"0"];
       // $whereConditions[] =['is_default','!=',"0"];

        $articles = OrderArticles::poArticle($whereConditions,$request);

        $res = json_encode(["status_code"=>200,"status" =>"success","data"=>$articles]);
        return CommonApp::webEncrypt($res);
    }

    /* To Get the PO Fabric */
    public static function getPOFabrics(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $whereConditions[] =['inquiry_reference_id','!=',"0"];
        $whereConditions[] =['is_default','!=',"0"];

        $fabrics = FabricType::poFabric($whereConditions,$request);

        $res = json_encode(["status_code"=>200,"status" =>"success","data"=>$fabrics]);
        return CommonApp::webEncrypt($res);
    }

}
