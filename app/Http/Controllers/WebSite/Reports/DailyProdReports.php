<?php

namespace App\Http\Controllers\WebSite\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\UpdateSkuQuantityLog;
use App\Models\Workspace;
use App\Common\CommonApp;
use Barryvdh\DomPDF\Facade\Pdf;

class DailyProdReports extends Controller
{

    public static function dailyProdReportsLogs(Request $request){
        ($request->getContent()!="") ? $request= CommonApp::webDecrypt($request->getContent()):$request;
        $result = UpdateSkuQuantityLog::update_sku_quantity_log($request);
        $res = json_encode(["status_code"=>200,"status" =>"success","data"=>$result]);
        return CommonApp::webEncrypt($res);
    }

    public static function downloaddailyProdReportsLogs(Request $request){
        ($request->getContent()!="") ? $request= CommonApp::webDecrypt($request->getContent()):$request;
        $result = UpdateSkuQuantityLog::update_sku_quantity_log_download($request);
        view()->share(["result"=>$result,"request"=>$request]);
        //return view("DataInputLogPDF",["result"=>$result,"request"=>$request]);
        $pdf = Pdf::loadView('DataInputLogPDF');
        $pdf->getOptions()->setIsFontSubsettingEnabled(true);
        $pdf->setOption(['defaultFont' => 'arialuni','poppins','notoSansJP']);
        return $pdf->download();
    }

    /* Get the company specific workspace details */
    public function getWorkspace(Request $request){
        ($request->getContent()!="") ? $request= CommonApp::webDecrypt($request->getContent()):$request;
        $where=[['company_id','>',0],['status','=','1']];
        if($request->company_id !=''){
            $where[]=['company_id','=',$request->company_id];
        }
        $workspaces = Workspace::where($where)
        ->select('id','name','workspace_type')
        ->get();
        $res = json_encode(["status_code"=>200,"status" =>"Success","data"=>$workspaces]);
        return CommonApp::webEncrypt($res);
    }
    /* Get the company details */
    public function getCompany(Request $request){
        ($request->getContent()!="") ? $request= CommonApp::webDecrypt($request->getContent()):$request;
        $Company = CommonApp::getAllCompanyDetails();
        $res = json_encode(["status_code"=>200,"status" =>"Success","data"=>$Company]);
        return CommonApp::webEncrypt($res);
    }
    /* Get the user details */
    public function getuser(Request $request){
        ($request->getContent()!="") ? $request= CommonApp::webDecrypt($request->getContent()):$request;
        $users = CommonApp::getAllUsers($request);
        $res = json_encode(["status_code"=>200,"status" =>"Success","data"=>$users]);
        return CommonApp::webEncrypt($res);
    }
    /* Get the staff details */
    public function getstaff(Request $request){
        ($request->getContent()!="") ? $request= CommonApp::webDecrypt($request->getContent()):$request;
        $staffs = CommonApp::getAllStaffs($request);
        $res = json_encode(["status_code"=>200,"status" =>"Success","data"=>$staffs]);
        return CommonApp::webEncrypt($res);
    }
    /* Get the orders details */
    public function getorders(Request $request){
        ($request->getContent()!="") ? $request= CommonApp::webDecrypt($request->getContent()):$request;
        $orders = CommonApp::getAllOrders($request);
        $res = json_encode(["status_code"=>200,"status" =>"Success","data"=>$orders]);
        return CommonApp::webEncrypt($res);
    }
}
