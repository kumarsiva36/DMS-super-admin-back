<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class POLogs extends Model
{
    use HasFactory;

    protected $table = "inquiry_po_log";

    static function getPOLogs($request){
        $whereConditions[] =['inquiry_po_log.company_id','!=',"0"];
        if(isset($request->company_id) && $request->company_id!=""){
            $whereConditions[]=['inquiry_po_log.company_id','=',$request->company_id];
        }
        if(isset($request->action) && $request->action!=""){
            $whereConditions[]=['inquiry_po_log.action','=',$request->action];
        }
        if(isset($request->user_id) && $request->user_id!=""){
            $whereConditions[]=['inquiry_po_log.user_id','=',$request->user_id];
        }
        if(isset($request->staff_id) && $request->staff_id!=""){
            $whereConditions[]=['inquiry_po_log.staff_id','=',$request->staff_id];
        }
        if(isset($request->po_id) && $request->po_id!=""){
            $whereConditions[]=['inquiry_po_log.po_id','=',$request->po_id];
        }
        if(isset($request->start_date) && isset($request->end_date) && $request->start_date != "" && $request->end_date == ""){
            $from = date('Y-m-d 00:00:00',strtotime($request->start_date));
            $to = date('Y-m-d 23:59:59');
            $whereConditions[]=['inquiry_po_log.created_at','>=',$from];
            $whereConditions[]=['inquiry_po_log.created_at','<=',$to];
        }
        if(isset($request->start_date) && isset($request->end_date) && $request->start_date != "" && $request->end_date != ""){
            $from = date('Y-m-d 00:00:00',strtotime($request->start_date));
            $to = date('Y-m-d 23:59:59',strtotime($request->end_date));
            $whereConditions[]=['inquiry_po_log.created_at','>=',$from];
            $whereConditions[]=['inquiry_po_log.created_at','<=',$to];
        }

        $res = POLogs::where($whereConditions)
               ->join('inquiry_new_po','inquiry_po_log.po_id','inquiry_new_po.id')
               ->join('company_settings','inquiry_po_log.company_id','company_settings.id')
               ->leftjoin('users','inquiry_po_log.user_id','users.id')
               ->leftjoin('staff','inquiry_po_log.staff_id','staff.id')
               ->select('inquiry_po_log.po_id', DB::raw('DATE_FORMAT(inquiry_po_log.created_at,"%Y-%m-%d") as created_date'), 'action',
               'company_settings.company_name','users.name as user','staff.first_name as staffname','inquiry_new_po.po_number','before_values','after_values')
               ->orderBy('inquiry_po_log.created_at','DESC')
               //->groupBy('inquiry_new_po.parent_id')
               ->paginate(20, ['*'], 'page', $request->page);

        return $res;
    }

    static function download_getPOLogs($request){
        $whereConditions[] =['inquiry_po_log.company_id','!=',"0"];
        if(isset($request->company_id) && $request->company_id!=""){
            $whereConditions[]=['inquiry_po_log.company_id','=',$request->company_id];
        }
        if(isset($request->action) && $request->action!=""){
            $whereConditions[]=['inquiry_po_log.action','=',$request->action];
        }
        if(isset($request->user_id) && $request->user_id!=""){
            $whereConditions[]=['inquiry_po_log.user_id','=',$request->user_id];
        }
        if(isset($request->staff_id) && $request->staff_id!=""){
            $whereConditions[]=['inquiry_po_log.staff_id','=',$request->staff_id];
        }
        if(isset($request->po_id) && $request->po_id!=""){
            $whereConditions[]=['inquiry_po_log.po_id','=',$request->po_id];
        }
        if(isset($request->start_date) && isset($request->end_date) && $request->start_date != "" && $request->end_date == ""){
            $from = date('Y-m-d 00:00:00',strtotime($request->start_date));
            $to = date('Y-m-d 23:59:59');
            $whereConditions[]=['inquiry_po_log.created_at','>=',$from];
            $whereConditions[]=['inquiry_po_log.created_at','<=',$to];
        }
        if(isset($request->start_date) && isset($request->end_date) && $request->start_date != "" && $request->end_date != ""){
            $from = date('Y-m-d 00:00:00',strtotime($request->start_date));
            $to = date('Y-m-d 23:59:59',strtotime($request->end_date));
            $whereConditions[]=['inquiry_po_log.created_at','>=',$from];
            $whereConditions[]=['inquiry_po_log.created_at','<=',$to];
        }

        $res = POLogs::where($whereConditions)
                ->join('inquiry_new_po','inquiry_po_log.po_id','inquiry_new_po.id')
                ->join('company_settings','inquiry_po_log.company_id','company_settings.id')
                ->leftjoin('users','inquiry_po_log.user_id','users.id')
                ->leftjoin('staff','inquiry_po_log.staff_id','staff.id')
                ->select('inquiry_po_log.po_id', DB::raw('DATE_FORMAT(inquiry_po_log.created_at,"%Y-%m-%d") as created_date'), 'action',
                'company_settings.company_name','users.name as user','staff.first_name as staffname','inquiry_new_po.po_number','before_values','after_values')
                ->orderBy('inquiry_po_log.created_at','DESC')
                ->get();

        return $res;
    }
}
