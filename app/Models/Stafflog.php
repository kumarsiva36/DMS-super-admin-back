<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Stafflog extends Model
{
    use HasFactory;

    protected $table = 'staff_logs';

    public static function getstafflogs($request){
        $whereCondition =[
            ['staff_logs.workspace_id',">",'0'],
            ['staff_logs.company_id','>','0']
        ];
        if(isset($request->company_id) && $request->company_id!=''){
            $whereCondition[] =['staff_logs.company_id',$request->company_id];
        }
        if(isset($request->workspace_id) && $request->workspace_id!=''){
            $whereCondition[] =['staff_logs.workspace_id',$request->workspace_id];
        }
        if(isset($request->user_id) && $request->user_id!=''){
            $whereCondition[] =['staff_logs.updated_by_user_id',$request->user_id];
        }
        if(isset($request->staff_id) && $request->staff_id!=''){
            $whereCondition[] =['staff_logs.staff_id',$request->staff_id];
        }        
        if(isset($request->start_date) && $request->start_date!=''){
            $whereCondition[] =['staff_logs.created_at',">=",date('Y-m-d 00:00:00',strtotime($request->start_date))];
        }
        if(isset($request->end_date) && $request->end_date!=''){
            $whereCondition[] =['staff_logs.created_at',"<=",date('Y-m-d 23:59:59',strtotime($request->end_date))];
        }
        if(isset($request->action) && $request->action!=''){
            $whereCondition[] =['staff_logs.action',"=",$request->action];
        }
        $page = (isset($request->page) && $request->page!='')?$request->page:1;
        $result = Stafflog::where($whereCondition)
        ->join('company_settings','company_settings.id','staff_logs.company_id')
        ->join('workspace','workspace.id','staff_logs.workspace_id')
        ->leftjoin('users','users.id','staff_logs.updated_by_user_id')
        ->leftjoin('staff','staff.id','staff_logs.staff_id')
        ->select('company_settings.company_name','workspace.name','users.name as username','staff.first_name as staffname','action','before_values','after_values',
        DB::raw('DATE_FORMAT(staff_logs.created_at,"%d %b %Y %H:%i") as created_date'))
        ->orderBy('staff_logs.id','DESC')
        // ->paginate(30);
       ->paginate(30, ['*'], 'page', $page);

        return $result;
    }

    public static function downloadgetStafflogs($request){
        $whereCondition =[
            ['staff_logs.workspace_id',">",'0'],
            ['staff_logs.company_id','>','0']
        ];
        if(isset($request->company_id) && $request->company_id!=''){
            $whereCondition[] =['staff_logs.company_id',$request->company_id];
        }
        if(isset($request->workspace_id) && $request->workspace_id!=''){
            $whereCondition[] =['staff_logs.workspace_id',$request->workspace_id];
        }
        if(isset($request->user_id) && $request->user_id!=''){
            $whereCondition[] =['staff_logs.updated_by_user_id',$request->user_id];
        }
        if(isset($request->staff_id) && $request->staff_id!=''){
            $whereCondition[] =['staff_logs.staff_id',$request->staff_id];
        }
        if(isset($request->start_date) && $request->start_date!=''){
            $whereCondition[] =['staff_logs.created_at',">=",date('Y-m-d 00:00:00',strtotime($request->start_date))];
        }
        if(isset($request->end_date) && $request->end_date!=''){
            $whereCondition[] =['staff_logs.created_at',"<=",date('Y-m-d 23:59:59',strtotime($request->end_date))];
        }
        if(isset($request->action) && $request->action!=''){
            $whereCondition[] =['staff_logs.action',"=",$request->action];
        }
        
        $result = Stafflog::where($whereCondition)
        ->join('company_settings','company_settings.id','staff_logs.company_id')
        ->join('workspace','workspace.id','staff_logs.workspace_id')
        ->leftjoin('users','users.id','staff_logs.updated_by_user_id')
        ->leftjoin('staff','staff.id','staff_logs.staff_id')
        ->select('company_settings.company_name','workspace.name','users.name as username','staff.first_name as staffname','action','before_values','after_values',
        DB::raw('DATE_FORMAT(staff_logs.created_at,"%d %b %Y %H:%i") as created_date'))
        ->orderBy('staff_logs.id','DESC')
        ->get();

        return $result;
    }
}
