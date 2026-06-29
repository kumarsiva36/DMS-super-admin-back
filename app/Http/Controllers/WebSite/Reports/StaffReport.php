<?php

namespace App\Http\Controllers\WebSite\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Stafflog;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Common\CommonApp;

class StaffReport extends Controller
{
    public static function StaffLogs(Request $request){
        ($request->getContent()!="") ? $request= CommonApp::webDecrypt($request->getContent()):$request; 
        $result = Stafflog::getstafflogs($request);
        //return json_encode(["status_code"=>200,"status" =>"success","data"=>$result]);
        $res = json_encode(["status_code"=>200,"status" =>"success","data"=>$result]);
        return CommonApp::webEncrypt($res);
    }
    public static function downloadStaffLogs(Request $request){
        ($request->getContent()!="") ? $request= CommonApp::webDecrypt($request->getContent()):$request; 
        $result = Stafflog::downloadgetStafflogs($request);
        view()->share(["result"=>$result,"request"=>$request]);
        //return view("StaffLogPDF",["result"=>$result,"request"=>$request]);
        $pdf = Pdf::loadView('StaffLogPDF');
        $pdf->getOptions()->setIsFontSubsettingEnabled(true);
        $pdf->setOption(['defaultFont' => 'arialuni','poppins','notoSansJP']);
        return $pdf->download();
    }
}
