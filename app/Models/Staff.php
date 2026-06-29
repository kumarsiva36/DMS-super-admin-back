<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;
use App\Models\User;

class Staff extends Authenticatable
{
    use HasApiTokens,HasFactory,Notifiable;

    protected $table = 'staff';
    protected $fillable =[
       'id','first_name','last_name','company_id','workspace_id','user_id','role_id','mobile','email','address1','address2','city','state','zipcode','country','status','language','timezone','created_at','updated_at'
    ];
    protected $hidden=[
        'otp',
        //'remember_token',
     ];
     //
     protected $casts=[
        'email_verified_at' => 'datetime',
     ];

    /* To Get/Filter the Staff with similar letters */
    public static function getStaffSimilar($request){
        $whereConditions[]=['first_name','LIKE',"%".$request->name."%"];
        $whereConditions1[]=['email','LIKE',"%".$request->name."%"];
        $whereConditions2[]=['last_name','LIKE',"%".$request->name."%"];
        $theLikeStaff = Staff::where($whereConditions)
        ->orWhere($whereConditions1)
        ->orWhere($whereConditions2)
        ->select('id','email',DB::raw('CONCAT(first_name," ",last_name) as name'))
        ->orderBy('name','ASC')
        ->groupBy('email')
        ->get();
        
        return $theLikeStaff;
    }
    public static function getUserandStaff($request){
        $whereConditionUsers =[
           // ['workspace_id',"=",$request->workspace],
            ['users.company_id','=',$request->company_id],
            ['users.status','=',"1"],
           // ['company_settings.status','=',"1"]
        ];
        if($request->workspace_id>0){
         $whereConditionStaff=[
             ['staff.workspace_id',"=",$request->workspace_id],
             ['staff.company_id','=',$request->company_id],
             ['staff.status','=',"1"],
            // ['company_settings.status','=',"1"]
         ];
        }else{
        $whereConditionStaff=[
            // ['staff.workspace_id',"=",$request->workspace_id],
             ['staff.company_id','=',$request->company_id],
             ['staff.status','=',"1"],
            // ['company_settings.status','=',"1"]
         ];
      }
        $user = User::where($whereConditionUsers)   
        ->select('users.id','users.name','users.email',DB::raw("'user' as 'viewtype'"),'company_settings.company_name','workspace.name as wname')
        ->leftjoin('company_settings','users.company_id','company_settings.id')
        ->leftjoin('workspace','workspace.company_id','company_settings.id')
        ->get();
        $staff = Staff::where($whereConditionStaff)
         ->select('staff.id',DB::raw('CONCAT(staff.first_name," ",staff.last_name) as name'),'staff.email',DB::raw("'staff' as 'viewtype'"),'company_settings.company_name','workspace.name as wname')
         ->leftjoin('company_settings','staff.company_id','company_settings.id')
         ->leftjoin('workspace','staff.workspace_id','workspace.id')
         ->get();
         if(!empty($staff)){
         $userMerge=collect($user);
         $mergedData=$userMerge->merge($staff);
         }else{
            $mergedData=$user;
         }
        return $mergedData;
       
    }
}
