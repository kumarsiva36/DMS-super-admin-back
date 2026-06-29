<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ActivateDeactivateLog extends Model
{
    use HasFactory;

    protected $table = 'activate_deactivate_logs';

    /* To Store Log of activation and deactivation */
    public static function activateDeactivatelogs($userType,$request,$changesDoneBy,$pageType){
        $log=[];
        $log['company_id']=$request->companyId;
        $log['page_type']=$pageType;
        $log['action_type']=ucfirst($request->actionType);
        $log['changed_by']=$changesDoneBy;
        $log['reason']=$request->reason;
        $log['created_at']= date('Y-m-d H:i:s');
        $log['updated_at']= date('Y-m-d H:i:s');
        if(strtolower($request->userType) == "user"){
            $log['user_id']= $request->userId;
            $log['staff_id']='0';
            $log['user_type']=$userType;
            if($request->actionType != 'active'){
                DB::table('oauth_access_tokens')->where('user_id',$request->userId)->where('name','APItoken')
                ->update(array('revoked'=>1));
                DB::table('user_logging_history')->where("logging_user_id",$request->userId)->where("login_user_type","User")
                ->where("log_type","Login")
                ->where("login_status","Success")
                ->update(array("logged_out_datetime"=>date("Y-m-d H:i:s")));
            }
        }else{
            $log['user_id']= '0';
            $log['staff_id']= $request->userId;
            $log['user_type']=$userType;
            if($request->actionType != 'active'){
                DB::table('oauth_access_tokens')->where('user_id',$request->userId)->where('name','StaffAPIToken')
                ->update(array('revoked'=>1));
                DB::table('user_logging_history')->where("logging_user_id",$request->userId)->where("login_user_type","Staff")
                ->where("log_type","Login")
                ->where("login_status","Success")
                ->update(array("logged_out_datetime"=>date("Y-m-d H:i:s")));
            }
        }

        ActivateDeactivateLog::insert($log);
    }
}
