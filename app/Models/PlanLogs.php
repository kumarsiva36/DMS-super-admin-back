<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PlanLogs extends Model
{
    use HasFactory;

    protected $table = 'user_plan_history_log';

    public static function getplanlogs($request){
        $whereCondition =[            
            ['user_plan_history_log.company_id','>','0']
        ];
        if(isset($request->company_id) && $request->company_id!=''){
            $whereCondition[] =['user_plan_history_log.company_id',$request->company_id];
        }
        if(isset($request->user_id) && $request->user_id!=''){
            $whereCondition[] =['user_plan_history_log.user_id',$request->user_id];
        } 
        if(isset($request->plan_name) && $request->plan_name!=''){
            $whereCondition[] =['user_plan_history_log.plan_name',$request->plan_name];
        } 
        if(isset($request->plan_type) && $request->plan_type!=''){
            $whereCondition[] =['user_plan_history_log.purchased_plan_type',$request->plan_type];
        }               
        if(isset($request->start_date) && $request->start_date!=''){
            $whereCondition[] =['user_plan_history_log.created_at',">=",date('Y-m-d 00:00:00',strtotime($request->start_date))];
        }
        if(isset($request->end_date) && $request->end_date!=''){
            $whereCondition[] =['user_plan_history_log.created_at',"<=",date('Y-m-d 23:59:59',strtotime($request->end_date))];
        }
        $page = (isset($request->page) && $request->page!='')?$request->page:1;
        $result = PlanLogs::where($whereCondition)
        ->join('company_settings','company_settings.id','user_plan_history_log.company_id')
        ->select('company_settings.company_name','user_name','plan_name','user_plan_history_log.purchased_plan_type',
        DB::raw('DATE_FORMAT(user_plan_history_log.plan_purchase_at,"%d %b %Y %H:%i") as plan_purchase'),
        DB::raw('DATE_FORMAT(user_plan_history_log.account_activated_at,"%d %b %Y %H:%i") as account_activated'),
        DB::raw('DATE_FORMAT(user_plan_history_log.account_expire_at,"%d %b %Y %H:%i") as account_expire'),
        DB::raw('DATE_FORMAT(user_plan_history_log.created_at,"%d %b %Y %H:%i") as created_date'))
        ->orderBy('user_plan_history_log.id','DESC')
        // ->paginate(30);
       ->paginate(30, ['*'], 'page', $page);

        return $result;
    }

    public static function downloadPlanlogs($request){
        $whereCondition =[            
            ['user_plan_history_log.company_id','>','0']
        ];
        if(isset($request->company_id) && $request->company_id!=''){
            $whereCondition[] =['user_plan_history_log.company_id',$request->company_id];
        }
        if(isset($request->user_id) && $request->user_id!=''){
            $whereCondition[] =['user_plan_history_log.user_id',$request->user_id];
        }               
        if(isset($request->start_date) && $request->start_date!=''){
            $whereCondition[] =['user_plan_history_log.created_at',">=",date('Y-m-d 00:00:00',strtotime($request->start_date))];
        }
        if(isset($request->end_date) && $request->end_date!=''){
            $whereCondition[] =['user_plan_history_log.created_at',"<=",date('Y-m-d 23:59:59',strtotime($request->end_date))];
        }
        if(isset($request->plan_name) && $request->plan_name!=''){
            $whereCondition[] =['user_plan_history_log.plan_name',$request->plan_name];
        } 
        if(isset($request->plan_type) && $request->plan_type!=''){
            $whereCondition[] =['user_plan_history_log.purchased_plan_type',$request->plan_type];
        }  
        $result = PlanLogs::where($whereCondition)
        ->join('company_settings','company_settings.id','user_plan_history_log.company_id')
        ->select('company_settings.company_name','user_name','plan_name','user_plan_history_log.purchased_plan_type',
        DB::raw('DATE_FORMAT(user_plan_history_log.plan_purchase_at,"%d %b %Y %H:%i") as plan_purchase'),
        DB::raw('DATE_FORMAT(user_plan_history_log.account_activated_at,"%d %b %Y %H:%i") as account_activated'),
        DB::raw('DATE_FORMAT(user_plan_history_log.account_expire_at,"%d %b %Y %H:%i") as account_expire'),
        DB::raw('DATE_FORMAT(user_plan_history_log.created_at,"%d %b %Y %H:%i") as created_date'))
        ->orderBy('user_plan_history_log.id','DESC')
        ->get();

        return $result;

    }

    
}
