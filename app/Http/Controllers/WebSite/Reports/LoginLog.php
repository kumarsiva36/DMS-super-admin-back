<?php

namespace App\Http\Controllers\WebSite\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\LoginLogs;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Common\CommonApp;

class LoginLog extends Controller
{
    public static function LoginLogs(Request $request){
        ($request->getContent()!="") ? $request= CommonApp::webDecrypt($request->getContent()):$request;
        $result = LoginLogs::getloginlogs($request);
        $res = json_encode(["status_code"=>200,"status" =>"success","data"=>$result]);
        return CommonApp::webEncrypt($res);
    }
    public static function LoginLastLogs(Request $request){
        ($request->getContent()!="") ? $request= CommonApp::webDecrypt($request->getContent()):$request;
        $result = LoginLogs::getLastloginlogs($request);
        $res = json_encode(["status_code"=>200,"status" =>"success","data"=>$result]);
        return CommonApp::webEncrypt($res);
    }

    public static function LoginLastLogout(Request $request){
        ($request->getContent()!="") ? $request= CommonApp::webDecrypt($request->getContent()):$request;
        $result = LoginLogs::getLastloginlogout($request);
        $res = json_encode(["status_code"=>200,"status" =>"success","message"=>"Successfully logged out","data"=>$result]);
        return CommonApp::webEncrypt($res);
    }
    public static function downloadLoginLogs(Request $request){
        ($request->getContent()!="") ? $request= CommonApp::webDecrypt($request->getContent()):$request;
        $result = LoginLogs::downloadgetLoginlogs($request);
        view()->share(["result"=>$result,"request"=>$request]);
        //return view("LoginLogPDF",["result"=>$result,"request"=>$request]);
        $pdf = Pdf::loadView('LoginLogPDF');
        $pdf->getOptions()->setIsFontSubsettingEnabled(true);
        $pdf->setOption(['defaultFont' => 'arialuni','poppins','notoSansJP']);
        return $pdf->download();
    }
}
