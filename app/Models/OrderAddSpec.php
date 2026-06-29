<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class OrderAddSpec extends Model
{
    use HasFactory;
    protected $table = 'order_add_spec';

    static function deleteFile($request){

        $whereConditions1=[
            ['id','=',$request->id]
        ];
        if(isset($request->type) && $request->type=='po'){
            $filepath = POMedia::where($whereConditions1)->select('filepath','filesize','company_id')->limit(1)->get();
            if(!empty($filepath) && isset($filepath[0]['filepath'])){
                if($filepath[0]['company_id'] > 0){
                    $companyDetails = CompanySettings::where('id',$filepath[0]['company_id'])->first();
                    $storageUsed = $companyDetails->storage_used*1024*1024;
                    $storageToBeFreed = $filepath[0]['filesize'];
                    $freedStorage = ($storageUsed - $storageToBeFreed)/(1024*1024);
                    $companyDetails->storage_used = $freedStorage;
                    $companyDetails->save();
                }
                $file = $filepath[0]['filepath'];
                Storage::disk('s3')->delete($file);
            }
            POMedia::where($whereConditions1)->delete();
        }else if(isset($request->type) && $request->type=='techpack'){
            $filepath = TechpackMedia::where($whereConditions1)->select('filepath','filesize','company_id')->limit(1)->get();
            if(!empty($filepath) && isset($filepath[0]['filepath'])){
                if($filepath[0]['company_id'] > 0){
                    $companyDetails = CompanySettings::where('id',$filepath[0]['company_id'])->first();
                    $storageUsed = $companyDetails->storage_used*1024*1024;
                    $storageToBeFreed = $filepath[0]['filesize'];
                    $freedStorage = ($storageUsed - $storageToBeFreed)/(1024*1024);
                    $companyDetails->storage_used = $freedStorage;
                    $companyDetails->save();
                    $companyDetails = CompanySettings::where('id',$filepath[0]['company_id'])->select('storage_used')->first();
                }
                $file = $filepath[0]['filepath'];
                Storage::disk('s3')->delete($file);
            }
            TechpackMedia::where($whereConditions1)->delete();
        }else{
            $filepath = OrderAddSpec::where($whereConditions1)->select('filepath','filesize','company_id')->limit(1)->get();
            if(!empty($filepath) && isset($filepath[0]['filepath'])){
                if($filepath[0]['company_id'] > 0){
                    $companyDetails = CompanySettings::where('id',$filepath[0]['company_id'])->first();
                    $storageUsed = $companyDetails->storage_used*1024*1024;
                    $storageToBeFreed = $filepath[0]['filesize'];
                    $freedStorage = ($storageUsed - $storageToBeFreed)/(1024*1024);
                    $companyDetails->storage_used = $freedStorage;
                    $companyDetails->save();
                }
                $file = $filepath[0]['filepath'];
                Storage::disk('s3')->delete($file);
            }
            OrderAddSpec::where($whereConditions1)->delete();
        }


        // $fileToDelete = OrderAddSpec::where('id',$request->id)->first();
        // $fileToDelete->status="2";
        // $fileToDelete->save();
    }
}
