<?php

namespace App\Http\Controllers\WebSite\Plan;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\CompanySettings;
use App\Models\PaymentHistory;
use App\Models\Plan as ModelsPlan;
use App\Models\User;
use App\Models\UserPlanHistory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class Plan extends Controller
{
    /* Get The Plan Details */
    public function index()
    {
        $plans = ModelsPlan::getAllPlans();
        $res = json_encode(["status_code" => 200,"data"=>$plans]);
        return CommonApp::webEncrypt($res);
    }

    /* Get The active companies and their existing plan and expiry details */
    public static function getAllCompanies(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $request->page = (isset($request->page) && $request->page!='')?$request->page:1;
        $companies = CompanySettings::getAllTheCompaniesAboutToExpire($request);

        $res = json_encode(["status_code" => 200,"data"=>$companies]);
        return CommonApp::webEncrypt($res);
    }

    /* To Get the list of companies for filter */
    public static function getCompaniesForFilter(){
        $companies = CompanySettings::getCompaniesForPlan();

        $res = json_encode(["status_code" => 200,"data"=>$companies]);
        return CommonApp::webEncrypt($res);
    }

    /* Update the User Plan */
    public static function updatePlan(Request $request){
        $header = $request;
        $request = CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'company_id' => 'required',
            'user_id' => 'required',
            'plan_name' => 'required',
            'plan_id' => 'required',
            'plan_price' => 'required',
            'plan_discount' => 'required',
            'plan_subtotal' => 'required',
            'plan_grandtotal' => 'required',
            'payment_currency' => 'required',
            'plan_type' => 'required',
            'payment_type' => 'required',
            'payment_status' => 'required',
            'reference_id' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code" =>401,"error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        try{
            PaymentHistory::updatePlanDetails($request,$header);
            $res = json_encode(["status_code"=>200,"status" =>"success","message"=>"Plan Updated"],200);
            return CommonApp::webEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"status" =>"Failure",
            "error"=>$e->getMessage()]);
            return CommonApp::webEncrypt($res);
        }
    }

    /* Add New Plan */
    public static function addNewPlan(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'plan_name' => ['required',Rule::unique('plan_price_details')
                ->where(function ($query) use($request) {
                    $query->where('plan_name',ucfirst($request->plan_name));
                    $query->where('type',$request->plan_type);
                    return $query;
                })],
            'plan_type' => 'required',
            'no_of_months' => 'required',
            'no_of_days' => 'required',
            'plan_price' => 'required',
            'plan_yearly_price' => 'required',
            'plan_special_price' => 'required',
            'plan_currency' => 'required',
            'no_of_group' => 'required',
            'no_of_user' => 'required',
            'no_of_style' => 'required',
            'no_of_workspace' => 'required',
            'max_storage_size' => 'required',
            'report_range' => 'required',
            'download_report' => 'required',
            'notify_email_upcoming_task' => 'required',
            'notify_email_delayed_task' => 'required',
            'notify_whatsapp_upcoming_task' => 'required',
            'notify_whatsapp_delayed_task' => 'required',
            'notify_linemessenger_upcoming_task' => 'required',
            'notify_linemessenger_delayed_task' => 'required',
            'status' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code" =>401,"errors"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        try{
            ModelsPlan::addNewPlan($request);
            $res = json_encode(["status_code"=>200,"status" =>"success","message"=>"Plan Added Successfully"],200);
            return CommonApp::webEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"status" =>"Failure",
            "error"=>$e->getMessage()]);
            return CommonApp::webEncrypt($res);
        }
    }

    /* Edit the Plan */
    public static function editPlanDetails(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'plan_id' => 'required|integer',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code" =>401,"errors"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        try{
            $thePlan = ModelsPlan::getThePlanDetails($request);
            $res = json_encode(["status_code"=>200,"status" =>"success","data"=>$thePlan],200);
            return CommonApp::webEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"status" =>"Failure",
            "error"=>$e->getMessage()]);
            return CommonApp::webEncrypt($res);
        }
    }

    /* Update The Plan */
    public static function updateThePlan(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            'plan_id' => "required",
            'plan_name' => 'required',
            'plan_type' => 'required',
            'no_of_months' => 'required',
            'no_of_days' => 'required',
            'plan_price' => 'required',
            'plan_yearly_price' => 'required',
            'plan_special_price' => 'required',
            'plan_currency' => 'required',
            'no_of_group' => 'required',
            'no_of_user' => 'required',
            'no_of_style' => 'required',
            'no_of_workspace' => 'required',
            'max_storage_size' => 'required',
            'report_range' => 'required',
            'download_report' => 'required',
            'notify_email_upcoming_task' => 'required',
            'notify_email_delayed_task' => 'required',
            'notify_whatsapp_upcoming_task' => 'required',
            'notify_whatsapp_delayed_task' => 'required',
            'notify_linemessenger_upcoming_task' => 'required',
            'notify_linemessenger_delayed_task' => 'required',
            'status' => 'required',
        ]);
        if($validated->fails()){
            $res = json_encode(["status_code" =>401,"errors"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        try{
            ModelsPlan::updateThePlan($request);
            $res = json_encode(["status_code"=>200,"status" =>"success","message"=>"Plan Added Successfully"],200);
            return CommonApp::webEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>401,"status" =>"Failure",
            "error"=>$e->getMessage()]);
            return CommonApp::webEncrypt($res);
        }
    }
}
