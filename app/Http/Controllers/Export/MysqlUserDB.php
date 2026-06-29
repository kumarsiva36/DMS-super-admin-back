<?php

namespace App\Http\Controllers\Export;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Common\CommonApp;
use Exception;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ImportXL;
use DateTime;

class MysqlUserDB extends Controller
{


    public function exportTables(Request $request)
    {
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            "workspace_id" => 'required',
            "company_id" => 'required',
        ]);
        if($validated->fails()){
        $res = json_encode((["status_code"=>401,"status" =>"failed","validation_error"=>$validated->errors()]));
        return CommonApp::webEncrypt($res);
        }
        // if (! Storage::exists('backup')) {
        //     Storage::makeDirectory('backup');
        // }
        $workSpaceID=$request->workspace_id;
        $companyID=$request->company_id;
        $userID=1;
        $staffID=1;
        $data = [];
        $tableNames = [
            'users'=>["table"=>'users',"select"=>["id","name"],"where"=> [ ['company_id', '=',$companyID]],"orwhere"=>[]],
            'company_settings'=>["table"=>'company_settings',"select"=>["id","name"],"where"=> [['id', '=',$companyID]],"orwhere"=>[]],
            'workspace'=>["table"=>'workspace',"select"=>["id","name"],"where"=> [ ['id','=',$workSpaceID], ['company_id', '=',$companyID]],"orwhere"=>[]],
            'color'=>["table"=>'color',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID],['status', '=',1]],"orwhere"=>[['is_default','=',"0"]]],
            'size'=> ["table"=>'size',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID],['status', '=',1]],"orwhere"=>[['is_default','=',"0"]]],
            'staff'=> ["table"=>'staff',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]],"orwhere"=>[]],
            'roles'=> ["table"=>'roles',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]],"orwhere"=>[['is_default','=',"0"]]],
            'order_article_name'=> ["table"=>'order_article_name',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]],"orwhere"=>[['is_default','=',"0"]]],
            'order_buyer'=> ["table"=>'order_buyer',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]],"orwhere"=>[]],
            'order_category'=> ["table"=>'order_category',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]],"orwhere"=>[['is_default','=',"0"]]],
            'order_factory'=> ["table"=>'order_factory',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]],"orwhere"=>[]],
            'order_pcu'=> ["table"=>'order_pcu',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]],"orwhere"=>[]],
            'fabric_type'=> ["table"=>'fabric_type',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID],['inquiry_reference_id', '=',0]],"orwhere"=>[]],
            'orders'=> ["table"=>'orders',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]],"orwhere"=>[]],
            'order_contacts'=> ["table"=>'order_contacts',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]],"orwhere"=>[]],
            'order_sku'=> ["table"=>'order_sku',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]],"orwhere"=>[]],
            'order_task_data'=> ["table"=>'order_task_data',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]],"orwhere"=>[]],
            'order_task_template'=> ["table"=>'order_task_template',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]],"orwhere"=>[['is_default','=',"0"]]],
            'order_add_spec'=> ["table"=>'order_add_spec',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]],"orwhere"=>[]],
            'order_production_data'=> ["table"=>'order_production_data',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]],"orwhere"=>[]],
            'update_order_action'=> ["table"=>'update_order_action',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]],"orwhere"=>[]],
            'update_sku_quantities'=> ["table"=>'update_sku_quantities',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]],"orwhere"=>[]],
            'order_bom'=> ["table"=>'order_bom',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]],"orwhere"=>[]],
            'inquiry_label_vendor_list'=> ["table"=>'inquiry_label_vendor_list',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]],"orwhere"=>[]],
            'multiple_delivery_dates'=> ["table"=>'multiple_delivery_dates',"select"=>["id","order_id"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]],"orwhere"=>[]],
            'partial_deliveries'=> ["table"=>'partial_deliveries',"select"=>["id","order_id"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]],"orwhere"=>[]],
            'order_units'=> ["table"=>'order_units',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]],"orwhere"=>[['is_default','=',"0"]]],

            'country'=> ["table"=>'country',"select"=>["id","name"],"where"=> [ ['id','!=',0]]],
            'currencies'=> ["table"=>'currencies',"select"=>["id","name"],"where"=> [ ['id','!=',0]]],
            'dashboard_notification'=> ["table"=>'dashboard_notification',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]]],
            'dashboard_settings'=> ["table"=>'dashboard_settings',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]]],
            'email_notification_settings'=> ["table"=>'email_notification_settings',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]]],
            'email_schedule_notification'=> ["table"=>'email_schedule_notification',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]]],
            'email_schedule_report_orderid'=> ["table"=>'email_schedule_report_orderid',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]]],
            'email_schedule_task'=> ["table"=>'email_schedule_task',"select"=>["id","name"],"where"=> [ ['id','!=',0]]],
            'fabric_composition'=> ["table"=>'fabric_composition',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]],"orwhere"=>[['is_default','=',"0"]]],
            'holiday_settings'=> ["table"=>'fabric_type',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]]],
            'income_terms'=> ["table"=>'income_terms',"select"=>["id","name"],"where"=> [ ['id','!=',0]]],
            'inquiry_master'=> ["table"=>'inquiry_master',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]],"orwhere"=>[['is_default','=',"0"]]],
            'inquiry_new_po'=> ["table"=>'inquiry_new_po',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]]],
            'inquiry_new_po_translate'=> ["table"=>'inquiry_new_po_translate',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]]],
            'inquiry_po_comments'=> ["table"=>'inquiry_po_comments',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]]],
            'inquiry_po_log'=> ["table"=>'inquiry_po_log',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]]],
            'inquiry_po_media'=> ["table"=>'inquiry_po_media',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]]],
            'language'=> ["table"=>'language',"select"=>["id","name"],"where"=> [ ['id','!=',0]]],
            'notification_settings'=> ["table"=>'notification_settings',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]]],
            'order_bom_approval_log'=> ["table"=>'order_bom_approval_log',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]]],
            'permissions'=> ["table"=>'permissions',"select"=>["id","name"],"where"=> [ ['id','!=',0],['status','=',1]]],
            'plan_price_details'=> ["table"=>'plan_price_details',"select"=>["id","name"],"where"=> [ ['id','!=',0]]],
            'reschedule_tasks'=> ["table"=>'reschedule_tasks',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]]],
            'role_permission_changes'=> ["table"=>'role_permission_changes',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]]],
            'role_privileges'=> ["table"=>'role_privileges',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]]],
            'sam_master'=> ["table"=>'sam_master',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]]],
            'sam_report_settings'=> ["table"=>'sam_report_settings',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]]],
            'techpack'=> ["table"=>'techpack',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]]],
            'techpack_comments'=> ["table"=>'techpack_comments',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]]],
            'techpack_details'=> ["table"=>'techpack_details',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]]],
            'techpack_edit_details'=> ["table"=>'techpack_edit_details',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]]],
            'techpack_images'=> ["table"=>'techpack_images',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]]],
            'techpack_log'=> ["table"=>'techpack_log',"select"=>["id","name"],"where"=> [ ['workspace_id','=',$workSpaceID], ['company_id', '=',$companyID]]],
        ];

        foreach ($tableNames as $tableName) {
            $select=implode(",",$tableName['select']);

            $tableNamef=$tableName['table'];

        try{
            if($tableNamef=='order_bom'){
                $ordersData = DB::table('orders')->where($tableName['where'])->pluck('id');
                if(!empty($ordersData)){
                $tableData = DB::table($tableNamef)->whereIn('order_id',$ordersData)->get();
                }else{
                    $tableData ='';
                }
            }
            elseif(empty($tableName['orwhere'])){
                $tableData = DB::table($tableNamef)->where($tableName['where'])->get();
            }else{
                $tableData = DB::table($tableNamef)->where($tableName['where'])->orwhere($tableName['orwhere'])->get();
            }
            if($tableData!=''){
            $data[$tableNamef] = $tableData->toArray();
            }

        }catch(Exception $e){
            //$res = json_encode(["status_code"=>401,"error"=>$e->getMessage()]);
        }

        }

        $data = json_encode($data);
        $filename= date("YmdHis").".json";
        Storage::disk('local')->put($filename, $data);
        $filePath = storage_path('app/'.$filename);
        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function importTables(Request $request)
    {

        // $request= CommonApp::webDecrypt($request->getContent());

        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'file'=>'required|mimes:json',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }

        $workSpaceID=$request->workspace_id;
        $companyID=$request->company_id;
        // $data = Storage::disk('local')->get('database_backup.json');
        // $tablesData = json_decode($data, true);
        if($request->hasfile('file')){
            $logArr['company_id'] = $companyID;
            $logArr['workspace_id'] = $workSpaceID;

            $importfile = file_get_contents($request->file('file'));
            $tablesData = json_decode($importfile, true);

            /*Current company user details */
            $user= DB::table("users")->where('company_id','=',$companyID)->first();

            /*color table statrs*/
            $color_array=[];$color_i=0;
            foreach ($tablesData['color'] as $tableName => $tableData) {
                //dd($tableData);
                $colorId=$tableData['id'];
                $colorName=$tableData['name'];
                $company_id=$tableData['company_id'];
                $workspace_id=$tableData['workspace_id'];
                $is_default=$tableData['is_default'];
                $status=$tableData['status'];

                if($is_default==0){
                    $id= DB::table("color")->where('name','=',$colorName)->pluck('id')->first();
                    if($id!='' && $id!=NULL){
                        $color_array[$color_i]['old_id']=$colorId;
                        $color_array[$color_i]['new_id']=$id;
                    }else{
                        $new=[];
                        $new['name']=ucfirst($colorName);
                        $new['company_id']=0;
                        $new['workspace_id']=0;
                        $new['user_id']=0;
                        $new['staff_id']=0;
                        $new['is_default']=0;
                        $new['status']=1;
                        $new['created_by']=0;
                        $new['created_at']=date('Y-m-d H:i:s');
                        $new['updated_at']=date('Y-m-d H:i:s');
                        DB::table("color")->insert($new);
                        $id = DB::getPdo()->lastInsertId();
                        $color_array[$color_i]['old_id']=$colorId;
                        $color_array[$color_i]['new_id']=$id;
                    }
                }else{
                    $whereCondition=[
                        ['company_id','=',$companyID],
                        ['workspace_id','=',$workSpaceID],
                        ['name','=',$colorName],
                        ['status','=',$status]
                    ];
                    $id= DB::table("color")->where($whereCondition)->pluck('id')->first();
                    if($id!='' && $id!=NULL){
                        $color_array[$color_i]['old_id']=$colorId;
                        $color_array[$color_i]['new_id']=$id;
                    }else{
                        $new=[];
                        $new['name']=ucfirst($colorName);
                        $new['company_id']=$companyID;
                        $new['workspace_id']=$workSpaceID;
                        $new['user_id']=$user->id;
                        $new['staff_id']=0;
                        $new['is_default']='1';
                        $new['status']=1;
                        $new['created_by']=0;
                        $new['created_at']=date('Y-m-d H:i:s');
                        $new['updated_at']=date('Y-m-d H:i:s');
                        DB::table("color")->insert($new);
                        $id = DB::getPdo()->lastInsertId();
                        $color_array[$color_i]['old_id']=$colorId;
                        $color_array[$color_i]['new_id']=(int)$id;
                    }
                }

                $color_i++;
            }
            /*color table end*/

            /*Size table statrs*/
            $size_array=[];$size_i=0;
            foreach ($tablesData['size'] as $tableName => $tableData) {
                //dd($tableData);
                $sizeId=$tableData['id'];
                $sizeName=$tableData['name'];
                $company_id=$tableData['company_id'];
                $workspace_id=$tableData['workspace_id'];
                $is_default=$tableData['is_default'];
                $status=$tableData['status'];
                $category=$tableData['category'];

                if($is_default==0){
                    $id= DB::table("size")->where('name','=',$sizeName)->where('category','=',$category)->pluck('id')->first();
                    if($id!='' && $id!=NULL){
                        $size_array[$size_i]['old_id']=$sizeId;
                        $size_array[$size_i]['new_id']=$id;
                    }else{
                        $new=[];
                        $new['name']=ucfirst($sizeName);
                        $new['company_id']=0;
                        $new['workspace_id']=0;
                        $new['user_id']=0;
                        $new['staff_id']=0;
                        $new['is_default']=0;
                        $new['status']=1;
                        $new['category']=ucfirst($category);
                        $new['created_by']=0;
                        $new['created_at']=date('Y-m-d H:i:s');
                        $new['updated_at']=date('Y-m-d H:i:s');
                        DB::table("size")->insert($new);
                        $id = DB::getPdo()->lastInsertId();
                        $size_array[$size_i]['old_id']=$sizeId;
                        $size_array[$size_i]['new_id']=$id;
                    }
                }else{
                    $whereCondition=[
                        ['company_id','=',$companyID],
                        ['workspace_id','=',$workSpaceID],
                        ['name','=',$sizeName],
                        ['status','=',$status],
                        ['category','=',$category]
                    ];
                    $id= DB::table("size")->where($whereCondition)->pluck('id')->first();
                    if($id!='' && $id!=NULL){
                        $size_array[$size_i]['old_id']=$sizeId;
                        $size_array[$size_i]['new_id']=$id;
                    }else{
                        $new=[];
                        $new['name']=ucfirst($sizeName);
                        $new['company_id']=$companyID;
                        $new['workspace_id']=$workSpaceID;
                        $new['user_id']=$user->id;
                        $new['staff_id']=0;
                        $new['is_default']='1';
                        $new['status']=1;
                        $new['category']=ucfirst($category);
                        $new['created_by']=0;
                        $new['created_at']=date('Y-m-d H:i:s');
                        $new['updated_at']=date('Y-m-d H:i:s');
                        DB::table("size")->insert($new);
                        $id = DB::getPdo()->lastInsertId();
                        $size_array[$size_i]['old_id']=$sizeId;
                        $size_array[$size_i]['new_id']=(int)$id;
                    }
                }

                $size_i++;
            }
            /*Size table end*/

            /*Role table statrs*/
            $role_array=[];$role_i=0;
            foreach ($tablesData['roles'] as $tableName => $tableData) {
                //dd($tableData);
                $roleId=$tableData['id'];
                $roleName=$tableData['name'];
                $company_id=$tableData['company_id'];
                $workspace_id=$tableData['workspace_id'];
                $is_default=$tableData['is_default'];
                $status=$tableData['status'];
                $order_sequence=$tableData['order_sequence'];

                if($is_default==0){
                    $id= DB::table("roles")->where('name','=',$roleName)->pluck('id')->first();
                    if($id!='' && $id!=NULL){
                        $role_array[$role_i]['old_id']=$roleId;
                        $role_array[$role_i]['new_id']=$id;
                    }else{
                        $new=[];
                        $new['name']=ucfirst($roleName);
                        $new['company_id']=0;
                        $new['workspace_id']=0;
                        $new['user_id']=0;
                        $new['staff_id']=0;
                        $new['is_default']=0;
                        $new['status']=1;
                        $new['order_sequence']=$order_sequence;
                        $new['created_by']=0;
                        $new['created_at']=date('Y-m-d H:i:s');
                        $new['updated_at']=date('Y-m-d H:i:s');
                        DB::table("roles")->insert($new);
                        $id = DB::getPdo()->lastInsertId();
                        $role_array[$role_i]['old_id']=$roleId;
                        $role_array[$role_i]['new_id']=$id;
                    }
                }else{
                    $whereCondition=[
                        ['company_id','=',$companyID],
                        ['workspace_id','=',$workSpaceID],
                        ['name','=',$roleName],
                        ['status','=',$status],
                    ];
                    $id= DB::table("roles")->where($whereCondition)->pluck('id')->first();
                    if($id!='' && $id!=NULL){
                        $role_array[$role_i]['old_id']=$roleId;
                        $role_array[$role_i]['new_id']=$id;
                    }else{
                        $new=[];
                        $new['name']=ucfirst($roleName);
                        $new['company_id']=$companyID;
                        $new['workspace_id']=$workSpaceID;
                        $new['user_id']=$user->id;
                        $new['staff_id']=0;
                        $new['is_default']='1';
                        $new['status']=1;
                        $new['order_sequence']=$order_sequence;
                        $new['created_by']=0;
                        $new['created_at']=date('Y-m-d H:i:s');
                        $new['updated_at']=date('Y-m-d H:i:s');
                        DB::table("roles")->insert($new);
                        $id = DB::getPdo()->lastInsertId();
                        $role_array[$role_i]['old_id']=$roleId;
                        $role_array[$role_i]['new_id']=(int)$id;
                    }
                }

                $role_i++;
            }
            /*Roles table end*/

            /*Staff table statrs*/
            $staff_array=[];$staff_i=0;
            foreach ($tablesData['staff'] as $tableName => $tableData) {
                //dd($tableData);
                $staffId=$tableData['id'];
                $company_id=$tableData['company_id'];
                $workspace_id=$tableData['workspace_id'];
                $role_id=$tableData['role_id'];
                $first_name=$tableData['first_name'];
                $last_name=$tableData['last_name'];
                $mobile=$tableData['mobile'];
                $email=trim($tableData['email']);
                $status=$tableData['status'];
                $whereCondition=[
                    ['company_id','=',$companyID],
                    ['workspace_id','=',$workSpaceID],
                    ['first_name','=',$first_name],
                // ['last_name','=',$last_name],
                    ['email','=',$email],
                    //['status','=','1']
                ];
                $id= DB::table("staff")->where($whereCondition)->pluck('id')->first();
                if($id!='' && $id!=NULL){
                    $staff_array[$staff_i]['old_id']=$staffId;
                    $staff_array[$staff_i]['new_id']=$id;
                }else{
                    $new=[];
                    $new['company_id']=$companyID;
                    $new['workspace_id']=$workSpaceID;
                    $new['role_id']=MysqlUserDB::get_new_id($role_array,$role_id);
                    $new['user_id']=$user->id;
                    $new['first_name']=ucfirst($first_name);
                    $new['last_name']=ucfirst($last_name);
                    $new['mobile']=$mobile;
                    $new['email']=$email;
                    $new['address1']=$tableData['address1'];
                    $new['address2']=$tableData['address2'];
                    $new['city']=$tableData['city'];
                    $new['state']=$tableData['state'];
                    $new['country']=$tableData['country'];
                    $new['status']='1';
                    $new['created_at']=date('Y-m-d H:i:s');
                    $new['updated_at']=date('Y-m-d H:i:s');
                    DB::table("staff")->insert($new);
                    $id = DB::getPdo()->lastInsertId();
                    $staff_array[$staff_i]['old_id']=$staffId;
                    $staff_array[$staff_i]['new_id']=(int)$id;
                }

                $staff_i++;
            }
            /*Staff table end*/

            /*Order Article table statrs*/
            $article_array=[];$article_i=0;
            foreach ($tablesData['order_article_name'] as $tableName => $tableData) {
                //dd($tableData);
                $articleId=$tableData['id'];
                $articleName=$tableData['name'];
                $company_id=$tableData['company_id'];
                $workspace_id=$tableData['workspace_id'];
                $is_default=$tableData['is_default'];
                $status=$tableData['status'];

                if($is_default==0){
                    $id= DB::table("order_article_name")->where('name','=',$articleName)->pluck('id')->first();
                    if($id!='' && $id!=NULL){
                        $article_array[$article_i]['old_id']=$articleId;
                        $article_array[$article_i]['new_id']=$id;
                    }else{
                        $new=[];
                        $new['name']=ucfirst($articleName);
                        $new['company_id']=0;
                        $new['workspace_id']=0;
                        $new['user_id']=0;
                        $new['staff_id']=0;
                        $new['is_default']=0;
                        $new['status']=0;
                        $new['inquiry_reference_id']=0;
                        $new['created_by']=0;
                        $new['created_at']=date('Y-m-d H:i:s');
                        $new['updated_at']=date('Y-m-d H:i:s');
                        DB::table("order_article_name")->insert($new);
                        $id = DB::getPdo()->lastInsertId();
                        $article_array[$article_i]['old_id']=$articleId;
                        $article_array[$article_i]['new_id']=$id;
                    }
                }else{
                    $whereCondition=[
                        ['company_id','=',$companyID],
                        ['workspace_id','=',$workSpaceID],
                        ['name','=',$articleName],
                        ['status','=',$status],
                        ['inquiry_reference_id','=',0]
                    ];
                    $id= DB::table("order_article_name")->where($whereCondition)->pluck('id')->first();
                    if($id!='' && $id!=NULL){
                        $article_array[$article_i]['old_id']=$articleId;
                        $article_array[$article_i]['new_id']=$id;
                    }else{
                        $new=[];
                        $new['name']=ucfirst($articleName);
                        $new['company_id']=$companyID;
                        $new['workspace_id']=$workSpaceID;
                        $new['user_id']=$user->id;
                        $new['staff_id']=0;
                        $new['is_default']='1';
                        $new['status']=1;
                        $new['inquiry_reference_id']=0;
                        $new['created_by']=0;
                        $new['created_at']=date('Y-m-d H:i:s');
                        $new['updated_at']=date('Y-m-d H:i:s');
                        DB::table("order_article_name")->insert($new);
                        $id = DB::getPdo()->lastInsertId();
                        $article_array[$article_i]['old_id']=$articleId;
                        $article_array[$article_i]['new_id']=(int)$id;
                    }
                }

                $article_i++;
            }
            /*Order Article table end*/

            /*Order Buyer table statrs*/
            $buyer_array=[];$buyer_i=0;
            foreach ($tablesData['order_buyer'] as $tableName => $tableData) {
                //dd($tableData);
                $buyerId=$tableData['id'];
                $buyerName=$tableData['name'];
                $company_id=$tableData['company_id'];
                $workspace_id=$tableData['workspace_id'];

                $whereCondition=[
                    ['company_id','=',$companyID],
                    ['workspace_id','=',$workSpaceID],
                    ['name','=',$buyerName],
                ];
                $id= DB::table("order_buyer")->where($whereCondition)->pluck('id')->first();
                if($id!='' && $id!=NULL){
                    $buyer_array[$buyer_i]['old_id']=$buyerId;
                    $buyer_array[$buyer_i]['new_id']=$id;
                }else{
                    $new=[];
                    $new['name']=ucfirst($buyerName);
                    $new['company_id']=$companyID;
                    $new['workspace_id']=$workSpaceID;
                    $new['user_id']=$user->id;
                    $new['staff_id']=0;
                    $new['created_by']=$user->id;
                    $new['created_at']=date('Y-m-d H:i:s');
                    $new['updated_at']=date('Y-m-d H:i:s');
                    DB::table("order_buyer")->insert($new);
                    $id = DB::getPdo()->lastInsertId();
                    $buyer_array[$buyer_i]['old_id']=$buyerId;
                    $buyer_array[$buyer_i]['new_id']=(int)$id;
                }

                $buyer_i++;
            }
            /*Order Buyer table end*/

            /*Order Category table statrs*/
            $category_array=[];$category_i=0;
            foreach ($tablesData['order_category'] as $tableName => $tableData) {
                $categoryId=$tableData['id'];
                $categoryName=$tableData['name'];
                $company_id=$tableData['company_id'];
                $workspace_id=$tableData['workspace_id'];
                $is_default=$tableData['is_default'];
                $status=$tableData['status'];

                if($is_default==0){
                    $id= DB::table("order_category")->where('name','=',$categoryName)->pluck('id')->first();
                    if($id!='' && $id!=NULL){
                        $category_array[$category_i]['old_id']=$categoryId;
                        $category_array[$category_i]['new_id']=$id;
                    }else{
                        $new=[];
                        $new['name']=ucfirst($categoryName);
                        $new['company_id']=0;
                        $new['workspace_id']=0;
                        $new['user_id']=0;
                        $new['staff_id']=0;
                        $new['is_default']=0;
                        $new['status']=0;
                        $new['created_by']=0;
                        $new['created_at']=date('Y-m-d H:i:s');
                        $new['updated_at']=date('Y-m-d H:i:s');
                        DB::table("order_category")->insert($new);
                        $id = DB::getPdo()->lastInsertId();
                        $category_array[$category_i]['old_id']=$categoryId;
                        $category_array[$category_i]['new_id']=$id;
                    }
                }else{
                    $whereCondition=[
                        ['company_id','=',$companyID],
                        ['workspace_id','=',$workSpaceID],
                        ['name','=',$categoryName],
                        ['status','=',$status],
                    ];
                    $id= DB::table("order_category")->where($whereCondition)->pluck('id')->first();
                    if($id!='' && $id!=NULL){
                        $category_array[$category_i]['old_id']=$categoryId;
                        $category_array[$category_i]['new_id']=$id;
                    }else{
                        $new=[];
                        $new['name']=ucfirst($categoryName);
                        $new['company_id']=$companyID;
                        $new['workspace_id']=$workSpaceID;
                        $new['user_id']=$user->id;
                        $new['staff_id']=0;
                        $new['is_default']='1';
                        $new['status']=1;
                        $new['created_by']=$user->id;
                        $new['created_at']=date('Y-m-d H:i:s');
                        $new['updated_at']=date('Y-m-d H:i:s');
                        DB::table("order_category")->insert($new);
                        $id = DB::getPdo()->lastInsertId();
                        $category_array[$category_i]['old_id']=$categoryId;
                        $category_array[$category_i]['new_id']=(int)$id;
                    }
                }

                $category_i++;
            }
            /*Order Category table end*/

            /*Order Factory table statrs*/
            $factory_array=[];$factory_i=0;
            foreach ($tablesData['order_factory'] as $tableName => $tableData) {
                //dd($tableData);
                $factoryId=$tableData['id'];
                $factoryName=$tableData['name'];
                $company_id=$tableData['company_id'];
                $workspace_id=$tableData['workspace_id'];

                $whereCondition=[
                    ['company_id','=',$companyID],
                    ['workspace_id','=',$workSpaceID],
                    ['name','=',$factoryName],
                ];
                $id= DB::table("order_factory")->where($whereCondition)->pluck('id')->first();
                if($id!='' && $id!=NULL){
                    $factory_array[$factory_i]['old_id']=$factoryId;
                    $factory_array[$factory_i]['new_id']=$id;
                }else{
                    $new=[];
                    $new['name']=ucfirst($factoryName);
                    $new['company_id']=$companyID;
                    $new['workspace_id']=$workSpaceID;
                    $new['user_id']=$user->id;
                    $new['staff_id']=0;
                    $new['created_by']=$user->id;
                    $new['created_at']=date('Y-m-d H:i:s');
                    $new['updated_at']=date('Y-m-d H:i:s');
                    DB::table("order_factory")->insert($new);
                    $id = DB::getPdo()->lastInsertId();
                    $factory_array[$factory_i]['old_id']=$factoryId;
                    $factory_array[$factory_i]['new_id']=(int)$id;
                }

                $factory_i++;
            }
            /*Order Factory table end*/

            /*Order PCU table statrs*/
            $pcu_array=[];$pcu_i=0;
            foreach ($tablesData['order_pcu'] as $tableName => $tableData) {
                //dd($tableData);
                $pcuId=$tableData['id'];
                $pcuName=$tableData['name'];
                $company_id=$tableData['company_id'];
                $workspace_id=$tableData['workspace_id'];

                $whereCondition=[
                    ['company_id','=',$companyID],
                    ['workspace_id','=',$workSpaceID],
                    ['name','=',$pcuName],
                ];
                $id= DB::table("order_pcu")->where($whereCondition)->pluck('id')->first();
                if($id!='' && $id!=NULL){
                    $pcu_array[$pcu_i]['old_id']=$pcuId;
                    $pcu_array[$pcu_i]['new_id']=$id;
                }else{
                    $new=[];
                    $new['name']=ucfirst($pcuName);
                    $new['company_id']=$companyID;
                    $new['workspace_id']=$workSpaceID;
                    $new['user_id']=$user->id;
                    $new['staff_id']=0;
                    $new['created_by']=$user->id;
                    $new['created_at']=date('Y-m-d H:i:s');
                    $new['updated_at']=date('Y-m-d H:i:s');
                    DB::table("order_pcu")->insert($new);
                    $id = DB::getPdo()->lastInsertId();
                    $pcu_array[$pcu_i]['old_id']=$pcuId;
                    $pcu_array[$pcu_i]['new_id']=(int)$id;
                }

                $pcu_i++;
            }
            /*Order PCU table end*/

            /*Fabric table statrs*/
            $fabric_array=[];$fabric_i=0;
            if(isset($tablesData['fabric_type'])){
                foreach ($tablesData['fabric_type'] as $tableName => $tableData) {
                    //dd($tableData);
                    $fabricId=$tableData['id'];
                    $fabricName=$tableData['name'];
                    $company_id=$tableData['company_id'];
                    $workspace_id=$tableData['workspace_id'];
                    $is_default=$tableData['is_default'];
                    $status=$tableData['status'];

                    if($is_default==0){
                        $id= DB::table("fabric_type")->where('name','=',$fabricName)->pluck('id')->first();
                        if($id!='' && $id!=NULL){
                            $fabric_array[$fabric_i]['old_id']=$fabricId;
                            $fabric_array[$fabric_i]['new_id']=$id;
                        }else{
                            $new=[];
                            $new['name']=ucfirst($fabricName);
                            $new['company_id']=0;
                            $new['workspace_id']=0;
                            $new['user_id']=0;
                            $new['staff_id']=0;
                            $new['is_default']=0;
                            $new['status']=0;
                            $new['inquiry_reference_id']=0;
                            $new['created_by']=0;
                            $new['created_at']=date('Y-m-d H:i:s');
                            $new['updated_at']=date('Y-m-d H:i:s');
                            DB::table("fabric_type")->insert($new);
                            $id = DB::getPdo()->lastInsertId();
                            $fabric_array[$fabric_i]['old_id']=$fabricId;
                            $fabric_array[$fabric_i]['new_id']=$id;
                        }
                    }else{
                        $whereCondition=[
                            ['company_id','=',$companyID],
                            ['workspace_id','=',$workSpaceID],
                            ['name','=',$fabricName],
                            ['status','=',$status],
                            ['inquiry_reference_id','=',0]
                        ];
                        $id= DB::table("fabric_type")->where($whereCondition)->pluck('id')->first();
                        if($id!='' && $id!=NULL){
                            $fabric_array[$fabric_i]['old_id']=$fabricId;
                            $fabric_array[$fabric_i]['new_id']=$id;
                        }else{
                            $new=[];
                            $new['name']=ucfirst($fabricName);
                            $new['company_id']=$companyID;
                            $new['workspace_id']=$workSpaceID;
                            $new['user_id']=$user->id;
                            $new['staff_id']=0;
                            $new['is_default']='1';
                            $new['status']=1;
                            $new['inquiry_reference_id']=0;
                            $new['created_by']=0;
                            $new['created_at']=date('Y-m-d H:i:s');
                            $new['updated_at']=date('Y-m-d H:i:s');
                            DB::table("fabric_type")->insert($new);
                            $id = DB::getPdo()->lastInsertId();
                            $fabric_array[$fabric_i]['old_id']=$fabricId;
                            $fabric_array[$fabric_i]['new_id']=(int)$id;
                        }
                    }

                    $fabric_i++;
                }
            }
            /*Fabric table end*/

            /*Units table statrs*/
            $units_array=[];$units_i=0;
            if(isset($tablesData['order_units'])){
                foreach ($tablesData['order_units'] as $tableName => $tableData) {
                    $unitsId=$tableData['id'];
                    $unitName=$tableData['name'];
                    $company_id=$tableData['company_id'];
                    $workspace_id=$tableData['workspace_id'];
                    $is_default=$tableData['is_default'];
                    $status=$tableData['status'];
                    $bom_unit=$tableData['bom_unit'];

                    if($is_default==0){
                        $id= DB::table("order_units")->where('name','=',$unitName)->where('bom_unit','=',$bom_unit)->pluck('id')->first();
                        if($id!='' && $id!=NULL){
                            $units_array[$units_i]['old_id']=$unitsId;
                            $units_array[$units_i]['new_id']=$id;
                        }else{
                            $new=[];
                            $new['name']=ucfirst($unitName);
                            $new['company_id']=0;
                            $new['workspace_id']=0;
                            $new['user_id']=0;
                            $new['staff_id']=0;
                            $new['bom_unit']=$bom_unit;
                            $new['is_default']=0;
                            $new['status']=0;
                            $new['created_by']=0;
                            $new['created_at']=date('Y-m-d H:i:s');
                            $new['updated_at']=date('Y-m-d H:i:s');
                            DB::table("order_units")->insert($new);
                            $id = DB::getPdo()->lastInsertId();
                            $units_array[$units_i]['old_id']=$unitsId;
                            $units_array[$units_i]['new_id']=$id;
                        }
                    }else{
                        $whereCondition=[
                            ['company_id','=',$companyID],
                            ['workspace_id','=',$workSpaceID],
                            ['name','=',$unitName],
                            ['status','=',$status],
                            ['bom_unit','=',$bom_unit]
                        ];
                        $id= DB::table("order_units")->where($whereCondition)->pluck('id')->first();
                        if($id!='' && $id!=NULL){
                            $units_array[$units_i]['old_id']=$unitsId;
                            $units_array[$units_i]['new_id']=$id;
                        }else{
                            $new=[];
                            $new['name']=ucfirst($unitName);
                            $new['company_id']=$companyID;
                            $new['workspace_id']=$workSpaceID;
                            $new['user_id']=$user->id;
                            $new['staff_id']=0;
                            $new['is_default']='1';
                            $new['status']=1;
                            $new['bom_unit']=$bom_unit;
                            $new['created_by']=0;
                            $new['created_at']=date('Y-m-d H:i:s');
                            $new['updated_at']=date('Y-m-d H:i:s');
                            DB::table("order_units")->insert($new);
                            $id = DB::getPdo()->lastInsertId();
                            $units_array[$units_i]['old_id']=$unitsId;
                            $units_array[$units_i]['new_id']=(int)$id;
                        }
                    }

                    $units_i++;
                }
            }
            //dd($units_array);
            /*Units table end*/

            /*Order Template table statrs*/
            $template_array=[];$template_i=0;
            foreach ($tablesData['order_task_template'] as $tableName => $tableData) {
                //dd($tableData);
                $templateId=$tableData['id'];
                $templateName=$tableData['template_name'];
                $company_id=$tableData['company_id'];
                $workspace_id=$tableData['workspace_id'];

                $whereCondition=[
                    ['company_id','=',$companyID],
                    ['workspace_id','=',$workSpaceID],
                    ['template_name','=',$templateName],
                ];
                $id= DB::table("order_task_template")->where($whereCondition)->pluck('id')->first();
                if($id!='' && $id!=NULL){
                    $template_array[$template_i]['old_id']=$templateId;
                    $template_array[$template_i]['new_id']=$id;
                }else{
                    $new=[];
                    $new['company_id']=$companyID;
                    $new['workspace_id']=$workSpaceID;
                    $new['user_id']=$user->id;
                    $new['staff_id']=0;
                    $new['template_name']=ucfirst($templateName);
                    $new['status']='1';
                    $new['is_default']='1';
                    $new['task_template_structure']=$tableData['task_template_structure'];
                    $new['created_by']=$user->id;
                    $new['created_user_type']='user';
                    $new['created_at']=date('Y-m-d H:i:s');
                    $new['updated_at']=date('Y-m-d H:i:s');
                    DB::table("order_task_template")->insert($new);
                    $id = DB::getPdo()->lastInsertId();
                    $template_array[$template_i]['old_id']=$templateId;
                    $template_array[$template_i]['new_id']=(int)$id;
                }

                $template_i++;
            }
            /*Order Template table end*/

            /*Orders table statrs*/
            $orders_array=$ord_details=[];$orders_i=0;
            foreach ($tablesData['orders'] as $tableName => $tableData) {
                //dd($tableData);
                $orderId=$tableData['id'];
                $order_no = $tableData['order_no'];
                $style_no = $tableData['style_no'];
                $company_id=$tableData['company_id'];
                $workspace_id=$tableData['workspace_id'];

                $whereCondition=[
                    ['company_id','=',$companyID],
                    ['workspace_id','=',$workSpaceID],
                    ['order_no','=',$order_no],
                    ['style_no','=',$style_no],
                ];
                $id= DB::table("orders")->where($whereCondition)->pluck('id')->first();
                if($id!='' && $id!=NULL){
                    $orders_array[$orders_i]['old_id']=$orderId;
                    $orders_array[$orders_i]['new_id']=$id;

                    $ord_details[$orders_i]['id']=$id;
                    $ord_details[$orders_i]['order_no']=$order_no;
                    $ord_details[$orders_i]['style_no']=$style_no;
                }else{
                    $new=[];
                    $new['user_id']     = $user->id;
                    $new['company_id']  = $companyID;
                    $new['workspace_id']= $workSpaceID;
                    $new['staff_id']    = 0;
                    $new['order_no']    = $tableData['order_no'];
                    $new['style_no']    = $tableData['style_no'];
                    $new['buyer_id']    = ($tableData['buyer_id']!=Null && $tableData['buyer_id']!='')?MysqlUserDB::get_new_id($buyer_array,$tableData['buyer_id']):NULL;
                    $new['pcu_id']      = MysqlUserDB::get_new_id($pcu_array,$tableData['pcu_id']);
                    $new['factory_id']  = MysqlUserDB::get_new_id($factory_array,$tableData['factory_id']);
                    $new['fabric_id']   = MysqlUserDB::get_new_id($fabric_array,$tableData['fabric_id']);
                    $new['category_id'] = MysqlUserDB::get_new_id($category_array,$tableData['category_id']);
                    $new['article_id']  = MysqlUserDB::get_new_id($article_array,$tableData['article_id']);
                    $new['inquiry_date']= $tableData['inquiry_date'];
                    $new['order_price'] = $tableData['order_price'];
                    $new['income_terms']= $tableData['income_terms'];
                    $new['total_quantity']= $tableData['total_quantity'];
                    $new['no_of_deliverys']= $tableData['no_of_deliverys'];
                    $new['quantity_wise']= $tableData['quantity_wise'];
                    $new['tolerance_volume']= $tableData['tolerance_volume'];
                    $new['tolerance_perc']= $tableData['tolerance_perc'];
                    $new['cutting_start_date']= $tableData['cutting_start_date'];
                    $new['cutting_end_date']= $tableData['cutting_end_date'];
                    $new['cutting_accomplished_date']= $tableData['cutting_accomplished_date'];
                    $new['sewing_start_date']= $tableData['sewing_start_date'];
                    $new['sewing_end_date']= $tableData['sewing_end_date'];
                    $new['sewing_accomplished_date']= $tableData['sewing_accomplished_date'];
                    $new['packing_start_date']= $tableData['packing_start_date'];
                    $new['packing_end_date']= $tableData['packing_end_date'];
                    $new['packing_accomplished_date']= $tableData['packing_accomplished_date'];
                    $new['ref_img']= $tableData['ref_img'];
                    $new['cut_weekoffs']= $tableData['cut_weekoffs'];
                    $new['sew_weekoffs']= $tableData['sew_weekoffs'];
                    $new['pack_weekoffs']= $tableData['pack_weekoffs'];
                    $new['usual_weekoff']= $tableData['usual_weekoff'];
                    $new['currency_type']= $tableData['currency_type'];
                    $new['order_task_template']= MysqlUserDB::get_new_id($template_array,$tableData['order_task_template']);
                    $new['task_feeded']= $tableData['task_feeded'];
                    $new['pending_tasks']= $tableData['pending_tasks'];
                    $new['cutting_completion']= $tableData['cutting_completion'];
                    $new['sewing_completion']= $tableData['sewing_completion'];
                    $new['packing_completion']= $tableData['packing_completion'];
                    $new['step_level']= $tableData['step_level'];
                    $new['is_tolerance_req']= (string)$tableData['is_tolerance_req'];
                    $new['status']= (string)$tableData['status'];
                    $new['completed_on']= $tableData['completed_on'];
                    $new['completed_user_email']= $tableData['completed_user_email'];
                    $new['completed_staff_email']= $tableData['completed_staff_email'];
                    $new['status_request']= $tableData['status_request'];
                    $new['order_priority']= $tableData['order_priority'];
                    $new['action_done_user_id']= $tableData['action_done_user_id'];
                    $new['action_done_staff_id']= $tableData['action_done_staff_id'];
                    $new['action_done_at']= $tableData['action_done_at'];
                    $new['delivery_date']= $tableData['delivery_date'];
                    $new['units']= $tableData['units'];
                    $new['created_at']= $tableData['created_at'];
                    $new['updated_at']= $tableData['updated_at'];
                    DB::table("orders")->insert($new);
                    $id = DB::getPdo()->lastInsertId();
                    $orders_array[$orders_i]['old_id']=$orderId;
                    $orders_array[$orders_i]['new_id']=(int)$id;

                    $ord_details[$orders_i]['id']=$id;
                    $ord_details[$orders_i]['order_no']=$order_no;
                    $ord_details[$orders_i]['style_no']=$style_no;
                }

                $orders_i++;
            }
            /*Orders table end*/

            /*Order Contact table statrs*/
            $contact_array=[];$contact_i=0;
            foreach ($tablesData['order_contacts'] as $tableName => $tableData) {
                $contactId=$tableData['id'];
                $staff_id = MysqlUserDB::get_new_id($staff_array,$tableData['staff_id']);
                $order_id = MysqlUserDB::get_new_id($orders_array,$tableData['order_id']);
                $whereCondition=[
                    ['company_id','=',$companyID],
                    ['workspace_id','=',$workSpaceID],
                    ['staff_id','=',$staff_id],
                    ['order_id','=',$order_id],
                ];
                $id= DB::table("order_contacts")->where($whereCondition)->pluck('id')->first();
                if($id!='' && $id!=NULL){
                    $contact_array[$contact_i]['old_id']=$contactId;
                    $contact_array[$contact_i]['new_id']=$id;
                }else{
                    $new=[];
                    $new['user_id']     = $user->id;
                    $new['company_id']  = $companyID;
                    $new['workspace_id']= $workSpaceID;
                    $new['staff_id']    = MysqlUserDB::get_new_id($staff_array,$tableData['staff_id']);
                    $new['order_id']    = MysqlUserDB::get_new_id($orders_array,$tableData['order_id']);
                    $new['status']      = '1';
                    $new['created_at']  = $tableData['created_at'];
                    $new['updated_at']  = $tableData['updated_at'];
                    DB::table("order_contacts")->insert($new);
                    $id = DB::getPdo()->lastInsertId();
                    $contact_array[$contact_i]['old_id']=$contactId;
                    $contact_array[$contact_i]['new_id']=(int)$id;
                }

                $contact_i++;
            }
            /*Order Contact table end*/

            /*Order SKU table statrs*/
            $sku_array=[];$sku_i=0;
            foreach ($tablesData['order_sku'] as $tableName => $tableData) {
                $skuId=$tableData['id'];
                $order_id = MysqlUserDB::get_new_id($orders_array,$tableData['order_id']);
                $sku_color_id = MysqlUserDB::get_new_id($color_array,$tableData['sku_color_id']);
                $sku_size_id = MysqlUserDB::get_new_id($size_array,$tableData['sku_size_id']);
                $whereCondition=[
                    ['company_id','=',$companyID],
                    ['workspace_id','=',$workSpaceID],
                    ['sku_color_id','=',$sku_color_id],
                    ['sku_size_id','=',$sku_size_id],
                    ['order_id','=',$order_id],
                ];
                $id= DB::table("order_sku")->where($whereCondition)->pluck('id')->first();
                if($id!='' && $id!=NULL){
                    $sku_array[$sku_i]['old_id']=$skuId;
                    $sku_array[$sku_i]['new_id']=$id;
                }else{
                    $new=[];
                    $new['user_id']     = $user->id;
                    $new['company_id']  = $companyID;
                    $new['workspace_id']= $workSpaceID;
                    $new['staff_id']    = 0;
                    $new['order_id']    = MysqlUserDB::get_new_id($orders_array,$tableData['order_id']);
                    $new['sku_color_id']    = MysqlUserDB::get_new_id($color_array,$tableData['sku_color_id']);
                    $new['sku_size_id']    = MysqlUserDB::get_new_id($size_array,$tableData['sku_size_id']);
                    $new['sku_quantity']= $tableData['sku_quantity'];
                    $new['created_at']  = $tableData['created_at'];
                    $new['updated_at']  = $tableData['updated_at'];
                    DB::table("order_sku")->insert($new);
                    $id = DB::getPdo()->lastInsertId();
                    $sku_array[$sku_i]['old_id']=$skuId;
                    $sku_array[$sku_i]['new_id']=(int)$id;
                }

                $sku_i++;
            }
            /*Order SKU table end*/

            /*Order Task Data table statrs*/
            $task_array=[];$task_i=0;
            foreach ($tablesData['order_task_data'] as $tableName => $tableData) {
                $taskId=$tableData['id'];
                $order_id = MysqlUserDB::get_new_id($orders_array,$tableData['order_id']);
                $template_id = MysqlUserDB::get_new_id($template_array,$tableData['template_id']);
                $cat_title = $tableData['cat_title'];
                $task_title = $tableData['task_title'];
                $is_subtask = $tableData['is_subtask'];
                $subtask_title = $tableData['subtask_title'];
                $whereCondition=[
                    ['company_id','=',$companyID],
                    ['workspace_id','=',$workSpaceID],
                    ['order_id','=',$order_id],
                    ['cat_title','=',$cat_title],
                    ['task_title','=',$task_title],
                    ['is_subtask','=',$is_subtask],
                    ['subtask_title','=',$subtask_title],
                    ['template_id','=',$template_id],
                ];
                $id= DB::table("order_task_data")->where($whereCondition)->pluck('id')->first();
                if($id!='' && $id!=NULL){
                    $task_array[$task_i]['old_id']=$taskId;
                    $task_array[$task_i]['new_id']=$id;
                }else{
                    $new=[];
                    $new['user_id']     = $user->id;
                    $new['company_id']  = $companyID;
                    $new['workspace_id']= $workSpaceID;
                    $new['staff_id']    = 0;
                    $new['order_id']    = MysqlUserDB::get_new_id($orders_array,$tableData['order_id']);
                    $new['template_id'] = MysqlUserDB::get_new_id($template_array,$tableData['template_id']);
                    $new['cat_title']   = $tableData['cat_title'];
                    $new['cat_seq_no']   = $tableData['cat_seq_no'];
                    $new['task_seq_no']   = $tableData['task_seq_no'];
                    $new['task_title']   = $tableData['task_title'];
                    $new['parent_task_id']   = MysqlUserDB::get_new_id($task_array,$tableData['parent_task_id']);
                    $new['is_subtask']   = $tableData['is_subtask'];
                    $new['subtask_title']   = $tableData['subtask_title'];
                    $new['actual_start_date']   = $tableData['actual_start_date'];
                    $new['task_schedule_start_date']   = $tableData['task_schedule_start_date'];
                    $new['task_schedule_end_date']   = $tableData['task_schedule_end_date'];
                    $new['task_pic']   = MysqlUserDB::get_new_id($staff_array,$tableData['task_pic']);
                    $new['task_accomplished_date']   = $tableData['task_accomplished_date'];
                    $new['created_by']     = $user->id;
                    $new['created_user_type']     = 'user';
                    $new['created_at']  = $tableData['created_at'];
                    $new['updated_at']  = $tableData['updated_at'];
                    DB::table("order_task_data")->insert($new);
                    $id = DB::getPdo()->lastInsertId();
                    $task_array[$task_i]['old_id']=$taskId;
                    $task_array[$task_i]['new_id']=(int)$id;
                }

                $task_i++;
            }
            /*Order Task Data table end*/

            /*Order Additional Spec table statrs*/
            $spec_array=[];$spec_i=0;
            foreach ($tablesData['order_add_spec'] as $tableName => $tableData) {
                $specId=$tableData['id'];
                $order_id = MysqlUserDB::get_new_id($orders_array,$tableData['order_id']);
                $filename = $tableData['filename'];
                $whereCondition=[
                    ['company_id','=',$companyID],
                    ['workspace_id','=',$workSpaceID],
                    ['order_id','=',$order_id],
                    ['filename','=',$filename],
                ];
                $id= DB::table("order_add_spec")->where($whereCondition)->pluck('id')->first();
                if($id!='' && $id!=NULL){
                    $spec_array[$spec_i]['old_id']=$specId;
                    $spec_array[$spec_i]['new_id']=$id;
                }else{
                    $new=[];
                    $new['user_id']     = $user->id;
                    $new['company_id']  = $companyID;
                    $new['workspace_id']= $workSpaceID;
                    $new['staff_id']    = 0;
                    $new['order_id']    = MysqlUserDB::get_new_id($orders_array,$tableData['order_id']);
                    $new['filename']   = $tableData['filename'];
                    $new['orginalfilename']   = $tableData['orginalfilename'];
                    $new['filepath']   = $tableData['filepath'];
                    $new['filesize']   = $tableData['filesize'];
                    $new['fileorder']   = $tableData['fileorder'];
                    $new['status']   = '1';
                    $new['created_at']  = $tableData['created_at'];
                    $new['updated_at']  = $tableData['updated_at'];
                    DB::table("order_add_spec")->insert($new);
                    $id = DB::getPdo()->lastInsertId();
                    $spec_array[$spec_i]['old_id']=$specId;
                    $spec_array[$spec_i]['new_id']=(int)$id;
                }

                $spec_i++;
            }
            /*Order Additional Spec table end*/

            /*Order Production Data table statrs*/
            $production_array=[];$production_i=0;
            foreach ($tablesData['order_production_data'] as $tableName => $tableData) {
                $productionId=$tableData['id'];
                $order_id = MysqlUserDB::get_new_id($orders_array,$tableData['order_id']);
                $date_of_production = $tableData['date_of_production'];
                $type_of_production = $tableData['type_of_production'];
                $whereCondition=[
                    ['company_id','=',$companyID],
                    ['workspace_id','=',$workSpaceID],
                    ['order_id','=',$order_id],
                    ['date_of_production','=',$date_of_production],
                    ['type_of_production','=',$type_of_production],
                ];
                $id= DB::table("order_production_data")->where($whereCondition)->pluck('id')->first();
                if($id!='' && $id!=NULL){
                    $production_array[$production_i]['old_id']=$productionId;
                    $production_array[$production_i]['new_id']=$id;
                }else{
                    $new=[];
                    $new['user_id']     = $user->id;
                    $new['company_id']  = $companyID;
                    $new['workspace_id']= $workSpaceID;
                    $new['staff_id']    = 0;
                    $new['order_id']    = MysqlUserDB::get_new_id($orders_array,$tableData['order_id']);
                    $new['date_of_production']   = $tableData['date_of_production'];
                    $new['type_of_production']   = $tableData['type_of_production'];
                    $new['is_accomplished']   = $tableData['is_accomplished'];
                    $new['target_value']   = $tableData['target_value'];
                    $new['actual_value']   = $tableData['actual_value'];
                    $new['holiday_flag']   = $tableData['holiday_flag'];
                    $new['holiday_detail']   = $tableData['holiday_detail'];
                    $new['status']   = '1';
                    $new['created_at']  = $tableData['created_at'];
                    $new['updated_at']  = $tableData['updated_at'];
                    DB::table("order_production_data")->insert($new);
                    $id = DB::getPdo()->lastInsertId();
                    $production_array[$production_i]['old_id']=$productionId;
                    $production_array[$production_i]['new_id']=(int)$id;
                }

                $production_i++;
            }
            //dd($production_array);
            /*Order Production Data table end*/

            /*Order Update table statrs*/
            $update_array=[];$update_i=0;
            foreach ($tablesData['update_order_action'] as $tableName => $tableData) {
                $updateId=$tableData['id'];
                $order_id = MysqlUserDB::get_new_id($orders_array,$tableData['order_id']);
                $action_type = $tableData['action_type'];
                $whereCondition=[
                    ['company_id','=',$companyID],
                    ['workspace_id','=',$workSpaceID],
                    ['order_id','=',$order_id],
                    ['action_type','=',$action_type],
                ];
                $id= DB::table("update_order_action")->where($whereCondition)->pluck('id')->first();
                if($id!='' && $id!=NULL){
                    $update_array[$update_i]['old_id']=$updateId;
                    $update_array[$update_i]['new_id']=$id;
                }else{
                    $new=[];
                    $new['user_id']     = $user->id;
                    $new['company_id']  = $companyID;
                    $new['workspace_id']= $workSpaceID;
                    $new['staff_id']    = 0;
                    $new['order_id']    = MysqlUserDB::get_new_id($orders_array,$tableData['order_id']);
                    $new['order_no']   = $tableData['order_no'];
                    $new['reason']   = $tableData['reason'];
                    $new['action_type']   = $tableData['action_type'];
                    $new['created_at']  = $tableData['created_at'];
                    $new['updated_at']  = $tableData['updated_at'];
                    DB::table("update_order_action")->insert($new);
                    $id = DB::getPdo()->lastInsertId();
                    $update_array[$update_i]['old_id']=$updateId;
                    $update_array[$update_i]['new_id']=(int)$id;
                }

                $update_i++;
            }
            //dd($update_array);
            /*Order Update table end*/

            /*Update SKU QTY table statrs*/
            $sku_qty_array=[];$sku_qty_i=0;
            foreach ($tablesData['update_sku_quantities'] as $tableName => $tableData) {
                $skuqtyId=$tableData['id'];
                $order_id = MysqlUserDB::get_new_id($orders_array,$tableData['order_id']);
                $sku_id = MysqlUserDB::get_new_id($sku_array,$tableData['sku_id']);
                $color_id = MysqlUserDB::get_new_id($color_array,$tableData['color_id']);
                $size_id = MysqlUserDB::get_new_id($size_array,$tableData['size_id']);
                $type_of_production = $tableData['type_of_production'];
                $sku_date = $tableData['sku_date'];
                $updated_quantity = $tableData['updated_quantity'];
                $whereCondition=[
                    ['company_id','=',$companyID],
                    ['workspace_id','=',$workSpaceID],
                    ['sku_id','=',$sku_id],
                    ['color_id','=',$color_id],
                    ['order_id','=',$order_id],
                    ['size_id','=',$size_id],
                    ['type_of_production','=',$type_of_production],
                    ['sku_date','=',$sku_date],
                    ['updated_quantity','=',$updated_quantity],
                ];
                $id= DB::table("update_sku_quantities")->where($whereCondition)->pluck('id')->first();
                if($id!='' && $id!=NULL){
                    $sku_qty_array[$sku_qty_i]['old_id']=$skuqtyId;
                    $sku_qty_array[$sku_qty_i]['new_id']=$id;
                }else{
                    $new=[];
                    $new['user_id']     = $user->id;
                    $new['company_id']  = $companyID;
                    $new['workspace_id']= $workSpaceID;
                    $new['staff_id']    = 0;
                    $new['sku_id']      = MysqlUserDB::get_new_id($sku_array,$tableData['sku_id']);
                    $new['order_id']    = MysqlUserDB::get_new_id($orders_array,$tableData['order_id']);
                    $new['color_id']    = MysqlUserDB::get_new_id($color_array,$tableData['color_id']);
                    $new['size_id']     = MysqlUserDB::get_new_id($color_array,$tableData['size_id']);
                    $new['type_of_production']   = $tableData['type_of_production'];
                    $new['updated_quantity']   = $tableData['updated_quantity'];
                    $new['target_value']   = $tableData['target_value'];
                    $new['sku_date']   = $tableData['sku_date'];
                    $new['created_at']  = $tableData['created_at'];
                    $new['updated_at']  = $tableData['updated_at'];
                    DB::table("update_sku_quantities")->insert($new);
                    $id = DB::getPdo()->lastInsertId();
                    $sku_qty_array[$sku_qty_i]['old_id']=$skuqtyId;
                    $sku_qty_array[$sku_qty_i]['new_id']=(int)$id;
                }

                $sku_qty_i++;
            }
            //dd($sku_qty_array);
            /*Update SKU QTY table end*/

            /*Vendor List table statrs*/
            $vendor_array=[];$vendor_i=0;
            if(isset($tablesData['inquiry_label_vendor_list'])){
                foreach ($tablesData['inquiry_label_vendor_list'] as $tableName => $tableData) {
                    $vendorId=$tableData['id'];
                    $vendor_name = $tableData['vendor_name'];
                    $whereCondition=[
                        ['company_id','=',$companyID],
                        ['workspace_id','=',$workSpaceID],
                        ['vendor_name','=',$vendor_name],
                    ];
                    $id= DB::table("inquiry_label_vendor_list")->where($whereCondition)->pluck('id')->first();
                    if($id!='' && $id!=NULL){
                        $vendor_array[$vendor_i]['old_id']=$vendorId;
                        $vendor_array[$vendor_i]['new_id']=$id;
                    }else{
                        $new=[];
                        $new['company_id']  = $companyID;
                        $new['workspace_id']= $workSpaceID;
                        $new['vendor_name']   = $tableData['vendor_name'];
                        $new['website']  = $tableData['website'];
                        $new['office_address']        = $tableData['office_address'];
                        $new['factory_address']   = $tableData['factory_address'];
                        $new['contact_details']  = $tableData['contact_details'];
                        $new['category_ids']  = $tableData['category_ids'];
                        $new['created_user_type']   = 'user';
                        $new['created_user_id']  = $user->id;
                        $new['updated_user_type']   = NULL;
                        $new['updated_user_id']  = 0;
                        $new['created_at']  = $tableData['created_at'];
                        $new['updated_at']  = $tableData['updated_at'];
                        DB::table("inquiry_label_vendor_list")->insert($new);
                        $id = DB::getPdo()->lastInsertId();
                        $vendor_array[$vendor_i]['old_id']=$vendorId;
                        $vendor_array[$vendor_i]['new_id']=(int)$id;
                    }

                    $vendor_i++;
                }
            }
            //dd($vendor_array);
            /*Vendor List table end*/

            /*Order BOM table statrs*/
            $bom_array=[];$bom_i=0;
            if(isset($tablesData['order_bom'])){
                foreach ($tablesData['order_bom'] as $tableName => $tableData) {
                    $bomId=$tableData['id'];
                    $order_id = MysqlUserDB::get_new_id($orders_array,$tableData['order_id']);
                    $whereCondition=[
                        ['order_id','=',$order_id],
                    ];
                    $id= DB::table("order_bom")->where($whereCondition)->pluck('id')->first();
                    if($id!='' && $id!=NULL){
                        $bom_array[$bom_i]['old_id']=$bomId;
                        $bom_array[$bom_i]['new_id']=$id;
                    }else{
                        $new=[];
                        $new['order_id']    = MysqlUserDB::get_new_id($orders_array,$tableData['order_id']);
                        $new['sewing_accessories']   = $tableData['sewing_accessories'];
                        $new['packing_accessories']  = $tableData['packing_accessories'];
                        $new['miscellaneous']        = $tableData['miscellaneous'];
                        $new['is_approval']   = $tableData['is_approval'];
                        $new['created_at']  = $tableData['created_at'];
                        $new['updated_at']  = $tableData['updated_at'];
                        $new['created_user_id']   = $tableData['created_user_id']>0 ?$user->id:0;
                        $new['created_staff_id']  = $tableData['created_staff_id']>0 ?MysqlUserDB::get_new_id($staff_array,$tableData['created_staff_id']):0;
                        $new['updated_user_id']   = $tableData['updated_user_id']>0 ?$user->id:0;
                        $new['updated_staff_id']  = $tableData['updated_staff_id']>0 ?MysqlUserDB::get_new_id($staff_array,$tableData['updated_staff_id']):0;
                        DB::table("order_bom")->insert($new);
                        $id = DB::getPdo()->lastInsertId();
                        $bom_array[$bom_i]['old_id']=$bomId;
                        $bom_array[$bom_i]['new_id']=(int)$id;
                    }

                    $bom_i++;
                }
            }
            //dd($bom_array);
            /*Order BOM table end*/

            /*Multiple Delivey Dates table statrs*/
            $del_array=[];$del_i=0;
            if(isset($tablesData['multiple_delivery_dates'])){
                foreach ($tablesData['multiple_delivery_dates'] as $tableName => $tableData) {
                    $delId=$tableData['id'];
                    $order_id = MysqlUserDB::get_new_id($orders_array,$tableData['order_id']);
                    $delivery_date = $tableData['delivery_date'];
                    $whereCondition=[
                        ['company_id','=',$companyID],
                        ['workspace_id','=',$workSpaceID],
                        ['order_id','=',$order_id],
                        ['delivery_date','=',$delivery_date],
                    ];
                    $id= DB::table("multiple_delivery_dates")->where($whereCondition)->pluck('id')->first();
                    if($id!='' && $id!=NULL){
                        $del_array[$del_i]['old_id']=$delId;
                        $del_array[$del_i]['new_id']=$id;
                    }else{
                        $new=[];
                        $new['order_id']    = MysqlUserDB::get_new_id($orders_array,$tableData['order_id']);
                        $new['company_id']  = $companyID;
                        $new['workspace_id']= $workSpaceID;
                        $new['delivery_date']   = $tableData['delivery_date'];
                        $new['total_delivered_quantity']  = $tableData['total_delivered_quantity'];
                        $new['is_delivered']        = $tableData['is_delivered'];
                        $new['delivery_comments']   = $tableData['delivery_comments'];
                        $new['created_at']  = $tableData['created_at'];
                        $new['updated_at']  = $tableData['updated_at'];
                        DB::table("multiple_delivery_dates")->insert($new);
                        $id = DB::getPdo()->lastInsertId();
                        $del_array[$del_i]['old_id']=$delId;
                        $del_array[$del_i]['new_id']=(int)$id;
                    }

                    $del_i++;
                }
            }
            //dd($del_array);
            /*Multiple Delivey Dates table end*/

            /*Partial Deliveries table statrs*/
            $partial_array=[];$partial_i=0;
            if(isset($tablesData['partial_deliveries'])){
                foreach ($tablesData['partial_deliveries'] as $tableName => $tableData) {
                    $partialId=$tableData['id'];
                    $order_id = MysqlUserDB::get_new_id($orders_array,$tableData['order_id']);
                    $color_id = MysqlUserDB::get_new_id($color_array,$tableData['color_id']);
                    $size_id = MysqlUserDB::get_new_id($size_array,$tableData['size_id']);
                    $delivery_date = $tableData['delivery_date'];
                    $whereCondition=[
                        ['company_id','=',$companyID],
                        ['workspace_id','=',$workSpaceID],
                        ['order_id','=',$order_id],
                        ['color_id','=',$color_id],
                        ['size_id','=',$size_id],
                        ['delivery_date','=',$delivery_date],
                    ];
                    $id= DB::table("partial_deliveries")->where($whereCondition)->pluck('id')->first();
                    if($id!='' && $id!=NULL){
                        $partial_array[$partial_i]['old_id']=$partialId;
                        $partial_array[$partial_i]['new_id']=$id;
                    }else{
                        $new=[];
                        $new['order_id']    = MysqlUserDB::get_new_id($orders_array,$tableData['order_id']);
                        $new['company_id']  = $companyID;
                        $new['workspace_id']= $workSpaceID;
                        $new['user_type']   = 'User';
                        $new['user_id']   = $user->id;
                        $new['staff_id']   = 0;
                        $new['delivery_date']   = $tableData['delivery_date'];
                        $new['color_id']  = MysqlUserDB::get_new_id($color_array,$tableData['color_id']);
                        $new['size_id']   = MysqlUserDB::get_new_id($size_array,$tableData['size_id']);
                        $new['quantity']        = $tableData['quantity'];
                        $new['delivery_comments']   = $tableData['delivery_comments'];
                        $new['created_at']  = $tableData['created_at'];
                        $new['updated_at']  = $tableData['updated_at'];
                        DB::table("partial_deliveries")->insert($new);
                        $id = DB::getPdo()->lastInsertId();
                        $partial_array[$partial_i]['old_id']=$partialId;
                        $partial_array[$partial_i]['new_id']=(int)$id;
                    }

                    $partial_i++;
                }
            }
            //dd($partial_array);
            /*Partial Deliveries table end*/
            $logArr['data']['color'] = $color_array;
            $logArr['data']['size'] = $size_array;
            $logArr['data']['roles'] = $role_array;
            $logArr['data']['staff'] = $staff_array;
            $logArr['data']['article'] = $article_array;
            $logArr['data']['buyer'] = $buyer_array;
            $logArr['data']['category'] = $category_array;
            $logArr['data']['factory'] = $factory_array;
            $logArr['data']['pcu'] = $pcu_array;
            $logArr['data']['fabric'] = $fabric_array;
            $logArr['data']['units'] = $units_array;
            $logArr['data']['templates'] = $template_array;
            $logArr['data']['orders'] = $orders_array;
            $logArr['data']['order_contact'] = $contact_array;
            $logArr['data']['order_sku'] = $sku_array;
            $logArr['data']['order_task'] = $task_array;
            $logArr['data']['order_add_spec'] = $spec_array;
            $logArr['data']['order_production'] = $production_array;
            $logArr['data']['update_order_action'] = $update_array;
            $logArr['data']['update_sku_quantities'] = $sku_qty_array;
            $logArr['data']['vendor_list'] = $vendor_array;
            $logArr['data']['order_bom'] = $bom_array;
            $logArr['data']['multiple_delivery_dates'] = $del_array;
            $logArr['data']['partial_deliveries'] = $partial_array;

        }
        if(!empty($logArr)){
            $logArr['system_info'] = json_encode($_SERVER['REMOTE_ADDR']);
            $logArr['data'] = json_encode($logArr['data']);
            $logArr['orders'] = json_encode($ord_details);
            $logArr['created_at'] = date('Y-m-d H:i:s');

            DB::table("data_import_logs")->insert($logArr);
        }
        return response()->json(["status_code"=>200,"status" =>"Success","message"=>"Successfully Updated","data"=>$tablesData]);
        // return CommonApp::webEncrypt($res);
    }

    public static function get_new_id($array,$old_id){
        foreach ($array as $key => $val) {
            if ($val['old_id'] === $old_id) {
                return $val['new_id'];
            }
        }
        return $old_id;
    }

    public function importColors(Request $request){
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'file'=>'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $workSpaceID=$request->workspace_id;
        $companyID=$request->company_id;
        $file = $request->file('file');

        /*Current company user details */
        $user= DB::table("users")->where('company_id','=',$companyID)->first();
        $color_array=$logArr=[];$color_i=0;
        $logArr['company_id']=$companyID;
        $logArr['workspace_id']=$workSpaceID;
        $logArr['table_name']='color';
        if(stristr($file->getClientOriginalName(),'.xls')){
            $datas = Excel::toArray(new ImportXL,request()->file('file'));
            $data=json_encode($datas);
            $data = str_replace('[[','',$data);
            $data = str_replace(']]','',$data);
            $data = str_replace('],',']],',$data);
            $data = explode('],',$data);
            //echo count($data); exit;
            for($i=1;$i<count($data);$i++){
                $data[$i] = str_replace('"','',$data[$i]);
                $data[$i] = str_replace('[','',$data[$i]);
                $data[$i] = str_replace(']','',$data[$i]);
                $data[$i] = explode(',',$data[$i]);
                $color_i=$i;
                $colorName = $data[$i][0];
                $is_default = strtolower($data[$i][1])=='yes'?0:1;

                if($colorName!='' && $colorName!='null'){
                    if($is_default==0){
                        $id= DB::table("color")->where('name','=',$colorName)->pluck('id')->first();
                        if($id!='' && $id!=NULL){
                            $color_array[$color_i]['old_id']=0;
                            $color_array[$color_i]['new_id']=$id;
                        }else{
                            $new=[];
                            $new['name']=ucfirst($colorName);
                            $new['company_id']=0;
                            $new['workspace_id']=0;
                            $new['user_id']=0;
                            $new['staff_id']=0;
                            $new['is_default']=0;
                            $new['status']=1;
                            $new['created_by']=0;
                            $new['created_at']=date('Y-m-d H:i:s');
                            $new['updated_at']=date('Y-m-d H:i:s');
                            DB::table("color")->insert($new);
                            $id = DB::getPdo()->lastInsertId();
                            $color_array[$color_i]['old_id']=1;
                            $color_array[$color_i]['new_id']=$id;
                        }
                    }else{
                        $whereCondition=[
                            ['company_id','=',$companyID],
                            ['workspace_id','=',$workSpaceID],
                            ['name','=',$colorName]
                        ];
                        $id= DB::table("color")->where($whereCondition)->pluck('id')->first();
                        if($id!='' && $id!=NULL){
                            $color_array[$color_i]['old_id']=0;
                            $color_array[$color_i]['new_id']=$id;
                        }else{
                            $new=[];
                            $new['name']=ucfirst($colorName);
                            $new['company_id']=$companyID;
                            $new['workspace_id']=$workSpaceID;
                            $new['user_id']=$user->id;
                            $new['staff_id']=0;
                            $new['is_default']='1';
                            $new['status']='1';
                            $new['created_by']=0;
                            $new['created_at']=date('Y-m-d H:i:s');
                            $new['updated_at']=date('Y-m-d H:i:s');
                            DB::table("color")->insert($new);
                            $id = DB::getPdo()->lastInsertId();
                            $color_array[$color_i]['old_id']=1;
                            $color_array[$color_i]['new_id']=(int)$id;
                        }
                    }
                }
            }
        }else {
            return response()->json(["status_code"=>401,"error"=>'Invalid file']);
        }
        if(!empty($logArr)){
            $logArr['ip_address'] = json_encode($_SERVER['REMOTE_ADDR']);
            $logArr['data'] = json_encode(array_values($color_array));
            $logArr['order_id'] = 0;
            $logArr['created_at'] = date('Y-m-d H:i:s');

            DB::table("excel_import_logs")->insert($logArr);
        }
        return response()->json(["status_code"=>200,"status" =>"Success","message"=>"Successfully Updated","data"=>$color_array]);
    }

    public function importSize(Request $request){
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'file'=>'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $workSpaceID=$request->workspace_id;
        $companyID=$request->company_id;
        $file = $request->file('file');

        /*Current company user details */
        $user= DB::table("users")->where('company_id','=',$companyID)->first();
        $data_array=$logArr=[];$data_i=0;
        $logArr['company_id']=$companyID;
        $logArr['workspace_id']=$workSpaceID;
        $logArr['table_name']='size';
        if(stristr($file->getClientOriginalName(),'.xls')){
            $datas = Excel::toArray(new ImportXL,request()->file('file'));
            $data=json_encode($datas);
            $data = str_replace('[[','',$data);
            $data = str_replace(']]','',$data);
            $data = str_replace('],',']],',$data);
            $data = explode('],',$data);
            //echo count($data); exit;

            for($i=1;$i<count($data);$i++){
                $data[$i] = str_replace('"','',$data[$i]);
                $data[$i] = str_replace('[','',$data[$i]);
                $data[$i] = str_replace(']','',$data[$i]);
                $data[$i] = explode(',',$data[$i]);
                $data_i=$i;
                $Name = $data[$i][0];
                $is_default = strtolower($data[$i][1])=='yes'?0:1;
                $category = trim($data[$i][2]);
                if($Name!='' && $Name!='null'){
                if($is_default==0){
                    $id= DB::table("size")->where('name','=',$Name)->where('category','=',$category)->pluck('id')->first();
                    if($id!='' && $id!=NULL){
                        $data_array[$data_i]['old_id']=0;
                        $data_array[$data_i]['new_id']=$id;
                    }else{
                        $new=[];
                        $new['name']=ucfirst($Name);
                        $new['company_id']=0;
                        $new['workspace_id']=0;
                        $new['user_id']=0;
                        $new['staff_id']=0;
                        $new['is_default']='0';
                        $new['status']='1';
                        $new['category']=ucfirst($category);
                        $new['created_by']=0;
                        $new['created_at']=date('Y-m-d H:i:s');
                        $new['updated_at']=date('Y-m-d H:i:s');
                        DB::table("size")->insert($new);
                        $id = DB::getPdo()->lastInsertId();
                        $data_array[$data_i]['old_id']=1;
                        $data_array[$data_i]['new_id']=$id;
                    }
                    }else{
                        $whereCondition=[
                            ['company_id','=',$companyID],
                            ['workspace_id','=',$workSpaceID],
                            ['name','=',$Name],
                            ['category','=',$category]
                        ];
                        $id= DB::table("size")->where($whereCondition)->pluck('id')->first();
                        if($id!='' && $id!=NULL){
                            $data_array[$data_i]['old_id']=0;
                            $data_array[$data_i]['new_id']=$id;
                        }else{
                            $new=[];
                            $new['name']=ucfirst($Name);
                            $new['company_id']=$companyID;
                            $new['workspace_id']=$workSpaceID;
                            $new['user_id']=$user->id;
                            $new['staff_id']=0;
                            $new['is_default']='1';
                            $new['status']='1';
                            $new['category']=ucfirst($category);
                            $new['created_by']=0;
                            $new['created_at']=date('Y-m-d H:i:s');
                            $new['updated_at']=date('Y-m-d H:i:s');
                            DB::table("size")->insert($new);
                            $id = DB::getPdo()->lastInsertId();
                            $data_array[$data_i]['old_id']=1;
                            $data_array[$data_i]['new_id']=(int)$id;
                        }
                    }
                }
            }
        }else {
            return response()->json(["status_code"=>401,"error"=>'Invalid file']);
        }
        if(!empty($logArr)){
            $logArr['ip_address'] = json_encode($_SERVER['REMOTE_ADDR']);
            $logArr['data'] = json_encode(array_values($data_array));
            $logArr['order_id'] = 0;
            $logArr['created_at'] = date('Y-m-d H:i:s');
            DB::table("excel_import_logs")->insert($logArr);
        }
        return response()->json(["status_code"=>200,"status" =>"Success","message"=>"Successfully Updated","data"=>$data_array]);
    }

    public function importStaff(Request $request){
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'file'=>'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $workSpaceID=$request->workspace_id;
        $companyID=$request->company_id;
        $file = $request->file('file');
        /*Current company user details */
        $user= DB::table("users")->where('company_id','=',$companyID)->first();
        $data_array=$logArr=[];$data_i=0;
        $logArr['company_id']=$companyID;
        $logArr['workspace_id']=$workSpaceID;
        $logArr['table_name']='staff';
        if(stristr($file->getClientOriginalName(),'.xls')){
            $datas = Excel::toArray(new ImportXL,request()->file('file'));
            $data=json_encode($datas);
            $data = str_replace('[[','',$data);
            $data = str_replace(']]','',$data);
            $data = str_replace('],',']],',$data);
            $data = explode('],',$data);
            //echo count($data); exit;

            for($i=1;$i<count($data);$i++){
                $data[$i] = str_replace('"','',$data[$i]);
                $data[$i] = str_replace('[','',$data[$i]);
                $data[$i] = str_replace(']','',$data[$i]);
                $data[$i] = explode(',',$data[$i]);
                $data_i=$i;
                $first_name = trim($data[$i][0]);
                $last_name = trim($data[$i][1]);
                $mobile = trim($data[$i][2]);
                $email = trim($data[$i][3]);

                if($first_name!='' && $email!='' && $first_name!='null' && $email!='null'){
                    $whereCondition=[
                        ['company_id','=',$companyID],
                        ['workspace_id','=',$workSpaceID],
                        ['first_name','=',$first_name],
                        ['email','=',$email],
                    ];
                    $id= DB::table("staff")->where($whereCondition)->pluck('id')->first();
                    if($id!='' && $id!=NULL){
                        $data_array[$data_i]['old_id']=0;
                        $data_array[$data_i]['new_id']=$id;
                    }else{
                        $new=[];
                        $new['company_id']=$companyID;
                        $new['workspace_id']=$workSpaceID;
                        $new['role_id']=MysqlUserDB::get_role_id(trim($data[$i][8]),$companyID,$workSpaceID,$user->id);
                        $new['user_id']=$user->id;
                        $new['first_name']=ucfirst($first_name);
                        $new['last_name']=ucfirst($last_name);
                        $new['mobile']=$mobile;
                        $new['email']=$email;
                        $new['address1']=trim($data[$i][4]);
                        $new['address2']=trim($data[$i][5]);
                        $new['city']=trim($data[$i][6]);
                        $new['state']=trim($data[$i][7]);
                        $new['country']='0';
                        $new['status']='1';
                        $new['created_at']=date('Y-m-d H:i:s');
                        $new['updated_at']=date('Y-m-d H:i:s');
                        DB::table("staff")->insert($new);
                        $id = DB::getPdo()->lastInsertId();
                        $data_array[$data_i]['old_id']=1;
                        $data_array[$data_i]['new_id']=(int)$id;
                    }
                }
            }
        }else {
            return response()->json(["status_code"=>401,"error"=>'Invalid file']);
        }
        if(!empty($logArr)){
            $logArr['ip_address'] = json_encode($_SERVER['REMOTE_ADDR']);
            $logArr['data'] = json_encode(array_values($data_array));
            $logArr['order_id'] = 0;
            $logArr['created_at'] = date('Y-m-d H:i:s');
            DB::table("excel_import_logs")->insert($logArr);
        }
        return response()->json(["status_code"=>200,"status" =>"Success","message"=>"Successfully Updated","data"=>$data_array]);
    }

    public function importOrders(Request $request){
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'file'=>'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $workSpaceID=$request->workspace_id;
        $companyID=$request->company_id;
        $file = $request->file('file');
        /*Current company user details */
        $user= DB::table("users")->where('company_id','=',$companyID)->first();
        $data_array=$logArr=[];$data_i=0;
        $logArr['company_id']=$companyID;
        $logArr['workspace_id']=$workSpaceID;
        $logArr['table_name']='orders';
        if(stristr($file->getClientOriginalName(),'.xls')){
            $datas = Excel::toArray(new ImportXL,request()->file('file'));
            $data=json_encode($datas);
            $data = str_replace('[[','',$data);
            $data = str_replace(']]','',$data);
            $data = str_replace('],',']],',$data);
            $data = explode('],',$data);
            //echo count($data); exit;

            for($i=1;$i<count($data);$i++){
                $data[$i] = str_replace('"','',$data[$i]);
                $data[$i] = str_replace('[','',$data[$i]);
                $data[$i] = str_replace(']','',$data[$i]);
                $data[$i] = explode(',',$data[$i]);
                $data_i=$i;
                $order_no = trim($data[$i][0]);
                $style_no = trim($data[$i][1]);

                if($order_no!='' && $style_no!='' && $order_no!='null' && $style_no!='null'){
                    $whereCondition=[
                        ['company_id','=',$companyID],
                        ['workspace_id','=',$workSpaceID],
                        ['order_no','=',$order_no],
                        ['style_no','=',$style_no],
                    ];
                    $id= DB::table("orders")->where($whereCondition)->pluck('id')->first();
                    if($id!='' && $id!=NULL){
                        $data_array[$data_i]['old_id']=0;
                        $data_array[$data_i]['new_id']=$id;
                    }else{

                        $new=[];
                        $new['company_id']=$companyID;
                        $new['workspace_id']=$workSpaceID;
                        $new['user_id']=$user->id;
                        $new['staff_id']=0;
                        $new['order_no']=$order_no;
                        $new['style_no']=$style_no;
                        $new['buyer_id']=($data[$i][2]!='' && $data[$i][2]!='null')?MysqlUserDB::get_buyer_id(trim($data[$i][2]),$companyID,$workSpaceID,$user->id):0;
                        $new['pcu_id']=($data[$i][3]!='' && $data[$i][3]!='null')?MysqlUserDB::get_pcu_id(trim($data[$i][3]),$companyID,$workSpaceID,$user->id):0;
                        $new['factory_id']=($data[$i][4]!='' && $data[$i][4]!='null')?MysqlUserDB::get_factory_id(trim($data[$i][4]),$companyID,$workSpaceID,$user->id):0;
                        $new['fabric_id']=($data[$i][5]!='' && $data[$i][5]!='null')?MysqlUserDB::get_fabric_id(trim($data[$i][5]),$companyID,$workSpaceID,$user->id):0;
                        $new['category_id']=($data[$i][6]!='' && $data[$i][6]!='null')?MysqlUserDB::get_category_id(trim($data[$i][6]),$companyID,$workSpaceID,$user->id):0;
                        $new['article_id']=($data[$i][7]!='' && $data[$i][7]!='null')?MysqlUserDB::get_article_id(trim($data[$i][7]),$companyID,$workSpaceID,$user->id):0;
                        $new['inquiry_date']=($data[$i][8]!='' && $data[$i][8]!='null')?date('Y-m-d',(($data[$i][8] - 25569) * 86400)):NULL;
                        $new['order_price']=($data[$i][9]!='' && $data[$i][9]!='null')?(float)$data[$i][9]:0;
                        $new['income_terms']=($data[$i][10]!='' && $data[$i][10]!='null')?MysqlUserDB::get_incometerms_id(trim($data[$i][10])):0;
                        $new['total_quantity']=($data[$i][11]!='' && $data[$i][11]!='null')?(int)$data[$i][11]:0;
                        $new['units']=($data[$i][12]!='' && $data[$i][12]!='null')?MysqlUserDB::get_unit_id(trim($data[$i][12]),$companyID,$workSpaceID,$user->id):0;
                        $new['no_of_deliverys']=($data[$i][13]!='' && $data[$i][13]!='null')?(int)$data[$i][13]:0;
                        $new['quantity_wise']='SKU-Wise';
                        $new['tolerance_volume']=($data[$i][14]!='' && $data[$i][14]!='null' && (int)$data[$i][14]>0)?(int)$data[$i][14]:0;
                        $new['tolerance_perc']=($data[$i][15]!='' && $data[$i][15]!='null' && (int)$data[$i][15]>0)?(float)$data[$i][15]:0;
                        $new['cutting_start_date']=($data[$i][16]!='' && $data[$i][16]!='null')?date('Y-m-d',(($data[$i][16] - 25569) * 86400)):NULL;
                        $new['cutting_end_date']=($data[$i][17]!='' && $data[$i][17]!='null')?date('Y-m-d',(($data[$i][17] - 25569) * 86400)):NULL;
                        $new['cutting_accomplished_date']=($data[$i][18]!='' && $data[$i][18]!='null')?date('Y-m-d',($data[$i][18] - 25569) * 86400):NULL;
                        $new['sewing_start_date']=($data[$i][19]!='' && $data[$i][19]!='null')?date('Y-m-d',(($data[$i][19] - 25569) * 86400)):NULL;
                        $new['sewing_end_date']=($data[$i][20]!='' && $data[$i][20]!='null')?date('Y-m-d',(($data[$i][20] - 25569) * 86400)):NULL;
                        $new['sewing_accomplished_date']=($data[$i][21]!='' && $data[$i][21]!='null')?date('Y-m-d',(($data[$i][21] - 25569) * 86400)):NULL;
                        $new['packing_start_date']=($data[$i][22]!='' && $data[$i][22]!='null')?date('Y-m-d',(($data[$i][22] - 25569) * 86400)):NULL;
                        $new['packing_end_date']=($data[$i][23]!='' && $data[$i][23]!='null')?date('Y-m-d',(($data[$i][23] - 25569) * 86400)):NULL;
                        $new['packing_accomplished_date']=($data[$i][24]!='' && $data[$i][24]!='null')?date('Y-m-d',(($data[$i][24] - 25569) * 86400)):NULL;
                        $new['ref_img']='0';
                        $new['cut_weekoffs']='0';
                        $new['sew_weekoffs']='0';
                        $new['pack_weekoffs']='0';
                        $new['usual_weekoff']='0';
                        $new['currency_type']=($data[$i][25]!='' && $data[$i][25]!='null')?MysqlUserDB::get_currency_id(trim($data[$i][25])):0;
                        $new['order_task_template']=($data[$i][26]!='' && $data[$i][26]!='null')?MysqlUserDB::get_template_id(trim($data[$i][26]),$companyID,$workSpaceID):1;
                        $new['task_feeded']='0';
                        $new['pending_tasks']='0';
                        $new['cutting_completion']=strtolower(trim($data[$i][27]))=='yes'? 1 : 0;
                        $new['sewing_completion']=strtolower(trim($data[$i][28]))=='yes'? 1 : 0;
                        $new['packing_completion']=strtolower(trim($data[$i][29]))=='yes'? 1 : 0;
                        $new['is_tolerance_req']=strtolower(trim($data[$i][30]))=='yes'? 1 : 0;
                        $new['step_level']='6';
                        $new['status']='1';
                        $new['delivery_date']=($data[$i][31]!='' && $data[$i][31]!='null')?date('Y-m-d',(($data[$i][31] - 25569) * 86400)):NULL;
                        $new['completed_on']=($data[$i][32]!='' && $data[$i][32]!='null')?date('Y-m-d',(($data[$i][32] - 25569) * 86400)):NULL;
                        $new['created_at']=date('Y-m-d H:i:s');
                        $new['updated_at']=date('Y-m-d H:i:s');
                        //dd($new);
                        DB::table("orders")->insert($new);
                        $id = DB::getPdo()->lastInsertId();
                        if($new['delivery_date']!='' && $new['delivery_date']!=null && $new['delivery_date']!='null'){
                            $del_arr['order_id'] = $id;
                            $del_arr['company_id'] = $companyID;
                            $del_arr['workspace_id'] = $workSpaceID;
                            $del_arr['delivery_date'] = $new['delivery_date'];
                            $del_arr['total_delivered_quantity'] = 0;
                            $del_arr['is_delivered'] = 0;
                            $del_arr['created_at']=date('Y-m-d H:i:s');
                            $del_arr['updated_at']=date('Y-m-d H:i:s');
                            //dd($del_arr);
                            DB::table("multiple_delivery_dates")->insert($del_arr);
                        }

                        $data_array[$data_i]['old_id']=1;
                        $data_array[$data_i]['new_id']=(int)$id;
                    }
                }


            }
        }else {
            return response()->json(["status_code"=>401,"error"=>'Invalid file']);
        }
        if(!empty($logArr)){
            $logArr['ip_address'] = json_encode($_SERVER['REMOTE_ADDR']);
            $logArr['data'] = json_encode(array_values($data_array));
            $logArr['order_id'] = 0;
            $logArr['created_at'] = date('Y-m-d H:i:s');
            DB::table("excel_import_logs")->insert($logArr);
        }
        return response()->json(["status_code"=>200,"status" =>"Success","message"=>"Successfully Updated","data"=>$data_array]);
    }

    public function importOrdersSKU(Request $request){
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'file'=>'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $workSpaceID=$request->workspace_id;
        $companyID=$request->company_id;
        $file = $request->file('file');
        /*Current company user details */
        $user= DB::table("users")->where('company_id','=',$companyID)->first();
        $data_array=$logArr=[];$data_i=0;
        $logArr['company_id']=$companyID;
        $logArr['workspace_id']=$workSpaceID;
        $logArr['table_name']='order_sku';
        if(stristr($file->getClientOriginalName(),'.xls')){
            $datas = Excel::toArray(new ImportXL,request()->file('file'));
            $data=json_encode($datas);
            $data = str_replace('[[','',$data);
            $data = str_replace(']]','',$data);
            $data = str_replace('],',']],',$data);
            $data = explode('],',$data);
            //echo count($data); exit;

            for($i=1;$i<count($data);$i++){
                $data[$i] = str_replace('"','',$data[$i]);
                $data[$i] = str_replace('[','',$data[$i]);
                $data[$i] = str_replace(']','',$data[$i]);
                $data[$i] = explode(',',$data[$i]);
                $data_i=$i;
                $order_no = trim($data[$i][0]);
                $style_no = trim($data[$i][1]);
                $color = trim($data[$i][2]);
                $size = trim($data[$i][3]);
                $size_category = trim($data[$i][4]);
                $qty = (int)trim($data[$i][5]);

                $order_id = MysqlUserDB::get_order_id($order_no,$style_no,$companyID,$workSpaceID);
                $color_id = MysqlUserDB::get_color_id($color,$companyID,$workSpaceID);
                $size_id  = MysqlUserDB::get_size_id($size,$size_category,$companyID,$workSpaceID);
                if($order_id!=0 && $color_id!=0 && $size_id!=0 && $qty > 0){
                    $whereCondition=[
                        ['company_id','=',$companyID],
                        ['workspace_id','=',$workSpaceID],
                        ['order_id','=',$order_id],
                        ['sku_color_id','=',$color_id],
                        ['sku_size_id','=',$size_id]
                    ];
                    $id= DB::table("order_sku")->where($whereCondition)->pluck('id')->first();
                    if($id!='' && $id!=NULL){
                        $sku_quantity = ['sku_quantity'=>$qty];
                       // dd($sku_quantity);
                        DB::table("order_sku")->where($whereCondition)->update($sku_quantity);
                        $data_array[$data_i]['old_id']=0;
                        $data_array[$data_i]['new_id']=$id;
                    }else{
                        $new=[];
                        $new['company_id']=$companyID;
                        $new['workspace_id']=$workSpaceID;
                        $new['user_id']=$user->id;
                        $new['sku_color_id']=$color_id;
                        $new['sku_size_id']=$size_id;
                        $new['order_id']=$order_id;
                        $new['sku_quantity']=$qty;
                        $new['created_at']=date('Y-m-d H:i:s');
                        $new['updated_at']=date('Y-m-d H:i:s');
                        //dd($new);
                        DB::table("order_sku")->insert($new);
                        $id = DB::getPdo()->lastInsertId();
                        $data_array[$data_i]['old_id']=1;
                        $data_array[$data_i]['new_id']=(int)$id;
                    }
                }


            }
        }else {
            return response()->json(["status_code"=>401,"error"=>'Invalid file']);
        }
        if(!empty($logArr)){
            $logArr['ip_address'] = json_encode($_SERVER['REMOTE_ADDR']);
            $logArr['data'] = json_encode(array_values($data_array));
            $logArr['order_id'] = 0;
            $logArr['created_at'] = date('Y-m-d H:i:s');

            DB::table("excel_import_logs")->insert($logArr);
        }
        return response()->json(["status_code"=>200,"status" =>"Success","message"=>"Successfully Updated","data"=>$data_array]);
    }

    public function importOrdersContacts(Request $request){
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'file'=>'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $workSpaceID=$request->workspace_id;
        $companyID=$request->company_id;
        $file = $request->file('file');
        /*Current company user details */
        $user= DB::table("users")->where('company_id','=',$companyID)->first();
        $data_array=$logArr=[];$data_i=0;
        $logArr['company_id']=$companyID;
        $logArr['workspace_id']=$workSpaceID;
        $logArr['table_name']='order_contacts';
        if(stristr($file->getClientOriginalName(),'.xls')){
            $datas = Excel::toArray(new ImportXL,request()->file('file'));
            $data=json_encode($datas);
            $data = str_replace('[[','',$data);
            $data = str_replace(']]','',$data);
            $data = str_replace('],',']],',$data);
            $data = explode('],',$data);
            //echo count($data); exit;

            for($i=1;$i<count($data);$i++){
                $data[$i] = str_replace('"','',$data[$i]);
                $data[$i] = str_replace('[','',$data[$i]);
                $data[$i] = str_replace(']','',$data[$i]);
                $data[$i] = explode(',',$data[$i]);
                $data_i=$i;
                $order_no = trim($data[$i][0]);
                $style_no = trim($data[$i][1]);
                $first_name = trim($data[$i][2]);
                $email = trim($data[$i][4]);

                $order_id = MysqlUserDB::get_order_id($order_no,$style_no,$companyID,$workSpaceID);
                $staff_id = MysqlUserDB::get_staff_id($first_name,$email,$companyID,$workSpaceID);
                if($order_id!=0 && $staff_id!=0 ){
                    $whereCondition=[
                        ['company_id','=',$companyID],
                        ['workspace_id','=',$workSpaceID],
                        ['order_id','=',$order_id],
                        ['staff_id','=',$staff_id]
                    ];
                    $id= DB::table("order_contacts")->where($whereCondition)->pluck('id')->first();
                    if($id!='' && $id!=NULL){
                        $data_array[$data_i]['old_id']=0;
                        $data_array[$data_i]['new_id']=$id;
                    }else{
                        $new=[];
                        $new['company_id']=$companyID;
                        $new['workspace_id']=$workSpaceID;
                        $new['user_id']=$user->id;
                        $new['staff_id']=$staff_id;
                        $new['order_id']=$order_id;
                        $new['status']='1';
                        $new['created_at']=date('Y-m-d H:i:s');
                        $new['updated_at']=date('Y-m-d H:i:s');
                        //dd($new);
                        DB::table("order_contacts")->insert($new);
                        $id = DB::getPdo()->lastInsertId();
                        $data_array[$data_i]['old_id']=1;
                        $data_array[$data_i]['new_id']=(int)$id;
                    }
                }
            }
        }else {
            return response()->json(["status_code"=>401,"error"=>'Invalid file']);
        }
        if(!empty($logArr)){
            $logArr['ip_address'] = json_encode($_SERVER['REMOTE_ADDR']);
            $logArr['data'] = json_encode(array_values($data_array));
            $logArr['order_id'] = 0;
            $logArr['created_at'] = date('Y-m-d H:i:s');

            DB::table("excel_import_logs")->insert($logArr);
        }
        return response()->json(["status_code"=>200,"status" =>"Success","message"=>"Successfully Updated","data"=>$data_array]);
    }

    public function importOrdersTemplate(Request $request){
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'file'=>'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $workSpaceID=$request->workspace_id;
        $companyID=$request->company_id;
        $file = $request->file('file');
        /*Current company user details */
        $user= DB::table("users")->where('company_id','=',$companyID)->first();
        $data_array=$logArr=[];$data_i=0;
        $logArr['company_id']=$companyID;
        $logArr['workspace_id']=$workSpaceID;
        $logArr['table_name']='order_task_template';
        if(stristr($file->getClientOriginalName(),'.xls')){
            $datas = Excel::toArray(new ImportXL,request()->file('file'));
            $data=json_encode($datas);
            $data = str_replace('[[','',$data);
            $data = str_replace(']]','',$data);
            $data = str_replace('],',']],',$data);
            $data = explode('],',$data);
            //echo count($data); exit;

            for($i=1;$i<2;$i++){
                $data[$i] = str_replace('"','',$data[$i]);
                $data[$i] = str_replace('[','',$data[$i]);
                $data[$i] = str_replace(']','',$data[$i]);
                $data[$i] = explode(',',$data[$i]);
                $data_i=$i;
                $order_no = trim($data[$i][0]);
                $style_no = trim($data[$i][1]);
                $template_name = trim($data[$i][2]);

                $order_id = MysqlUserDB::get_order_id($order_no,$style_no,$companyID,$workSpaceID);
                if($order_id!=0 && $template_name!='' && $template_name!='null' ){
                    $whereCondition=[
                        ['company_id','=',$companyID],
                        ['workspace_id','=',$workSpaceID],
                       // ['order_id','=',$order_id],
                        ['template_name','=',$template_name]
                    ];
                    $id= DB::table("order_task_template")->where($whereCondition)->pluck('id')->first();
                    if($id!='' && $id!=NULL){
                        $data_array[$data_i]['old_id']=0;
                        $data_array[$data_i]['new_id']=$id;
                    }else{
                        $new=[];
                        $new['company_id']=$companyID;
                        $new['workspace_id']=$workSpaceID;
                        $new['user_id']=$user->id;
                        $new['staff_id']=0;
                        $new['order_id']=$order_id;
                        $new['template_name']=$template_name;
                        $new['status']='1';
                        $new['is_default']='1';
                        $new['created_by']=$user->id;
                        $new['created_user_type']='User';
                        $new['created_at']=date('Y-m-d H:i:s');
                        $new['updated_at']=date('Y-m-d H:i:s');
                        //dd($new);
                        DB::table("order_task_template")->insert($new);
                        $id = DB::getPdo()->lastInsertId();
                        $data_array[$data_i]['old_id']=1;
                        $data_array[$data_i]['new_id']=(int)$id;
                    }
                }
            }
            $temp_arr = [];
            $j=$k=0;
            for($i=4;$i<count($data);$i++){
                $data[$i] = str_replace('"','',$data[$i]);
                $data[$i] = str_replace('[','',$data[$i]);
                $data[$i] = str_replace(']','',$data[$i]);
                $data[$i] = explode(',',$data[$i]);
                $data_i=$i;
                $task_title = trim($data[$i][0]);
                $task_subtitle = trim($data[$i][1]);
                if($i==4){
                    $key = $task_title;
                }
                if($task_title!='' && $task_title!='null' && $task_subtitle!='' && $task_subtitle!='null')
                if($key==$task_title){
                    $temp_arr[$j]['task_title'] = $task_title;
                    $temp_arr[$j]['task_subtitles'][$k] = $task_subtitle;
                    $k++;
                }else{
                    $j++;$k=0;
                    $key = $task_title;
                    $temp_arr[$j]['task_title'] = $task_title;
                    $temp_arr[$j]['task_subtitles'][$k] = $task_subtitle;
                    $k++;
                }
            }

            if(!empty($temp_arr)){
                DB::table("order_task_template")->where($whereCondition)->update(array('task_template_structure'=>json_encode($temp_arr)));
                DB::table("orders")->where('id',$order_id)->update(array('order_task_template'=>$id));
            }
        }else {
            return response()->json(["status_code"=>401,"error"=>'Invalid file']);
        }
        if(!empty($logArr)){
            $logArr['ip_address'] = json_encode($_SERVER['REMOTE_ADDR']);
            $logArr['data'] = json_encode(array_values($data_array));
            $logArr['order_id'] = 0;
            $logArr['created_at'] = date('Y-m-d H:i:s');

            DB::table("excel_import_logs")->insert($logArr);
        }
        return response()->json(["status_code"=>200,"status" =>"Success","message"=>"Successfully Updated","data"=>$data_array]);
    }

    public function importTemplateData(Request $request){
        $validator = Validator::make($request->all(), [
            'company_id' => 'required',
            'workspace_id' => 'required',
            'file'=>'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        $workSpaceID=$request->workspace_id;
        $companyID=$request->company_id;
        $file = $request->file('file');
        /*Current company user details */
        $user= DB::table("users")->where('company_id','=',$companyID)->first();
        $data_array=$logArr=[];$data_i=0;
        $logArr['company_id']=$companyID;
        $logArr['workspace_id']=$workSpaceID;
        $logArr['table_name']='order_task_data';
        if(stristr($file->getClientOriginalName(),'.xls')){
            $datas = Excel::toArray(new ImportXL,request()->file('file'));
            $data=json_encode($datas);
            $data = str_replace('[[','',$data);
            $data = str_replace(']]','',$data);
            $data = str_replace('],',']],',$data);
            $data = explode('],',$data);
            //echo count($data); exit;
            $template_id = 0;
            for($i=1;$i<2;$i++){
                $data[$i] = str_replace('"','',$data[$i]);
                $data[$i] = str_replace('[','',$data[$i]);
                $data[$i] = str_replace(']','',$data[$i]);
                $data[$i] = explode(',',$data[$i]);
                $data_i=$i;
                $order_no = trim($data[$i][0]);
                $style_no = trim($data[$i][1]);
                $template_name = trim($data[$i][2]);

                $order_id = MysqlUserDB::get_order_id($order_no,$style_no,$companyID,$workSpaceID);

                if($order_id!=0 && $template_name!='' && $template_name!='null' ){
                    $whereCondition=[
                        ['company_id','=',$companyID],
                        ['workspace_id','=',$workSpaceID],
                       // ['order_id','=',$order_id],
                        ['template_name','=',$template_name]
                    ];
                    $template_id= DB::table("order_task_template")->where($whereCondition)->pluck('id')->first();
                }
            }
            $temp_arr = [];
            $j=$k=0;
            for($i=4;$i<count($data);$i++){
                $data[$i] = str_replace('"','',$data[$i]);
                $data[$i] = str_replace('[','',$data[$i]);
                $data[$i] = str_replace(']','',$data[$i]);
                $data[$i] = explode(',',$data[$i]);
                $data_i=$i;
                $task_title = trim($data[$i][0]);
                $task_subtitle = trim($data[$i][1]);
                $start_date=($data[$i][2]!='' && $data[$i][2]!='null')?date('Y-m-d',(($data[$i][2] - 25569) * 86400)):NULL;
                $end_date=($data[$i][3]!='' && $data[$i][3]!='null')?date('Y-m-d',(($data[$i][3] - 25569) * 86400)):NULL;
                $task_accomplished_date=($data[$i][4]!='' && $data[$i][4]!='null')?date('Y-m-d',(($data[$i][4] - 25569) * 86400)):NULL;
                $first_name=trim($data[$i][5]);
                $email=trim($data[$i][6]);
                $staff_id = MysqlUserDB::get_staff_id($first_name,$email,$companyID,$workSpaceID);
                if($staff_id!=0 && $task_title!='' && $task_title!='null' && $task_subtitle!='' && $task_subtitle!='null' && $start_date!=NULL && $end_date!=NULL && (int)$template_id > 0){
                    $whereCondition=[
                        ['company_id','=',$companyID],
                        ['workspace_id','=',$workSpaceID],
                        ['order_id','=',$order_id],
                        ['task_pic','=',$staff_id],
                        ['cat_title','=',$task_title],
                        ['task_title','=',$task_subtitle],
                    ];
                    $id= DB::table("order_task_data")->where($whereCondition)->pluck('id')->first();
                    if($id!='' && $id!=NULL){
                        $data_array[$data_i]['old_id']=0;
                        $data_array[$data_i]['new_id']=$id;
                    }else{
                        $new=[];
                        $new['company_id']=$companyID;
                        $new['workspace_id']=$workSpaceID;
                        $new['user_id']=$user->id;
                        $new['staff_id']=0;
                        $new['order_id']=$order_id;
                        $new['template_id']=$template_id;
                        $new['cat_title']=$task_title;
                        $new['task_title']=$task_subtitle;
                        $new['task_schedule_start_date']=$start_date;
                        $new['task_schedule_end_date']=$end_date;
                        $new['task_accomplished_date']=$task_accomplished_date;
                        $new['task_pic']=$staff_id;
                        $new['created_by']=$user->id;
                        $new['created_user_type']='User';
                        $new['created_at']=date('Y-m-d H:i:s');
                        $new['updated_at']=date('Y-m-d H:i:s');
                        //dd($new);
                        DB::table("order_task_data")->insert($new);
                        $id = DB::getPdo()->lastInsertId();
                        $data_array[$data_i]['old_id']=1;
                        $data_array[$data_i]['new_id']=(int)$id;
                    }
                }
            }


        }else {
            return response()->json(["status_code"=>401,"error"=>'Invalid file']);
        }
        if(!empty($logArr)){
            $logArr['ip_address'] = json_encode($_SERVER['REMOTE_ADDR']);
            $logArr['data'] = json_encode(array_values($data_array));
            $logArr['order_id'] = 0;
            $logArr['created_at'] = date('Y-m-d H:i:s');

            DB::table("excel_import_logs")->insert($logArr);
        }
        return response()->json(["status_code"=>200,"status" =>"Success","message"=>"Successfully Updated","data"=>$data_array]);
    }

    public static function get_role_id($role,$company_id,$workspace_id,$user_id){
        $id= DB::table("roles")->Where(function($query) use ($role,$company_id,$workspace_id,$user_id)
                                {
                                    $query->where('name','=',$role)
                                    ->where('company_id','=',$company_id)
                                    ->where('workspace_id','=',$workspace_id);
                                })
                                ->orWhere(function($query) use ($role)
                                {
                                    $query->where('name','=',$role)
                                        ->Where("is_default",'=','0');
                                })
                                ->pluck('id')->first();
        if((int)$id > 0){
            return $id;
        }else{
            $new=[];
            $new['name']=ucfirst($role);
            $new['company_id']=$company_id;
            $new['workspace_id']=$workspace_id;
            $new['user_id']=$user_id;
            $new['staff_id']=0;
            $new['is_default']='1';
            $new['status']='1';
            $new['order_sequence']='0';
            $new['created_by']=$user_id;
            $new['created_at']=date('Y-m-d H:i:s');
            $new['updated_at']=date('Y-m-d H:i:s');
            DB::table("roles")->insert($new);
            return DB::getPdo()->lastInsertId();
        }
    }
    public static function get_buyer_id($name,$company_id,$workspace_id,$user_id){

        $id= DB::table("order_buyer")->where('name','=',$name)
                                ->where('company_id','=',$company_id)
                                ->where('workspace_id','=',$workspace_id)
                                ->pluck('id')->first();
        if((int)$id > 0){
            return $id;
        }else{
            $new=[];
            $new['name']=ucfirst($name);
            $new['company_id']=$company_id;
            $new['workspace_id']=$workspace_id;
            $new['user_id']=$user_id;
            $new['staff_id']=0;
            $new['created_by']=$user_id;
            $new['created_at']=date('Y-m-d H:i:s');
            $new['updated_at']=date('Y-m-d H:i:s');
            DB::table("order_buyer")->insert($new);
            return DB::getPdo()->lastInsertId();
        }
    }
    public static function get_pcu_id($name,$company_id,$workspace_id,$user_id){

        $id= DB::table("order_pcu")->where('name','=',$name)
                                ->where('company_id','=',$company_id)
                                ->where('workspace_id','=',$workspace_id)
                                ->pluck('id')->first();
        if((int)$id > 0){
            return $id;
        }else{
            $new=[];
            $new['name']=ucfirst($name);
            $new['company_id']=$company_id;
            $new['workspace_id']=$workspace_id;
            $new['user_id']=$user_id;
            $new['staff_id']=0;
            $new['created_by']=$user_id;
            $new['created_at']=date('Y-m-d H:i:s');
            $new['updated_at']=date('Y-m-d H:i:s');
            DB::table("order_pcu")->insert($new);
            return DB::getPdo()->lastInsertId();
        }
    }
    public static function get_factory_id($name,$company_id,$workspace_id,$user_id){

        $id= DB::table("order_factory")->where('name','=',$name)
                                ->where('company_id','=',$company_id)
                                ->where('workspace_id','=',$workspace_id)
                                ->pluck('id')->first();
        if((int)$id > 0){
            return $id;
        }else{
            $new=[];
            $new['name']=ucfirst($name);
            $new['company_id']=$company_id;
            $new['workspace_id']=$workspace_id;
            $new['user_id']=$user_id;
            $new['staff_id']=0;
            $new['created_by']=$user_id;
            $new['created_at']=date('Y-m-d H:i:s');
            $new['updated_at']=date('Y-m-d H:i:s');
            DB::table("order_factory")->insert($new);
            return DB::getPdo()->lastInsertId();
        }
    }
    public static function get_fabric_id($name,$company_id,$workspace_id,$user_id){

        $id= DB::table("fabric_type")->Where(function($query) use ($name,$company_id,$workspace_id,$user_id)
                                {
                                    $query->where('name','=',$name)
                                    ->where('company_id','=',$company_id)
                                    ->where('workspace_id','=',$workspace_id);
                                })
                                ->orWhere(function($query) use ($name)
                                {
                                    $query->where('name','=',$name)
                                        ->Where("is_default",'=','0');
                                })
                                ->pluck('id')->first();
        if((int)$id > 0){
            return $id;
        }else{
            $new=[];
            $new['name']=ucfirst($name);
            $new['company_id']=$company_id;
            $new['workspace_id']=$workspace_id;
            $new['user_id']=$user_id;
            $new['staff_id']=0;
            $new['is_default']='1';
            $new['status']='1';
            $new['created_by']=$user_id;
            $new['created_at']=date('Y-m-d H:i:s');
            $new['updated_at']=date('Y-m-d H:i:s');
            DB::table("fabric_type")->insert($new);
            return DB::getPdo()->lastInsertId();
        }
    }
    public static function get_category_id($name,$company_id,$workspace_id,$user_id){

        $id= DB::table("order_category")->Where(function($query) use ($name,$company_id,$workspace_id,$user_id)
                                {
                                    $query->where('name','=',$name)
                                    ->where('company_id','=',$company_id)
                                    ->where('workspace_id','=',$workspace_id);
                                })
                                ->orWhere(function($query) use ($name)
                                {
                                    $query->where('name','=',$name)
                                        ->Where("is_default",'=','0');
                                })
                                ->pluck('id')->first();
        if((int)$id > 0){
            return $id;
        }else{
            $new=[];
            $new['name']=ucfirst($name);
            $new['company_id']=$company_id;
            $new['workspace_id']=$workspace_id;
            $new['user_id']=$user_id;
            $new['staff_id']=0;
            $new['is_default']='1';
            $new['status']='1';
            $new['created_by']=$user_id;
            $new['created_at']=date('Y-m-d H:i:s');
            $new['updated_at']=date('Y-m-d H:i:s');
            DB::table("order_category")->insert($new);
            return DB::getPdo()->lastInsertId();
        }
    }
    public static function get_article_id($name,$company_id,$workspace_id,$user_id){

        $id= DB::table("order_article_name")->Where(function($query) use ($name,$company_id,$workspace_id,$user_id)
                                {
                                    $query->where('name','=',$name)
                                    ->where('company_id','=',$company_id)
                                    ->where('workspace_id','=',$workspace_id);
                                })
                                ->orWhere(function($query) use ($name)
                                {
                                    $query->where('name','=',$name)
                                        ->Where("is_default",'=','0');
                                })
                                ->pluck('id')->first();
        if((int)$id > 0){
            return $id;
        }else{
            $new=[];
            $new['name']=ucfirst($name);
            $new['company_id']=$company_id;
            $new['workspace_id']=$workspace_id;
            $new['user_id']=$user_id;
            $new['staff_id']=0;
            $new['is_default']='1';
            $new['status']='1';
            $new['created_by']=$user_id;
            $new['created_at']=date('Y-m-d H:i:s');
            $new['updated_at']=date('Y-m-d H:i:s');
            DB::table("order_article_name")->insert($new);
            return DB::getPdo()->lastInsertId();
        }
    }
    public static function get_incometerms_id($name){

        $id= DB::table("income_terms")->where('name','=',$name)
                                ->pluck('id')->first();
        if((int)$id > 0){
            return $id;
        }else{
            return 0;
        }
    }
    public static function get_unit_id($name,$company_id,$workspace_id,$user_id){

        $id= DB::table("order_units")->Where(function($query) use ($name,$company_id,$workspace_id,$user_id)
                                {
                                    $query->where('name','=',$name)
                                    ->where('company_id','=',$company_id)
                                    ->where('workspace_id','=',$workspace_id)
                                    ->where('bom_unit','=','1');
                                })
                                ->orWhere(function($query) use ($name)
                                {
                                    $query->where('name','=',$name)
                                        ->where('bom_unit','=','1')
                                        ->Where("is_default",'=','0');
                                })
                                ->pluck('id')->first();
        if((int)$id > 0){
            return $id;
        }else{
            $new=[];
            $new['name']=ucfirst($name);
            $new['company_id']=$company_id;
            $new['workspace_id']=$workspace_id;
            $new['user_id']=$user_id;
            $new['staff_id']=0;
            $new['bom_unit']='1';
            $new['is_default']='1';
            $new['status']='1';
            $new['created_at']=date('Y-m-d H:i:s');
            $new['updated_at']=date('Y-m-d H:i:s');
            DB::table("order_units")->insert($new);
            return DB::getPdo()->lastInsertId();
        }
    }
    public static function get_currency_id($name){

        $id= DB::table("currencies")->where('name','=',$name)
                                ->pluck('id')->first();
        if((int)$id > 0){
            return $id;
        }else{
            return 0;
        }
    }
    public static function get_template_id($name,$company_id,$workspace_id){

        $id= DB::table("order_task_template")->Where(function($query) use ($name,$company_id,$workspace_id)
                                {
                                    $query->where('template_name','=',$name)
                                    ->where('company_id','=',$company_id)
                                    ->where('workspace_id','=',$workspace_id);
                                })
                                ->orWhere(function($query) use ($name)
                                {
                                    $query->where('template_name','=',$name)
                                        ->Where("is_default",'=','0');
                                })
                                ->pluck('id')->first();
        if((int)$id > 0){
            return $id;
        }else{
            return 1;
        }
    }
    public static function get_order_id($order_no,$style_no,$companyID,$workSpaceID){
        $id= DB::table("orders")->where('order_no','=',$order_no)
                ->where('style_no','=',$style_no)
                ->where('company_id','=',$companyID)
                ->where('workspace_id','=',$workSpaceID)
                ->pluck('id')->first();
        if((int)$id > 0){
            return $id;
        }else{
            return 0;
        }
    }
    public static function get_color_id($color,$companyID,$workSpaceID){
        $id= DB::table("color")->Where(function($query) use ($color,$companyID,$workSpaceID)
                {
                    $query->where('company_id','=',$companyID)
                    ->where('workspace_id','=',$workSpaceID)
                    ->where('name','=',$color);
                })
                ->orWhere(function($query) use ($color)
                {
                    $query->where('name','=',$color)
                        ->Where("is_default",'=','0');
                })
                ->pluck('id')->first();
        if((int)$id > 0){
            return $id;
        }else{
            return 0;
        }
    }
    public static function get_size_id($size,$size_category,$companyID,$workSpaceID){
        $id= DB::table("size")->Where(function($query) use ($size,$size_category,$companyID,$workSpaceID)
                {
                    $query->where('company_id','=',$companyID)
                    ->where('workspace_id','=',$workSpaceID)
                    ->where('name','=',$size)
                    ->where('category','=',$size_category);
                })
                ->orWhere(function($query) use ($size,$size_category)
                {
                    $query->where('name','=',$size)
                        ->where('category','=',$size_category)
                        ->Where("is_default",'=','0');
                })
                ->pluck('id')->first();
        if((int)$id > 0){
            return $id;
        }else{
            return 0;
        }
    }
    public static function get_staff_id($first_name,$email,$companyID,$workSpaceID){
        $id= DB::table("staff")->where('first_name','=',$first_name)
                ->where('email','=',$email)
                ->where('company_id','=',$companyID)
                ->where('workspace_id','=',$workSpaceID)
                ->pluck('id')->first();
        if((int)$id > 0){
            return $id;
        }else{
            return 0;
        }
    }

}
