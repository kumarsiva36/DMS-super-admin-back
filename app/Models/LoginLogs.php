<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Staff;
use App\Models\User;

class LoginLogs extends Model
{
    use HasFactory;

    protected $table = 'user_logging_history';

    public static function getloginlogs($request){
        $whereCondition =[
            ['user_logging_history.company_id','>','0']
        ];
        if(isset($request->company_id) && $request->company_id!=''){
            $whereCondition[] =['user_logging_history.company_id',$request->company_id];
        }
        if(isset($request->user_id) && $request->user_id!=''){
            $whereCondition[] =['user_logging_history.logging_user_id',$request->user_id];
            $whereCondition[] =['user_logging_history.login_user_type','User'];
        }
        if(isset($request->staff_id) && $request->staff_id!=''){
            $whereCondition[] =['user_logging_history.logging_user_id',$request->staff_id];
            $whereCondition[] =['user_logging_history.login_user_type','Staff'];
        }
        if(isset($request->user_type) && $request->user_type!=''){
            $whereCondition[] =['user_logging_history.login_user_type',$request->user_type];
        }
        if(isset($request->start_date) && $request->start_date!=''){
            $whereCondition[] =['user_logging_history.created_at',">=",date('Y-m-d 00:00:00',strtotime($request->start_date))];
        }
        if(isset($request->end_date) && $request->end_date!=''){
            $whereCondition[] =['user_logging_history.created_at',"<=",date('Y-m-d 23:59:59',strtotime($request->end_date))];
        }
        if(isset($request->device) && $request->device!=''){
            if($request->device=='Web'){
                $whereCondition[] =['user_logging_history.browser_details',"like",'%Mozilla%'];
            }else{
                $whereCondition[] =['user_logging_history.browser_details',"like",'%"'.$request->device.'"%'];
            }
        }
        $page = (isset($request->page) && $request->page!='')?$request->page:1;
        $result = LoginLogs::where($whereCondition)
        ->join('company_settings','company_settings.id','user_logging_history.company_id')
        ->select('company_settings.company_name','user_logging_history.logging_user_name as username','ipaddress','browser_details','login_status',
        'login_user_type',DB::raw('DATE_FORMAT(user_logging_history.logging_in_datetime,"%d %b %Y %H:%i") as login_date'))
        ->orderBy('user_logging_history.id','DESC')
        // ->paginate(30);
       ->paginate(30, ['*'], 'page', $page);

        return $result;
    }

    public static function getLastloginlogs($request){
        $whereCondition =[
            ['user_logging_history.company_id','>','0'],
            // ['user_logging_history.login_status','=','Success'],
            // ['user_logging_history.log_type','=','Login'],
            // ['user_logging_history.logged_out_datetime','=',null],


        ];
        if(isset($request->company_id) && $request->company_id!=''){
            $whereCondition[] =['user_logging_history.company_id',$request->company_id];
        }
        if(isset($request->user_id) && $request->user_id!=''){
            $whereCondition[] =['user_logging_history.logging_user_id',$request->user_id];
            $whereCondition[] =['user_logging_history.login_user_type','User'];
        }
        if(isset($request->staff_id) && $request->staff_id!=''){
            $whereCondition[] =['user_logging_history.logging_user_id',$request->staff_id];
            $whereCondition[] =['user_logging_history.login_user_type','Staff'];
        }
        if(isset($request->user_type) && $request->user_type!=''){
            $whereCondition[] =['user_logging_history.login_user_type',$request->user_type];
        }
        if(isset($request->start_date) && $request->start_date!=''){
            $whereCondition[] =['user_logging_history.created_at',">=",date('Y-m-d 00:00:00',strtotime($request->start_date))];
        }
        if(isset($request->end_date) && $request->end_date!=''){
            $whereCondition[] =['user_logging_history.created_at',"<=",date('Y-m-d 23:59:59',strtotime($request->end_date))];
        }
        if(isset($request->device) && $request->device!=''){
            if($request->device=='Web'){
                $whereCondition[] =['user_logging_history.browser_details',"like",'%Mozilla%'];
            }else{
                $whereCondition[] =['user_logging_history.browser_details',"like",'%"'.$request->device.'"%'];
            }
        }
        $page = (isset($request->page) && $request->page!='')?$request->page:1;
        $result = LoginLogs::where($whereCondition)
        ->join('company_settings','company_settings.id','user_logging_history.company_id')
        ->select('user_logging_history.id','user_logging_history.company_id','user_logging_history.logging_user_id','company_settings.company_name','user_logging_history.logging_user_name as username','ipaddress','browser_details','login_status',
        'login_user_type',DB::raw('DATE_FORMAT(user_logging_history.logging_in_datetime,"%d %b %Y %H:%i") as login_date'))
        //->groupBy('user_logging_history.company_id','user_logging_history.logging_user_id','user_logging_history.login_user_type')
        ->orderBy('user_logging_history.id','DESC')
        // ->paginate(30);
       ->paginate(30, ['*'], 'page', $page);
//dd($result);
        return $result;
    }

