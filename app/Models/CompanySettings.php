<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CompanySettings extends Model
{
    use HasFactory;

    protected $table = 'company_settings';
    protected $fillable = ['aws_s3_path','logo','company_name','user_id','contact_person','contact_number','address1','address2','city',
    'state','zipcode','country_id','account_no','ifsc_code','gst_number','pan_number','language','currency','timezone','purchased_plan_type',
    'purchased_plan_id','purchased_plan_name','purchased_plan_price','purchased_plan_price_currency','plan_purchase_at','status',
    'account_activated_at','account_expire_at','no_of_group','no_of_user','no_of_style','no_of_workspace',
    'max_storage_size','report_range','download_report','notify_email_upcoming_task','notify_email_delayed_task',
    'notify_whatsapp_upcoming_task','notify_whatsapp_delayed_task','notify_linemessenger_upcoming_task',
    'notify_linemessenger_delayed_task','created_at','updated_at'];

    /* To List that are about to expire  */
    public static function getAllTheCompaniesAboutToExpire($request){
        $whereConditions[]=['company_settings.status','=',"1"];

        if(isset($request->company_id)&&$request->company_id != ""){
            $whereConditions[]=['company_settings.id','=',$request->company_id];
        }
        if(isset($request->no_of_days)&&$request->no_of_days!=""){
            if($request->no_of_days === "Expired"){
                $companies['companies'] = CompanySettings::where($whereConditions)
                ->join('staff','company_settings.id','staff.company_id')
                ->join('orders','company_settings.id','orders.company_id')
                ->select('company_settings.id','company_name','company_settings.user_id','purchased_plan_id','purchased_plan_name','purchased_plan_type',
                'purchased_plan_price','plan_purchase_at','company_settings.status','account_activated_at','account_expire_at','logo',
                'contact_person','contact_number','company_settings.address1','company_settings.address2','company_settings.city','company_settings.state','company_settings.zipcode',
                'no_of_group','no_of_user','no_of_style','no_of_workspace','max_storage_size','storage_used',
                DB::raw('COUNT(DISTINCT(staff.id)) as staffsCount'), DB::raw('COUNT(DISTINCT(orders.id)) as ordersCount'),
                DB::raw('DATEDIFF(account_expire_at, NOW()) as daysUntilExpiry'))
                ->having('daysUntilExpiry','<','0')
                ->groupBy('company_settings.id')
                ->orderBy('daysUntilExpiry','ASC')
                ->paginate(20, ['*'], 'page', $request->page);
            }else{
                $companies['companies'] = CompanySettings::where($whereConditions)
                ->join('staff','company_settings.id','staff.company_id')
                ->join('orders','company_settings.id','orders.company_id')
                ->select('company_settings.id','company_name','company_settings.user_id','purchased_plan_id','purchased_plan_name','purchased_plan_type',
                'purchased_plan_price','plan_purchase_at','company_settings.status','account_activated_at','account_expire_at','logo',
                'contact_person','contact_number','company_settings.address1','company_settings.address2','company_settings.city','company_settings.state','company_settings.zipcode',
                'no_of_group','no_of_user','no_of_style','no_of_workspace','max_storage_size','storage_used',
                DB::raw('COUNT(DISTINCT(staff.id)) as staffsCount'), DB::raw('COUNT(DISTINCT(orders.id)) as ordersCount'),
                DB::raw('DATEDIFF(account_expire_at, NOW()) as daysUntilExpiry'))
                ->having('daysUntilExpiry','=',$request->no_of_days)
                ->groupBy('company_settings.id')
                ->orderBy('daysUntilExpiry','ASC')
                ->paginate(20, ['*'], 'page', $request->page);
            }
        }else{
            $companies['companies'] = CompanySettings::where($whereConditions)
            ->join('staff','company_settings.id','=','staff.company_id')
            ->join('orders','company_settings.id','=','orders.company_id')
            ->select('company_settings.id','company_name','company_settings.user_id','purchased_plan_id','purchased_plan_name','purchased_plan_type',
            'purchased_plan_price','plan_purchase_at','company_settings.status','account_activated_at','account_expire_at','logo',
            'contact_person','contact_number','company_settings.address1','company_settings.address2','company_settings.city','company_settings.state','company_settings.zipcode',
            'no_of_group','no_of_user','no_of_style','no_of_workspace','max_storage_size','storage_used',
            DB::raw('COUNT(DISTINCT(staff.id)) as staffsCount'), DB::raw('COUNT(DISTINCT(orders.id)) as ordersCount'),
            DB::raw('DATEDIFF(account_expire_at, NOW()) as daysUntilExpiry'))
            ->having('daysUntilExpiry','<','7')
            ->groupBy('company_settings.id')
            ->orderBy('daysUntilExpiry','ASC')
            ->paginate(20, ['*'], 'page', $request->page);
        }
        $awsURl = config('filesystems.disks.s3.url');
        $companies['awsUrl'] = $awsURl;
        return $companies;
    }

    /* To List the companies that are about to expire in the filter section*/
    public static function getCompaniesForPlan(){
        $companyNames = CompanySettings::where('status',"1")
        ->select('id','company_name','account_expire_at',DB::raw('DATEDIFF(account_expire_at, NOW()) as daysUntilExpiry'))
        ->having('daysUntilExpiry','<','7')
        ->orderBy('company_name','ASC')
        ->get();

        return $companyNames;
    }

    /* To Get/Filter the company with similar letters */
    public static function getCompanySimilar($request){
        $whereConditions[]=['company_name','LIKE',"%".$request->name."%"];
        $theLikeCompanies = CompanySettings::where($whereConditions)
        ->select('company_name','id','contact_person')
        ->orderBy('company_name','ASC')
        ->get();

        return $theLikeCompanies;
    }

    /* Get The Counts of User and Workspaces */
    public static function getTheCountsOfUsersAndWorkspaces(){
        $companies = CompanySettings::join('staff','staff.company_id','company_settings.id')
        ->select('company_settings.id','company_settings.company_name as companyName',DB::raw('COUNT(staff.id) as staffsCount'),
        DB::raw('(SELECT COUNT(staff.id) FROM staff WHERE staff.company_id = company_settings.id AND staff.status = "1"
        GROUP BY staff.company_id) as activeStaffsCount'),
        DB::raw('(SELECT COUNT(staff.id) FROM staff WHERE staff.company_id = company_settings.id AND staff.status = "2"
        GROUP BY staff.company_id) as inactiveStaffsCount'),
        DB::raw('(SELECT COUNT(workspace.id) FROM workspace WHERE workspace.company_id = company_settings.id
        GROUP BY workspace.company_id) as workspaceCount'))
        ->groupby('staff.company_id')
        ->orderby('staffsCount','DESC')
        ->limit(5)
        ->get();

        return $companies;
    }

    /* To Get the plan S3 usage and S3 storage Details */
    public static function getPlanStorageDetails(){
        $companyAndPlan = CompanySettings::select('company_name as name','purchased_plan_name as planName',
        'purchased_plan_type as planType','storage_used as storageUsed','max_storage_size as storageSize')
        ->orderBy('storageUsed','DESC')
        ->limit(5)
        ->get();

        return $companyAndPlan;
    }

    /* Get Custom Plan and Details */
    public static function getCustomPlanCompanyDetails(){
        $customPlanCompanies = CompanySettings::where('purchased_plan_id','>','8')
        ->leftjoin('staff','staff.company_id','company_settings.id')
        ->select('company_settings.id','company_settings.company_name as companyName',DB::raw('COUNT(staff.id) as usedStaffsCount'),
        'no_of_user as totalStaffsCount',DB::raw('DATE_FORMAT(account_expire_at,"%Y-%m-%d") as effectiveDate'),
        DB::raw('(SELECT COUNT(workspace.id) FROM workspace WHERE workspace.company_id = company_settings.id
        GROUP BY workspace.company_id) as workspaceCount'),'purchased_plan_name as planName')
        ->groupBy('staff.company_id')
        ->orderBy('usedStaffsCount','DESC')
        ->limit(5)
        ->get();

        return $customPlanCompanies;
    }

    /* Get Plan Expiry Details */
    public static function getPlanExpiryDetails(){
        $companyPlanDetails = CompanySettings::leftjoin('staff','staff.company_id','company_settings.id')
        ->select('company_name as companyName','purchased_plan_name as planName',/* DB::raw('COUNT(staff.id) as usedStaffsCount') */
        /* 'no_of_user as totalStaffsCount', */DB::raw('DATE_FORMAT(account_expire_at,"%Y-%m-%d") as effectiveDate'),
        DB::raw("DATEDIFF(account_expire_at,NOW()) as daysToExpire"),
        )
        ->having('daysToExpire','<',7)
        ->having('daysToExpire','>=',0)
        // ->groupBy('staff.company_id')
        ->orderBy('daysToExpire','DESC')
        ->limit(5)
        ->get();

        return $companyPlanDetails;
    }

    /* To Get the company settings */
    public static function getCompanyStorage($request){
        $whereConditions[]=['id','!=',"0"];
        if(isset($request->companyId) && $request->companyId!=0){
            $whereConditions[]=['id','=',$request->companyId];
        }
        $result = CompanySettings::where($whereConditions)
        ->select('company_name','id','contact_person','contact_number','no_of_group','no_of_user','no_of_style','no_of_workspace','max_storage_size','storage_used',DB::raw('DATE_FORMAT(account_expire_at,"%Y-%m-%d") as account_expire_at'))
        ->orderBy('company_name','ASC')
        ->get();

        return $result;
    }
}
