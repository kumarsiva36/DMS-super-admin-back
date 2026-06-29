<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class FabricType extends Model
{
    use HasFactory;

    protected $table = 'fabric_type';

    /* To Get all the non default articles */
    public static function inquiryFabric($whereConditions,$request){
        $inquiryFabric = FabricType::where($whereConditions)
        ->leftjoin('inquiry','inquiry.media_reference_id','inquiry_reference_id')
        ->leftjoin('company_settings','fabric_type.company_id','company_settings.id')
        ->select('fabric_type.id as id','fabric_type.name as name',DB::raw('DATE_FORMAT(fabric_type.created_at,"%Y-%m-%d") as created_date'),
        'inquiry.id as inquiry_id','company_settings.company_name')
        ->orderBy('fabric_type.created_at','DESC')
        ->paginate(20, ['*'], 'page', $request->page);

        return $inquiryFabric;
    }

    /* To add non-default article as default one */
    public static function defaultFabric($request){
        DB::beginTransaction();
        try{
            $inquiryFabric = FabricType::where('id',$request->id)->first();
            $inquiryFabric->inquiry_reference_id = 0;
            $inquiryFabric->is_default = 0;
            $inquiryFabric->save();
        }catch(Exception $e){
            DB::rollBack();
            throw new InvalidArgumentException("Something Went Wrong");
        }
        DB::commit();
    }

    /* To Get all the non default articles(PO) */
    public static function poFabric($whereConditions,$request){
        $inquiryFabric = FabricType::where($whereConditions)
        ->leftjoin('inquiry_new_po','inquiry_new_po.media_reference_id','inquiry_reference_id')
        ->leftjoin('company_settings','fabric_type.company_id','company_settings.id')
        ->select('fabric_type.id as id','fabric_type.name as name',DB::raw('DATE_FORMAT(fabric_type.created_at,"%Y-%m-%d") as created_date'),
        'inquiry_new_po.id as po_id','company_settings.company_name','inquiry_new_po.po_number')
        ->orderBy('fabric_type.created_at','DESC')
        ->groupBy('inquiry_new_po.parent_id')
        ->paginate(20, ['*'], 'page', $request->page);

        return $inquiryFabric;
    }
}
