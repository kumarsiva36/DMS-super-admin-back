<?php

namespace App\Http\Controllers\WebSite\Dashboard;

use App\Common\CommonApp;
use App\Http\Controllers\Controller;
use App\Models\CompanySettings;
use App\Models\Plan;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\Request;

class DashboardWidgets extends Controller
{
    /* Dashboard Widgets */
    public static function dashboardWidgets(Request $request){
        $data=[];
        $users = User::getUsersListCount();
        $plans = Plan::getPlanCount();
        $workspace = Workspace::getWorkspacesCount();

        $data['activeUsers']=$users['activeUsers'];
        $data['inActiveUsers']=$users['inActiveUsers'];
        $data['plan']=$plans;
        $data['workspace']=$workspace;

        $res = json_encode(["status_code"=>200,"status" =>"success","data"=>$data],200);
        return CommonApp::webEncrypt($res);
    }

    /* Get Company User and Workspace Details */
    public static function getCompanyCounts(){
        $companyDetails = CompanySettings::getTheCountsOfUsersAndWorkspaces();
        $res = json_encode(["status_code"=>200,"status" =>"success","data"=>$companyDetails],200);
        return CommonApp::webEncrypt($res);
    }

    /* Get Company Storage Details and Usage of storage */
    public static function getCompanyS3(){
        $companyDetails = CompanySettings::getPlanStorageDetails();
        $res = json_encode(["status_code"=>200,"status" =>"success","data"=>$companyDetails],200);
        return CommonApp::webEncrypt($res);
    }

    /* Get Companies with custom plan and their details */
    public static function getCustomPlanCompanies(){
        $companyDetails = CompanySettings::getCustomPlanCompanyDetails();
        $res = json_encode(["status_code"=>200,"status" =>"success","data"=>$companyDetails],200);
        return CommonApp::webEncrypt($res);
    }

    /* Get Companies with custom plan and their details */
    public static function getCompanyPlanDetailsAndExpiry(){
        $companyDetails = CompanySettings::getPlanExpiryDetails();
        $res = json_encode(["status_code"=>200,"status" =>"success","data"=>$companyDetails],200);
        return CommonApp::webEncrypt($res);
    }
}
