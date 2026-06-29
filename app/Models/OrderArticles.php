<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class OrderArticles extends Model
{
    use HasFactory;

    protected $table = 'order_article_name';

    /* To Get all the non default articles */
    public static function inquiryArticle($whereConditions,$request){
        $inquiryArticles = OrderArticles::where($whereConditions)
        ->leftjoin('inquiry','inquiry.media_reference_id','inquiry_reference_id')
        ->leftjoin('company_settings','order_article_name.company_id','company_settings.id')
        ->select('order_article_name.id as id','order_article_name.name as name',DB::raw('DATE_FORMAT(order_article_name.created_at,"%Y-%m-%d") as created_date'),
        'inquiry.id as inquiry_id','company_settings.company_name')
        ->orderBy('order_article_name.created_at','DESC')
        ->paginate(20, ['*'], 'page', $request->page);

        return $inquiryArticles;
    }

    /* To add non-default article as default one */
    public static function defaultArticle($request){
        DB::beginTransaction();
        try{
            $inquiryArticle = OrderArticles::where('id',$request->id)->first();
            $inquiryArticle->inquiry_reference_id = 0;
            $inquiryArticle->is_default = 0;
            $inquiryArticle->save();
        }catch(Exception $e){
            DB::rollBack();
            throw new InvalidArgumentException("Something Went Wrong");
        }
        DB::commit();
    }

    /* To Get all the non default articles (PO) */
    public static function poArticle($whereConditions,$request){
        $inquiryArticles = OrderArticles::where($whereConditions)
        ->leftjoin('inquiry_new_po','inquiry_new_po.media_reference_id','inquiry_reference_id')
        ->leftjoin('company_settings','order_article_name.company_id','company_settings.id')
        ->select('order_article_name.id as id','order_article_name.name as name',DB::raw('DATE_FORMAT(order_article_name.created_at,"%Y-%m-%d") as created_date'),
        'inquiry_new_po.id as po_id','company_settings.company_name','inquiry_new_po.po_number')
        ->orderBy('order_article_name.created_at','DESC')
        ->groupBy('inquiry_new_po.parent_id')
        ->paginate(20, ['*'], 'page', $request->page);

        return $inquiryArticles;
    }
}
