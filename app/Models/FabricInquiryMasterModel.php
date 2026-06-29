<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class FabricInquiryMasterModel extends Model
{
    use HasFactory;

    protected $table = 'fabric_master';

    /* Get Inquiry Master Details */
    public static function inquiryMasterDetails($whereConditions,$request){
        $inquiryMaster = FabricInquiryMasterModel::where($whereConditions)
        ->join('fabric_inquiry','fabric_inquiry.reference_id','fabric_master.reference_id')
        ->join('company_settings','fabric_inquiry.company_id','company_settings.id')
        ->select('fabric_master.id as id','type','content',DB::raw('DATE_FORMAT(fabric_master.created_at,"%Y-%m-%d") as created_date'),'fabric_inquiry.id as inquiry_id','company_settings.company_name')
        ->orderBy('fabric_master.created_at','DESC')
        ->paginate(20, ['*'], 'page', $request->page);
        // ->get();

        $type=FabricInquiryMasterModel::select('type')->distinct()->get();

        $data['data'] = $inquiryMaster;
        $data['type'] = $type;

        return $data;
    }

    /* Add Defaults Msater Details */
    public static function inquiryDefault($request){
        DB::beginTransaction();
        try{
            $inquiryMaster = FabricInquiryMasterModel::where('id',$request->id)->first();
            $inquiryMaster->reference_id = 0;
            $inquiryMaster->save();
        }catch(Exception $e){
            dd($e);
            DB::rollBack();
            throw new InvalidArgumentException("Something Went Wrong");
        }
        DB::commit();
    }
}
