<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Orderlog extends Model
{
    use HasFactory;

    protected $table = 'order_logs';

    public static function getOrderlogs($request){
        $whereCondition =[
            ['order_logs.workspace_id',">",'0'],
            ['order_logs.company_id','>','0']
        ];
        if(isset($request->company_id) && $request->company_id!=''){
            $whereCondition[] =['order_logs.company_id',$request->company_id];
        }
        if(isset($request->workspace_id) && $request->workspace_id!=''){
            $whereCondition[] =['order_logs.workspace_id',$request->workspace_id];
        }
        if(isset($request->user_id) && $request->user_id!=''){
            $whereCondition[] =['order_logs.user_id',$request->user_id];
        }
        if(isset($request->staff_id) && $request->staff_id!=''){
            $whereCondition[] =['order_logs.staff_id',$request->staff_id];
        }
        if(isset($request->order_id) && $request->order_id!=''){
            $whereCondition[] =['order_logs.order_id',$request->order_id];
        }
        if(isset($request->start_date) && $request->start_date!=''){
            $whereCondition[] =['order_logs.created_at',">=",date('Y-m-d 00:00:00',strtotime($request->start_date))];
        }
        if(isset($request->end_date) && $request->end_date!=''){
            $whereCondition[] =['order_logs.created_at',"<=",date('Y-m-d 23:59:59',strtotime($request->end_date))];
        }
        if(isset($request->action) && $request->action!=''){
            $whereCondition[] =['order_logs.action',"=",$request->action];
        }
        $page = (isset($request->page) && $request->page!='')?$request->page:1;
        $result = Orderlog::where($whereCondition)
        ->join('company_settings','company_settings.id','order_logs.company_id')
        ->join('workspace','workspace.id','order_logs.workspace_id')
        ->leftjoin('users','users.id','order_logs.user_id')
        ->leftjoin('staff','staff.id','order_logs.staff_id')
        ->join('orders','orders.id','order_logs.order_id')
        ->select('company_settings.company_name','workspace.name','users.name as username','staff.first_name as staffname','orders.style_no','orders.order_no','action','before_values','after_values',
        DB::raw('DATE_FORMAT(order_logs.created_at,"%d %b %Y %H:%i") as created_date'))
        ->orderBy('order_logs.id','DESC')
        //->paginate(30);
        ->paginate(30, ['*'], 'page', $page);

        return $result;
    }

    public static function downloadgetOrderlogs($request){
        $whereCondition =[
            ['order_logs.workspace_id',">",'0'],
            ['order_logs.company_id','>','0']
        ];
        if(isset($request->company_id) && $request->company_id!=''){
            $whereCondition[] =['order_logs.company_id',$request->company_id];
        }
        if(isset($request->workspace_id) && $request->workspace_id!=''){
            $whereCondition[] =['order_logs.workspace_id',$request->workspace_id];
        }
        if(isset($request->user_id) && $request->user_id!=''){
            $whereCondition[] =['order_logs.user_id',$request->user_id];
        }
        if(isset($request->staff_id) && $request->staff_id!=''){
            $whereCondition[] =['order_logs.staff_id',$request->staff_id];
        }
        if(isset($request->order_id) && $request->order_id!=''){
            $whereCondition[] =['order_logs.order_id',$request->order_id];
        }
        if(isset($request->start_date) && $request->start_date!=''){
            $whereCondition[] =['order_logs.created_at',">=",date('Y-m-d 00:00:00',strtotime($request->start_date))];
        }
        if(isset($request->end_date) && $request->end_date!=''){
            $whereCondition[] =['order_logs.created_at',"<=",date('Y-m-d 23:59:59',strtotime($request->end_date))];
        }
        if(isset($request->action) && $request->action!=''){
            $whereCondition[] =['order_logs.action',"=",$request->action];
        }
        $result = Orderlog::where($whereCondition)
        ->join('company_settings','company_settings.id','order_logs.company_id')
        ->join('workspace','workspace.id','order_logs.workspace_id')
        ->leftjoin('users','users.id','order_logs.user_id')
        ->leftjoin('staff','staff.id','order_logs.staff_id')
        ->join('orders','orders.id','order_logs.order_id')
        ->select('company_settings.company_name','workspace.name','users.name as username','staff.first_name as staffname','orders.style_no','orders.order_no','action','before_values','after_values',
        DB::raw('DATE_FORMAT(order_logs.created_at,"%d %b %Y %H:%i") as created_date'))
        ->orderBy('order_logs.id','DESC')
        ->get();

        return $result;
    }
}
