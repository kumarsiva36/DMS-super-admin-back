<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class InquiryMasterModel extends Model
{
    use HasFactory;

    protected $table = 'inquiry_master';

    /* Get Inquiry Master Details */
    public static function inquiryMasterDetails($whereConditions,$request){
        $inquiryMaster = InquiryMasterModel::where($whereConditions)
        ->join('inquiry','inquiry.media_reference_id','inq_reference_id')
        ->join('company_settings','inquiry.company_id','company_settings.id')
        ->select('inquiry_master.id as id','type','content',DB::raw('DATE_FORMAT(inquiry_master.created_at,"%Y-%m-%d") as created_date'),'inquiry.id as inquiry_id','company_settings.company_name')
        ->orderBy('inquiry_master.created_at','DESC')
        ->paginate(20, ['*'], 'page', $request->page);
        // ->get();

        $type=InquiryMasterModel::select('type')->distinct()->get();

        $data['data'] = $inquiryMaster;
        $data['type'] = $type;

        return $data;
    }

    /* Add Defaults Msater Details */
    public static function inquiryDefault($request){
        DB::beginTransaction();
        try{
            $inquiryMaster = InquiryMasterModel::where('id',$request->id)->first();
            $inquiryMaster->inq_reference_id = 0;
            $inquiryMaster->save();
        }catch(Exception $e){
            DB::rollBack();
            throw new InvalidArgumentException("Something Went Wrong");
        }
        DB::commit();
    }

    /* Get PO Master Details */
    public static function poMasterDetails($whereConditions,$request){
        $inquiryMaster = InquiryMasterModel::where($whereConditions)
        ->join('inquiry_new_po','inquiry_new_po.media_reference_id','inq_reference_id')
        ->join('company_settings','inquiry_new_po.company_id','company_settings.id')
        ->select('inquiry_master.id as id','type','content',DB::raw('DATE_FORMAT(inquiry_master.created_at,"%Y-%m-%d") as created_date'),
        'inquiry_new_po.id as po_id','company_settings.company_name','inquiry_new_po.po_number')
        ->orderBy('inquiry_master.created_at','DESC')
        ->groupBy('inquiry_new_po.parent_id')
        ->paginate(20, ['*'], 'page', $request->page);
        // ->get();

        $type=InquiryMasterModel::select('type')->distinct()->get();

        $data['data'] = $inquiryMaster;
        $data['type'] = $type;

        return $data;
    }
}
