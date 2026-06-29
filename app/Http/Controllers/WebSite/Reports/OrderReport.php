<?php

namespace App\Http\Controllers\WebSite\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Orderlog;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Common\CommonApp;

class OrderReport extends Controller
{
    public static function OrderReportLogs(Request $request){
        ($request->getContent()!="") ? $request= CommonApp::webDecrypt($request->getContent()):$request; 
        $result = Orderlog::getOrderlogs($request);
        $res = json_encode(["status_code"=>200,"status" =>"success","data"=>$result]);
        return CommonApp::webEncrypt($res);
    }
    public static function downloadOrderReportLogs(Request $request){
        ($request->getContent()!="") ? $request= CommonApp::webDecrypt($request->getContent()):$request; 
        $result = Orderlog::downloadgetOrderlogs($request);
        view()->share(["result"=>$result,"request"=>$request]);
        //return view("OrderLogPDF",["result"=>$result,"request"=>$request]);
        $pdf = Pdf::loadView('OrderLogPDF');
        $pdf->getOptions()->setIsFontSubsettingEnabled(true);
        $pdf->setOption(['defaultFont' => 'arialuni','poppins','notoSansJP']);
        return $pdf->download();
    }
}
