<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PaymentHistory extends Model
{
    use HasFactory;

    protected $table = 'payment_history';

    public static function getPlanDetailsByUserID($id){
        $history = PaymentHistory::where('user_id',$id)->orderBy('id','DESC')->first();
        return $history;
    }

    /* For Plan Updation/Upgradation */
    public static function updatePlanDetails($request,$header){
        $previousPlanDetails = PaymentHistory::where('user_id',$request->user_id)->orderBy('id','DESC')->first();
        // if($request->plan_id < $previousPlanDetails->plan_id){
        //     throw new InvalidArgumentException('Please choose the same plan or a better plan');
        // }
        $user = User::where('id',$request->user_id)->first();
        $paymentHistory=[];
        $paymentHistory['user_id']=$user->id;
        $paymentHistory['user_name']=$user->username;
        $paymentHistory['user_email']=$user->email;
        $paymentHistory['mobile']=$user->mobile_number;
        $paymentHistory['payment_type']=$request->payment_type;
        $paymentHistory['plan_name']=$request->plan_name;
        $paymentHistory['plan_id']=$request->plan_id;
        $paymentHistory['plan_price']=$request->plan_price;
        $paymentHistory['plan_type']=$request->plan_type;
        $paymentHistory['plan_discount']=$request->plan_discount;
        $paymentHistory['plan_subtotal']=$request->plan_subtotal;
        $paymentHistory['plan_grandtotal']=$request->plan_grandtotal;
        $paymentHistory['payment_currency']=$request->payment_currency;
        // $paymentHistory['reference_id']=$request->reference_id;
        $paymentHistory['reference_id']="Admin";
        $paymentHistory['payment_intent']="";
        $paymentHistory['reason']="Updated By Admin";
        $paymentHistory['payment_status']=$request->payment_status;
        // $paymentHistory['payment_status']="Success";
        $paymentHistory['ipaddress']=$header->ip();
        $paymentHistory['payment_date']=date("Y-m-d H:i:s");
        $paymentHistory['created_at']=date("Y-m-d H:i:s");
        if($request->payment_status === "Success"){
            $user->status = '1';
            $user->save();
        }
        // if($paymentHistory['payment_status'] === "Success"){
        //     $user->status = '1';
        //     $user->save();
        // }
        DB::beginTransaction();
        try{
            PaymentHistory::insert($paymentHistory);
            UserPlanHistory::upgradePlan($request,$previousPlanDetails);
        }
        catch(Exception $e){
            DB::rollBack();
            throw new InvalidArgumentException('Unable to post Data');
        }
        DB::commit();
    }
}
