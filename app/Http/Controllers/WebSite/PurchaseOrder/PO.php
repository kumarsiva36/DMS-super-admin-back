<?php

namespace App\Http\Controllers\WebSite\PurchaseOrder;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\POLogs;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PO extends Controller
{
    /* PO Logs */
    public static function poLogs(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $logs = POLogs::getPOLogs($request);
        $res = json_encode(["status_code"=>200,"status" =>"success","data"=>$logs]);
        return CommonApp::webEncrypt($res);
    }
    public static function download_poLogs(Request $request){
        ($request->getContent()!="") ? $request= CommonApp::webDecrypt($request->getContent()):$request;
        $result = POLogs::download_getPOLogs($request);
        view()->share(["result"=>$result,"request"=>$request]);
        $pdf = Pdf::loadView('PoLogPDF');
        $pdf->getOptions()->setIsFontSubsettingEnabled(true);
        $pdf->setOption(['defaultFont' => 'arialuni','poppins','notoSansJP']);
        return $pdf->download();
    }
}
