<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\PlanLogs;
use App\Models\UpdateSkuQuantityLog;
use App\Models\Orderlog;
use App\Models\UpdateSkuQuantity;
use App\Models\UpdateOrderAction;
use App\Models\WeekOff;
use App\Models\UserPreferences;
use Illuminate\Support\Facades\Storage;
use App\Models\WorkspaceLogs;
use App\Models\OrderAddSpec;
use Exception;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use ZanySoft\Zip\Zip;

class Workspace extends Model
{
    use HasFactory;

    protected $table = 'workspace';
    protected $fillable = ['id', 'name', 'user_id', 'company_id', 'workspace_type', 'created_by', 'status', 'created_at', 'updatred_at'];


    public static function getWorkspaceDetails($request)
    {
        $workspace = self::where('company_id', $request->companyId)
        ->leftjoin("company_settings","company_settings.id","workspace.company_id")
        ->select("workspace.*","company_settings.company_name")
        ->get();
        return $workspace;
    }

    public static function destoryWorkSpace($request)
    {
        try {
            PlanLogs::where('workspace_id', $request->workSpaceId)->delete();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::info($e->getMessage());
        }
        try {
            UpdateSkuQuantityLog::where('workspace_id', $request->workSpaceId)->delete();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::info($e->getMessage());
        }
        try {
            Orderlog::where('workspace_id', $request->workSpaceId)->delete();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::info($e->getMessage());
        }
        try {
            UpdateSkuQuantity::where('workspace_id', $request->workSpaceId)->delete();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::info($e->getMessage());
        }

        try {
            UpdateOrderAction::where('workspace_id', $request->workSpaceId)->delete();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::info($e->getMessage());
        }
        try {
            WeekOff::where('workspace_id', $request->workSpaceId)->delete();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::info($e->getMessage());
        }
        try {
            DB::table('role_privileges')->where('workspace_id', $request->workSpaceId)->delete();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::info($e->getMessage());
        }
        try {
            DB::table('roles')->where('workspace_id', $request->workSpaceId)->delete();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::info($e->getMessage());
        }

        try {
            DB::table('reschedule_tasks')->where('workspace_id', $request->workSpaceId)->delete();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::info($e->getMessage());
        }
        try {
            DB::table('order_task_template')->where('workspace_id', $request->workSpaceId)->delete();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::info($e->getMessage());
        }

        try {
            DB::table('order_task_data')->where('workspace_id', $request->workSpaceId)->delete();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::info($e->getMessage());
        }
        try {
            DB::table('order_sku')->where('workspace_id', $request->workSpaceId)->delete();
        } catch (\Exception $e) {
            $error = $e->getMessage();
           Log::info($e->getMessage());
        }
        try {
            DB::table('order_production_data')->where('workspace_id', $request->workSpaceId)->delete();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::info($e->getMessage());
        }
        try {
            DB::table('order_contacts')->where('workspace_id', $request->workSpaceId)->delete();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::info($e->getMessage());
        }
        try {
            DB::table('order_category')->where('workspace_id', $request->workSpaceId)->delete();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::info($e->getMessage());
        }
        try {
            DB::table('order_buyer')->where('workspace_id', $request->workSpaceId)->delete();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::info($e->getMessage());
        }
        try {
            DB::table('order_article_name')->where('workspace_id', $request->workSpaceId)->delete();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::info($e->getMessage());
        }
        try {
            DB::table('order_add_spec')->where('workspace_id', $request->workSpaceId)->delete();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::info($e->getMessage());
        }
        try {
            DB::table('orders')->where('workspace_id', $request->workSpaceId)->delete();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::info($e->getMessage());
        }
        try {
            DB::table('email_schedule_notification')->where('workspace_id', $request->workSpaceId)->delete();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::info($e->getMessage());
        }
        try {
            DB::table('email_configurations')->where('workspace_id', $request->workSpaceId)->delete();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::info($e->getMessage());
        }
        try {
            DB::table('dashboard_settings')->where('workspace_id', $request->workSpaceId)->delete();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::info($e->getMessage());
        }
        try {
            DB::table('dashboard_notification')->where('workspace_id', $request->workSpaceId)->delete();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::info($e->getMessage());
        }
        try {
            DB::table('color')->where('workspace_id', $request->workSpaceId)->delete();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::info($e->getMessage());
        }
        try {
            DB::table('size')->where('workspace_id', $request->workSpaceId)->delete();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::info($e->getMessage());
        }
        try {
            DB::table('workspace_access_user')->where('workspace_id', $request->workSpaceId)->delete();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::info($e->getMessage());
        }
        try {
            DB::table('staff_logs')->where('workspace_id', $request->workSpaceId)->delete();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::info($e->getMessage());
        }


        try {
            DB::table('user_preferences')->where('workspace_id', $request->workSpaceId)->delete();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::info($e->getMessage());
        }
        try {
            DB::table('staff')->where('workspace_id', $request->workSpaceId)->delete();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::info($e->getMessage());
        }
        try{
            // $inquiryIDs = DB::table('inquiry')->where('workspace_id', $request->workSpaceId)->select('id')->get()->toArray();
            $inquiryIDs = DB::table('inquiry')->where('workspace_id', $request->workSpaceId)->pluck('id')->toArray();
            /* Delete Other Inquiry Records */
            if(count($inquiryIDs)>0){
                DB::table('inquiry_factory_feedback')->whereIn('inquiry_id', $inquiryIDs)->delete();
                DB::table('inquiry_factory_response')->whereIn('inquiry_id', $inquiryIDs)->delete();
                DB::table('inquiry_log')->where('workspace_id', $request->workSpaceId)->delete();
                DB::table('inquiry_media')->whereIn('inquiry_id', $inquiryIDs)->delete();
                DB::table('inquiry_sku')->whereIn('inquiry_id', $inquiryIDs)->delete();
                /* Get PO id and delete other records */
                $poIDs = DB::table('inquiry_po')->whereIn('inquiry_id', $inquiryIDs)->pluck('id')->toArray();
                Log::info("Po ID");
                Log::info($poIDs);
                DB::table('inquiry_po_media')->whereIn('po_id', $poIDs)->delete();
                DB::table('inquiry_po_sku')->whereIn('po_id', $poIDs)->delete();
                DB::table('inquiry_po_log')->where('workspace_id', $request->workSpaceId)->delete();
                /* Delete Inquiry */
                DB::table('inquiry')->where('workspace_id', $request->workSpaceId)->delete();
                /* Delete PO */
                DB::table('inquiry_po')->whereIn('inquiry_id', $inquiryIDs)->delete();
            }
        }catch(Exception $e) {
            $error = $e->getMessage();
            Log::info($e->getMessage());
        }
        try{
            // $fabricIDs = DB::table('fabric_inquiry')->where('workspace_id', $request->workSpaceId)->select('id')->get()->toArray();
            $fabricIDs = DB::table('fabric_inquiry')->where('workspace_id', $request->workSpaceId)->pluck('id')->toArray();
            /* Delete Other Fabric Records */
            if(count($fabricIDs)>0){
                DB::table('fabric_supplier_response')->whereIn('inquiry_id', $fabricIDs)->delete();
                DB::table('fabric_inquiry_log')->where('workspace_id', $request->workSpaceId)->delete();
                DB::table('fabric_type')->where('workspace_id', $request->workSpaceId)->delete();
                DB::table('fabric_inquiry')->where('workspace_id', $request->workSpaceId)->delete();
            }
        }catch(Exception $e){
            $error = $e->getMessage();
            Log::info($e->getMessage());
        }
       /* Get workSpace Details*/
        $getUser= DB::table('users')->where('company_id', $request->companyId)->first();
        $getCompany= DB::table('company_settings')->where('id', $request->companyId)->first();
        $getWorkspace= DB::table('workspace')->where('company_id', $request->companyId)->first();
        $getDetails=[];
        $getDetails['company_id']=$request->companyId;
        $getDetails['user_id']=$getUser->id;
        $getDetails['workspace_id']=$getWorkspace->id;
        $getDetails['workspace_name']=$getWorkspace->name;
        $getDetails['workspace_type']=$getWorkspace->workspace_type;
        $getDetails['company_name']=$getCompany->company_name;
        $getDetails['user_name']=$getUser->name;
        $getDetails['user_email']=$getUser->email;
        $getDetails['aws_name']=$getCompany->aws_s3_path;
        $getDetails['created_at']=date("Y-m-d H:i:s");
        $getDetails['updated_at']=date("Y-m-d H:i:s");
        try{
            WorkspaceLogs::insert($getDetails);
        }catch(Exception $e){
            throw new InvalidArgumentException($e->getMessage());
        }

        /* Workspace Delete */
        self::where('id', $request->workSpaceId)->delete();

        try {
            DB::table('users')->where('company_id', $request->companyId)->delete();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::info($e->getMessage());
        }
        try {
            $getServerURL = config('filesystems.disks.s3.url');
            //$compAWS= DB::table('company_settings')->select('aws_s3_path')->where('id', $request->companyId)->first();
            $dirname=$getCompany->aws_s3_path;
            Storage::disk('s3')->deleteDirectory($dirname);
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::info($e->getMessage());
        }
        try {
            DB::table('company_settings')->where('id', $request->companyId)->delete();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::info($e->getMessage());
        }
    }
    public static function downloadS3SelecetdFiles($request){
        $whereConditions=[
            ['workspace_id','=',$request->workSpaceId],
            ['id','=',$request->id]

        ];
        if(isset($request->type) && $request->type=='orderfile'){
            $getFileUrl = OrderAddSpec::where($whereConditions)->select('filepath','orginalfilename')->first();
        }else if(isset($request->type) && $request->type=='po'){
            $getFileUrl = POMedia::where($whereConditions)->select('filepath','orginalfilename')->first();
        }else{
            $getFileUrl = TechpackMedia::where($whereConditions)->select('filepath','orginalfilename')->first();
        }
        //$getServerURL = config('filesystems.disks.s3.url');
        $fullpath = Storage::disk('s3')->temporaryUrl($getFileUrl->filepath, '+15 minutes');
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=" . $getFileUrl->orginalfilename);
        header("Content-Type: application/octet-stream" );
        header('Access-Control-Allow-Origin:*');

        return readfile($fullpath);
    }

    public static function downloadS3Files($request){
        $getCompany= DB::table('company_settings')->where('id', $request->companyId)->first();
        $OrderAddSpecd = OrderAddSpec::where('workspace_id',$request->workSpaceId)->get();

       // $zip = Zip::create('file.zip');
       // $zip->add('/path/to/my/directory', true);
        /*
        $filename=$getCompany->aws_s3_path.'_'.time().'.zip';
      //  $zip->open(public_path('s3backup/'.$filename), ZipArchive::CREATE);
        $getServerURL = config('filesystems.disks.s3.url');


        foreach ($OrderAddSpecd as $OrderAddSpecs){
            $fullpath = $getServerURL.($OrderAddSpecs->filepath);
           $zip->addFromString($OrderAddSpecs->orginalfilename, file_get_contents($fullpath));
        }

        $zip->close();

        header('Content-disposition: attachment; filename=s3-file-download.zip');
        header('Content-type: application/zip');
        readfile(public_path('s3backup/'.$filename));
       */
      }

    /* Get the number of workspaces count */
    public static function getWorkspacesCount(){
        $workspace = Workspace::where("status","!=","3")->count();

        return $workspace;
    }
}
