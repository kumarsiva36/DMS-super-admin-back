<?php

namespace App\Models;

use DateTime;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class Plan extends Model
{
    use HasFactory;
    protected $table = 'plan_price_details';

    public static function getPlanDetails($plan_id){
       $planDetails= Plan::where('id',$plan_id)->first();
       return $planDetails;
    }

    public static function getAllPlans(){
        // $status = '1';
        // $whereCondition=[
        //     ['status','=',$status]
        // ];
        $getPlan = Plan::all();
        $monthly=$yearly=$totalPlans=[];
        foreach ($getPlan as $plan){
            if($plan->type === "Monthly"){
                $monthly[] = $plan;
            }
            else if($plan->type === "Yearly"){
                $yearly[] = $plan;
            }
        }
        $totalPlans['Monthly'] = $monthly;
        $totalPlans['Yearly'] = $yearly;
        return $totalPlans;
    }

    public static function getPlanRemainingDays($whereCondition){
        $planValidity = CompanySettings::where($whereCondition)
        ->select('account_expire_at')
        ->first();
        $today = new DateTime();
        $later = new DateTime($planValidity->account_expire_at);
        $days = $today->diff($later)->format("%r%a");

        return $days;
    }

    /* Add New Plan */
    public static function addNewPlan($request){
        DB::beginTransaction();
        try{
            $newplan=[];
            $newplan['plan_name']=ucfirst($request->plan_name);
            $newplan['type']=$request->plan_type;
            $newplan['no_of_month']=$request->no_of_months;
            $newplan['no_of_days']=$request->no_of_days;
            $newplan['price']=$request->plan_price;
            $newplan['yearly_price']=$request->plan_yearly_price;
            $newplan['special_price']=$request->plan_special_price;
            $newplan['currency']=$request->plan_currency;
            $newplan['no_of_group']=$request->no_of_group;
            $newplan['no_of_user']=$request->no_of_user;
            $newplan['no_of_style']=$request->no_of_style;
            $newplan['no_of_workspace']=$request->no_of_workspace;
            $newplan['max_storage_size']=$request->max_storage_size;
            $newplan['report_range']=$request->report_range;
            $newplan['download_report']=(string)$request->download_report;
            $newplan['notify_email_upcoming_task']=(string)$request->notify_email_upcoming_task;
            $newplan['notify_email_delayed_task']=(string)$request->notify_email_delayed_task;
            $newplan['notify_whatsapp_upcoming_task']=(string)$request->notify_whatsapp_upcoming_task;
            $newplan['notify_whatsapp_delayed_task']=(string)$request->notify_whatsapp_delayed_task;
            $newplan['notify_linemessenger_upcoming_task']=(string)$request->notify_linemessenger_upcoming_task;
            $newplan['notify_linemessenger_delayed_task']=(string)$request->notify_linemessenger_delayed_task;
            $newplan['status']=(string)$request->status;
            $newplan['created_at']=date('Y-m-d H:i:s');
            $newplan['updated_at']=date('Y-m-d H:i:s');
            Plan::insert($newplan);
        }catch(Exception $e){
            DB::rollBack();
            throw new InvalidArgumentException("Unable to Post Data");
        }
        DB::commit();
    }

    /* Edit The Plan - Get The plan Details */
    public static function getThePlanDetails($request){
        $whereCondition[]=["id",'=',$request->plan_id];
        $thePlan = Plan::where($whereCondition)->first();

        return $thePlan;
    }

    /* Update Plan */
    public static function updateThePlan($request){
        DB::beginTransaction();
        try{
            $newplan=Plan::where('id',$request->plan_id)->first();
            $newplan->plan_name=$request->plan_name;
            $newplan->type=$request->plan_type;
            $newplan->no_of_month=$request->no_of_months;
            $newplan->no_of_days=$request->no_of_days;
            $newplan->price=$request->plan_price;
            $newplan->yearly_price=$request->plan_yearly_price;
            $newplan->special_price=$request->plan_special_price;
            $newplan->currency=$request->plan_currency;
            $newplan->no_of_group=$request->no_of_group;
            $newplan->no_of_user=$request->no_of_user;
            $newplan->no_of_style=$request->no_of_style;
            $newplan->no_of_workspace=$request->no_of_workspace;
            $newplan->max_storage_size=$request->max_storage_size;
            $newplan->report_range=$request->report_range;
            $newplan->download_report=(string)$request->download_report;
            $newplan->notify_email_upcoming_task=(string)$request->notify_email_upcoming_task;
            $newplan->notify_email_delayed_task=(string)$request->notify_email_delayed_task;
            $newplan->notify_whatsapp_upcoming_task=(string)$request->notify_whatsapp_upcoming_task;
            $newplan->notify_whatsapp_delayed_task=(string)$request->notify_whatsapp_delayed_task;
            $newplan->notify_linemessenger_upcoming_task=(string)$request->notify_linemessenger_upcoming_task;
            $newplan->notify_linemessenger_delayed_task=(string)$request->notify_linemessenger_delayed_task;
            $newplan->status=(string)$request->status;
            $newplan->save();
        }catch(Exception $e){
            DB::rollBack();
            throw new InvalidArgumentException("Unable to Post Data");
        }
        DB::commit();
    }

    /* Get The Total Plans Count */
    public static function getPlanCount(){
        $plans = Plan::all();

        return count($plans);
    }
}
