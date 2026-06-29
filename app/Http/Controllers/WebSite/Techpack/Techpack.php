<?php

namespace App\Http\Controllers\WebSite\Techpack;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\TechpackLogs;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class Techpack extends Controller
{
    /* Techpack Logs */
    public static function techpackLogs(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $logs = TechpackLogs::getTechpackLogs($request);
        $res = json_encode(["status_code"=>200,"status" =>"success","data"=>$logs]);
        return CommonApp::webEncrypt($res);
    }
    public static function download_techpackLogs(Request $request){
        ($request->getContent()!="") ? $request= CommonApp::webDecrypt($request->getContent()):$request;
        $result = TechpackLogs::download_techpackLogs($request);
        view()->share(["result"=>$result,"request"=>$request]);
        $pdf = Pdf::loadView('TechpackLogPDF');
        $pdf->getOptions()->setIsFontSubsettingEnabled(true);
        $pdf->setOption(['defaultFont' => 'arialuni','poppins','notoSansJP']);
        return $pdf->download();
    }
}
