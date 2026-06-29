<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UpdateSkuQuantityLog extends Model
{
    use HasFactory;

    protected $table = "update_sku_quantity_logs";

    public static function update_sku_quantity_log($request){
        $whereCondition =[
            ['update_sku_quantity_logs.workspace_id',">",'0'],
            ['update_sku_quantity_logs.company_id','>','0']
        ];
        if(isset($request->company_id) && $request->company_id!=''){
            $whereCondition[] =['update_sku_quantity_logs.company_id',$request->company_id];
        }
        if(isset($request->workspace_id) && $request->workspace_id!=''){
            $whereCondition[] =['update_sku_quantity_logs.workspace_id',$request->workspace_id];
        }
        if(isset($request->user_id) && $request->user_id!=''){
            $whereCondition[] =['update_sku_quantity_logs.user_id',$request->user_id];
        }
        if(isset($request->staff_id) && $request->staff_id!=''){
            $whereCondition[] =['update_sku_quantity_logs.staff_id',$request->staff_id];
        }
        if(isset($request->order_id) && $request->order_id!=''){
            $whereCondition[] =['update_sku_quantity_logs.order_id',$request->order_id];
        }
        if(isset($request->start_date) && $request->start_date!=''){
            $whereCondition[] =['update_sku_quantity_logs.sku_date',">=",$request->start_date];
        }
        if(isset($request->end_date) && $request->end_date!=''){
            $whereCondition[] =['update_sku_quantity_logs.sku_date',"<=",$request->end_date];
        }
        if(isset($request->type_of_production) && $request->type_of_production!=''){
            $whereCondition[] =['update_sku_quantity_logs.type_of_production',"=",$request->type_of_production];
        }
        if(isset($request->device) && $request->device!=''){
            if($request->device=='Web'){
                $whereCondition[] =['update_sku_quantity_logs.device_details',"like",'%Mozilla%'];
            }else{
                $whereCondition[] =['update_sku_quantity_logs.device_details',"like",'%"'.$request->device.'"%'];
            }
        }
        $page = (isset($request->page) && $request->page!='')?$request->page:1;
        $result = UpdateSkuQuantityLog::where($whereCondition)
        ->join('company_settings','company_settings.id','update_sku_quantity_logs.company_id')
        ->join('workspace','workspace.id','update_sku_quantity_logs.workspace_id')
        ->leftjoin('users','users.id','update_sku_quantity_logs.user_id')
        ->leftjoin('staff','staff.id','update_sku_quantity_logs.staff_id')
        ->join('orders','orders.id','update_sku_quantity_logs.order_id')
        ->join('color','color.id','update_sku_quantity_logs.color_id')
        ->join('size','size.id','update_sku_quantity_logs.size_id')
        ->select('company_settings.company_name','workspace.name','users.name as username','staff.first_name as staffname','orders.style_no',
        'color.name as colorname','size.name as size','type_of_production','sku_date','updated_quantity','update_sku_quantity_logs.device_details',
        DB::raw('DATE_FORMAT(update_sku_quantity_logs.created_at,"%d %b %Y %H:%i") as created_date'))
        ->orderBy('update_sku_quantity_logs.id','DESC')
       // ->paginate(30);
       ->paginate(30, ['*'], 'page', $page);

        return $result;
    }

    public static function update_sku_quantity_log_download($request){
        $whereCondition =[
            ['update_sku_quantity_logs.workspace_id',">",'0'],
            ['update_sku_quantity_logs.company_id','>','0']
        ];
        if(isset($request->company_id) && $request->company_id!=''){
            $whereCondition[] =['update_sku_quantity_logs.company_id',$request->company_id];
        }
        if(isset($request->workspace_id) && $request->workspace_id!=''){
            $whereCondition[] =['update_sku_quantity_logs.workspace_id',$request->workspace_id];
        }
        if(isset($request->user_id) && $request->user_id!=''){
            $whereCondition[] =['update_sku_quantity_logs.user_id',$request->user_id];
        }
        if(isset($request->staff_id) && $request->staff_id!=''){
            $whereCondition[] =['update_sku_quantity_logs.staff_id',$request->staff_id];
        }
        if(isset($request->order_id) && $request->order_id!=''){
            $whereCondition[] =['update_sku_quantity_logs.order_id',$request->order_id];
        }
        if(isset($request->start_date) && $request->start_date!=''){
            $whereCondition[] =['update_sku_quantity_logs.sku_date',">=",$request->start_date];
        }
        if(isset($request->end_date) && $request->end_date!=''){
            $whereCondition[] =['update_sku_quantity_logs.sku_date',"<=",$request->end_date];
        }
        if(isset($request->type_of_production) && $request->type_of_production!=''){
            $whereCondition[] =['update_sku_quantity_logs.type_of_production',"=",$request->type_of_production];
        }
        if(isset($request->device) && $request->device!=''){
            if($request->device=='Web'){
                $whereCondition[] =['update_sku_quantity_logs.device_details',"like",'%Mozilla%'];
            }else{
                $whereCondition[] =['update_sku_quantity_logs.device_details',"like",'%"'.$request->device.'"%'];
            }
        }
        $result = UpdateSkuQuantityLog::where($whereCondition)
        ->join('company_settings','company_settings.id','update_sku_quantity_logs.company_id')
        ->join('workspace','workspace.id','update_sku_quantity_logs.workspace_id')
        ->leftjoin('users','users.id','update_sku_quantity_logs.user_id')
        ->leftjoin('staff','staff.id','update_sku_quantity_logs.staff_id')
        ->join('orders','orders.id','update_sku_quantity_logs.order_id')
        ->join('color','color.id','update_sku_quantity_logs.color_id')
        ->join('size','size.id','update_sku_quantity_logs.size_id')
        ->select('company_settings.company_name','workspace.name','users.name as username','staff.first_name as staffname','orders.style_no',
        'color.name as colorname','size.name as size','type_of_production','sku_date','updated_quantity','update_sku_quantity_logs.device_details',
        DB::raw('DATE_FORMAT(update_sku_quantity_logs.created_at,"%d %b %Y %H:%i") as created_date'))
        ->orderBy('update_sku_quantity_logs.id','DESC')
        ->get();
        return $result;
    }
}


