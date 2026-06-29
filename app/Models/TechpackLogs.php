<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TechpackLogs extends Model
{
    use HasFactory;

    protected $table = "techpack_log";

    static function getTechpackLogs($request){
        $whereConditions[] =['techpack_log.company_id','!=',"0"];
        if(isset($request->company_id) && $request->company_id!=""){
            $whereConditions[]=['techpack_log.company_id','=',$request->company_id];
        }
        if(isset($request->action) && $request->action!=""){
            $whereConditions[]=['techpack_log.action','=',$request->action];
        }
        if(isset($request->user_id) && $request->user_id!=""){
            $whereConditions[]=['techpack_log.user_id','=',$request->user_id];
        }
        if(isset($request->staff_id) && $request->staff_id!=""){
            $whereConditions[]=['techpack_log.staff_id','=',$request->staff_id];
        }
        if(isset($request->start_date) && isset($request->end_date) && $request->start_date != "" && $request->end_date == ""){
            $from = date('Y-m-d 00:00:00',strtotime($request->start_date));
            $to = date('Y-m-d 23:59:59');
            $whereConditions[]=['techpack_log.created_at','>=',$from];
            $whereConditions[]=['techpack_log.created_at','<=',$to];
        }
        if(isset($request->start_date) && isset($request->end_date) && $request->start_date != "" && $request->end_date != ""){
            $from = date('Y-m-d 00:00:00',strtotime($request->start_date));
            $to = date('Y-m-d 23:59:59',strtotime($request->end_date));
            $whereConditions[]=['techpack_log.created_at','>=',$from];
            $whereConditions[]=['techpack_log.created_at','<=',$to];
        }

        $res = TechpackLogs::where($whereConditions)
               ->join('company_settings','techpack_log.company_id','company_settings.id')
               ->join('techpack','techpack_log.teckpack_id','techpack.id')
               ->leftjoin('users','techpack_log.user_id','users.id')
               ->leftjoin('staff','techpack_log.staff_id','staff.id')
               ->select('techpack_log.teckpack_id as techpack_id', DB::raw('DATE_FORMAT(techpack_log.created_at,"%Y-%m-%d") as created_date'), 'action',
               'company_settings.company_name','users.name as user','staff.first_name as staffname','techpack.po_no','techpack.style_no','before_values','after_values')
               ->orderBy('techpack_log.created_at','DESC')
               ->paginate(20, ['*'], 'page', $request->page);

        return $res;
    }

    static function download_techpackLogs($request){
        $whereConditions[] =['techpack_log.company_id','!=',"0"];
        if(isset($request->company_id) && $request->company_id!=""){
            $whereConditions[]=['techpack_log.company_id','=',$request->company_id];
        }
        if(isset($request->action) && $request->action!=""){
            $whereConditions[]=['techpack_log.action','=',$request->action];
        }
        if(isset($request->user_id) && $request->user_id!=""){
            $whereConditions[]=['techpack_log.user_id','=',$request->user_id];
        }
        if(isset($request->staff_id) && $request->staff_id!=""){
            $whereConditions[]=['techpack_log.staff_id','=',$request->staff_id];
        }
        if(isset($request->start_date) && isset($request->end_date) && $request->start_date != "" && $request->end_date == ""){
            $from = date('Y-m-d 00:00:00',strtotime($request->start_date));
            $to = date('Y-m-d 23:59:59');
            $whereConditions[]=['techpack_log.created_at','>=',$from];
            $whereConditions[]=['techpack_log.created_at','<=',$to];
        }
        if(isset($request->start_date) && isset($request->end_date) && $request->start_date != "" && $request->end_date != ""){
            $from = date('Y-m-d 00:00:00',strtotime($request->start_date));
            $to = date('Y-m-d 23:59:59',strtotime($request->end_date));
            $whereConditions[]=['techpack_log.created_at','>=',$from];
            $whereConditions[]=['techpack_log.created_at','<=',$to];
        }

        $res = TechpackLogs::where($whereConditions)
                ->join('company_settings','techpack_log.company_id','company_settings.id')
                ->join('techpack','techpack_log.teckpack_id','techpack.id')
                ->leftjoin('users','techpack_log.user_id','users.id')
                ->leftjoin('staff','techpack_log.staff_id','staff.id')
                ->select('techpack_log.teckpack_id as techpack_id', DB::raw('DATE_FORMAT(techpack_log.created_at,"%Y-%m-%d") as created_date'), 'action',
                'company_settings.company_name','users.name as user','staff.first_name as staffname','techpack.po_no','techpack.style_no')
                ->orderBy('techpack_log.created_at','DESC')
                ->get();

        return $res;
    }
}
