<?php
namespace App\Common;

use App\Models\CompanySettings;
use App\Models\Staff;
use App\Models\User;
use App\Models\Workspace;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\Common\Encryption;

class CommonApp
{

    public static function generate_Login_OTP(){
        $otp = mt_rand(100001,999999);
        return $otp;
    }

    public static function webDecrypt($str){
        $nonceValue = 'ITOHENDMS';
        $decrypted = Encryption::decrypt($str, $nonceValue);
        $res = json_decode($decrypted, True);
        $res = json_decode(json_encode((object) $res), FALSE);
        return $res;
    }

    public static function webEncrypt($str){
        $nonceValue = 'ITOHENDMS';
        $encrypted = Encryption::encrypt($str, $nonceValue);
        return $encrypted;
    }

    public static function getAllCompanyDetails(){
        $getCompanyDetails = CompanySettings::where('id','>','0')->where('status','=','1')
        ->select('id','company_name')
        ->orderBy('company_name','ASC')
        ->get();
        return $getCompanyDetails;
    }
    public static function getAllUsers($resquest){
        $where = [['id','>','0']];
        if($resquest->company_id && $resquest->company_id!=''){
            $where[] =['company_id','=',$resquest->company_id];
        }
        $getUserDetails = User::where($where)
        ->select('id','name')
        ->orderBy('name','ASC')
        ->get();
        return $getUserDetails;
    }
    public static function getAllStaffs($resquest){
        $where = [['id','>','0']];
        if(isset($resquest->company_id) && $resquest->company_id!=''){
            $where[] =['company_id','=',$resquest->company_id];
        }
        if(isset($resquest->workspace_id) && $resquest->workspace_id!=''){
            $where[] =['workspace_id','=',$resquest->workspace_id];
        }
        $getUserDetails = Staff::where($where)
        ->select('id','first_name','last_name')
        ->orderBy('first_name','ASC')
        ->get();
        return $getUserDetails;
    }

    public static function getAllOrders($resquest){
        $orwhere = $where = [['orders.id','>','0']];
        if(isset    ($resquest->company_id) && $resquest->company_id!=''){
            $orwhere[] = $where[] =['orders.company_id','=',$resquest->company_id];
        }
        if(isset($resquest->workspace_id) && $resquest->workspace_id!=''){
            $orwhere[] = $where[] =['orders.workspace_id','=',$resquest->workspace_id];
        }
        if(isset($resquest->user_id) && $resquest->user_id!=''){
            $where[] =['orders.user_id','=',$resquest->user_id];
            $orwhere[] =['order_contacts.user_id','=',$resquest->user_id];
        }
        if(isset($resquest->staff_id) && $resquest->staff_id!=''){
            $where[] =['orders.staff_id','=',$resquest->staff_id];
            $orwhere[] =['order_contacts.staff_id','=',$resquest->staff_id];
        }
        $Order = Order::where($where)->orWhere($orwhere)
        ->join('order_contacts','orders.id','order_contacts.order_id')
        ->select(DB::RAW('DISTINCT(orders.id) as id'),'orders.style_no')
        ->orderBy('orders.style_no','ASC')
        ->get();
        return $Order;
    }


}
