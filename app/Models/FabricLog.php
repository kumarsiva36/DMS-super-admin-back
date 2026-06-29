<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class FabricLog extends Model
{
    use HasFactory;

    protected $table = 'fabric_inquiry_log';

    /* To Get all inquiry logs */
    public static function inquiry_logs($request){
        $whereConditions[] =['fabric_inquiry_log.company_id','!=',"0"];
        if(isset($request->company_id) && $request->company_id!=""){
            $whereConditions[]=['fabric_inquiry_log.company_id','=',$request->company_id];
        }
        if(isset($request->action) && $request->action!=""){
            $whereConditions[]=['fabric_inquiry_log.action','=',$request->action];
        }
        if(isset($request->user_id) && $request->user_id!=""){
            $whereConditions[]=['fabric_inquiry_log.user_id','=',$request->user_id];
        }
        if(isset($request->staff_id) && $request->staff_id!=""){
            $whereConditions[]=['fabric_inquiry_log.staff_id','=',$request->staff_id];
        }
        if(isset($request->inquiry_id) && $request->inquiry_id!=""){
            $whereConditions[]=['fabric_inquiry_log.inquiry_id','=',$request->inquiry_id];
        }
        if(isset($request->start_date) && isset($request->end_date) && $request->start_date != "" && $request->end_date == ""){
            $from = date('Y-m-d 00:00:00',strtotime($request->start_date));
            $to = date('Y-m-d 23:59:59');
            $whereConditions[]=['fabric_inquiry_log.created_at','>=',$from];
            $whereConditions[]=['fabric_inquiry_log.created_at','<=',$to];
        }
        if(isset($request->start_date) && isset($request->end_date) && $request->start_date != "" && $request->end_date != ""){
            $from = date('Y-m-d 00:00:00',strtotime($request->start_date));
            $to = date('Y-m-d 23:59:59',strtotime($request->end_date));
            $whereConditions[]=['fabric_inquiry_log.created_at','>=',$from];
            $whereConditions[]=['fabric_inquiry_log.created_at','<=',$to];
        }

        $res = FabricLog::where($whereConditions)
               ->join('company_settings','fabric_inquiry_log.company_id','company_settings.id')
               ->leftjoin('users','fabric_inquiry_log.user_id','users.id')
               ->leftjoin('staff','fabric_inquiry_log.staff_id','staff.id')
               ->select('fabric_inquiry_log.inquiry_id', DB::raw('DATE_FORMAT(fabric_inquiry_log.created_at,"%Y-%m-%d") as created_date'), 'action','before_values','after_values',
               'company_settings.company_name','users.name as user','staff.first_name as staffname')
               ->orderBy('fabric_inquiry_log.created_at','DESC')
               ->paginate(20, ['*'], 'page', $request->page);

        return $res;
    }

    public static function getInquiryIds($request){
        $res = FabricLog::select('inquiry_id')->distinct()->get();
        return $res;
    }

    /* To Download all inquiry logs */
    public static function download_inquiry_logs($request){
        $whereConditions[] =['fabric_inquiry_log.company_id','!=',"0"];
        if(isset($request->company_id) && $request->company_id!=""){
            $whereConditions[]=['fabric_inquiry_log.company_id','=',$request->company_id];
        }
        if(isset($request->action) && $request->action!=""){
            $whereConditions[]=['fabric_inquiry_log.action','=',$request->action];
        }
        if(isset($request->user_id) && $request->user_id!=""){
            $whereConditions[]=['fabric_inquiry_log.user_id','=',$request->user_id];
        }
        if(isset($request->staff_id) && $request->staff_id!=""){
            $whereConditions[]=['fabric_inquiry_log.staff_id','=',$request->staff_id];
        }
        if(isset($request->inquiry_id) && $request->inquiry_id!=""){
            $whereConditions[]=['fabric_inquiry_log.inquiry_id','=',$request->inquiry_id];
        }
        if(isset($request->start_date) && isset($request->end_date) && $request->start_date != "" && $request->end_date == ""){
            $from = date('Y-m-d 00:00:00',strtotime($request->start_date));
            $to = date('Y-m-d 23:59:59');
            $whereConditions[]=['fabric_inquiry_log.created_at','>=',$from];
            $whereConditions[]=['fabric_inquiry_log.created_at','<=',$to];
        }
        if(isset($request->start_date) && isset($request->end_date) && $request->start_date != "" && $request->end_date != ""){
            $from = date('Y-m-d 00:00:00',strtotime($request->start_date));
            $to = date('Y-m-d 23:59:59',strtotime($request->end_date));
            $whereConditions[]=['fabric_inquiry_log.created_at','>=',$from];
            $whereConditions[]=['fabric_inquiry_log.created_at','<=',$to];
        }

        $res = FabricLog::where($whereConditions)
               ->join('company_settings','fabric_inquiry_log.company_id','company_settings.id')
               ->leftjoin('users','fabric_inquiry_log.user_id','users.id')
               ->leftjoin('staff','fabric_inquiry_log.staff_id','staff.id')
               ->select('fabric_inquiry_log.inquiry_id', DB::raw('DATE_FORMAT(fabric_inquiry_log.created_at,"%Y-%m-%d") as created_date'), 'action','before_values','after_values',
               'company_settings.company_name','users.name as user','staff.first_name as staffname')
               ->orderBy('fabric_inquiry_log.created_at','DESC')
               ->get();

        return $res;
    }


}
