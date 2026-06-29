<?php

namespace App\Http\Controllers\WebSite\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\PlanLogs;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Common\CommonApp;

class PlanLog extends Controller
{
    public static function PlanLogs(Request $request){
        ($request->getContent()!="") ? $request= CommonApp::webDecrypt($request->getContent()):$request;
        $result = PlanLogs::getplanlogs($request);
        return json_encode(["status_code"=>200,"status" =>"success","data"=>$result]);
        $res = json_encode(["status_code"=>200,"status" =>"success","data"=>$result]);
        return CommonApp::webEncrypt($res);
    }
    public static function downloadPlanLogs(Request $request){
        ($request->getContent()!="") ? $request= CommonApp::webDecrypt($request->getContent()):$request;
        $result = PlanLogs::downloadPlanlogs($request);
        view()->share(["result"=>$result,"request"=>$request]);
        //return view("PlanLogPDF",["result"=>$result,"request"=>$request]);
        $pdf = Pdf::loadView('PlanLogPDF');
        $pdf->getOptions()->setIsFontSubsettingEnabled(true);
        $pdf->setOption(['defaultFont' => 'arialuni','poppins','notoSansJP']);
        return $pdf->download();
    }
}