    public static function getLastloginlogout($request){

        if(strtolower($request->user_type)=='user'){
            //$user = User::where("id",$request->id);

            //$token = $user->user()->token();
           // $token->revoke();
          // return $token = $user->revokeToken('APItoken')->accessToken;
            // $user->token()->delete();
            /*$userToLogout = User::find($request->user_id);
            Auth::setUser($userToLogout);
            Auth::logout();*/
            DB::table('oauth_access_tokens')->where('user_id',$request->user_id)->where('name','APItoken')->update(array('revoked'=>1));
            self::where("logging_user_id",$request->user_id)->where("login_user_type","User")->where("log_type","Login")->where("login_status","Success")->update(array("logged_out_datetime"=>date("Y-m-d H:i:s")));

        }else{
           // $staff = Staff::where("id",$request->id);
           // return $token = $staff->revokeToken('StaffAPIToken')->accessToken;
           // $staff->token()->delete();
           /* $staffToLogout = Staff::find($request->user_id);
            Auth::setUser($staffToLogout);
            Auth::logout();*/
            DB::table('oauth_access_tokens')->where('user_id',$request->user_id)->where('name','StaffAPIToken')->update(array('revoked'=>1));
            self::where("logging_user_id",$request->user_id)->where("login_user_type","Staff")->where("log_type","Login")->where("login_status","Success")->update(array("logged_out_datetime"=>date("Y-m-d H:i:s")));

        }

    }

    public static function downloadgetLoginlogs($request){
        $whereCondition =[
            ['user_logging_history.company_id','>','0']
        ];
        if(isset($request->company_id) && $request->company_id!=''){
            $whereCondition[] =['user_logging_history.company_id',$request->company_id];
        }
        if(isset($request->user_id) && $request->user_id!=''){
            $whereCondition[] =['user_logging_history.logging_user_id',$request->user_id];
            $whereCondition[] =['user_logging_history.login_user_type','User'];
        }
        if(isset($request->staff_id) && $request->staff_id!=''){
            $whereCondition[] =['user_logging_history.logging_user_id',$request->staff_id];
            $whereCondition[] =['user_logging_history.login_user_type','Staff'];
        }
        if(isset($request->user_type) && $request->user_type!=''){
            $whereCondition[] =['user_logging_history.login_user_type',$request->user_type];
        }
        if(isset($request->start_date) && $request->start_date!=''){
            $whereCondition[] =['user_logging_history.created_at',">=",date('Y-m-d 00:00:00',strtotime($request->start_date))];
        }
        if(isset($request->end_date) && $request->end_date!=''){
            $whereCondition[] =['user_logging_history.created_at',"<=",date('Y-m-d 23:59:59',strtotime($request->end_date))];
        }
        if(isset($request->device) && $request->device!=''){
            if($request->device=='Web'){
                $whereCondition[] =['user_logging_history.browser_details',"like",'%Mozilla%'];
            }else{
                $whereCondition[] =['user_logging_history.browser_details',"like",'%"'.$request->device.'"%'];
            }
        }
        $result = LoginLogs::where($whereCondition)
        ->join('company_settings','company_settings.id','user_logging_history.company_id')
        ->select('company_settings.company_name','user_logging_history.logging_user_name as username','ipaddress','browser_details','login_status',
        'login_user_type',DB::raw('DATE_FORMAT(user_logging_history.logging_in_datetime,"%d %b %Y %H:%i") as login_date'))
        ->orderBy('user_logging_history.id','DESC')
        ->get();

        return $result;

    }


}
