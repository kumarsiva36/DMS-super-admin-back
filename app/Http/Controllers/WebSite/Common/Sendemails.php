<?php

namespace App\Http\Controllers\WebSite\Common;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use App\Common\CommonApp;
use App\Jobs\sendEmailJob;
use Illuminate\Support\Facades\Validator;

class Sendemails extends Controller
{

    // /* EMAIL */
    // Route::post("/send-email",[Sendemails::class,'send_email']);

    /* Send Emails */
    public static function send_email(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            "email_ids" => 'required|array',
            "subject" => "required",
            "content" => "required"
        ]);
        if($validated->fails()){
            $res = json_encode((["status_code"=>401,"status" =>"failed","validation_error"=>$validated->errors()]));
            return CommonApp::webEncrypt($res);
        }
        $emails = $request->email_ids;
        foreach($emails as $email){
            $details['to']=$email;
            $details['subject']=$request->subject;
            $details['content']=$request->content;
            sendEmailJob::dispatch($details);
        }
        $res = json_encode(["status_code"=>200,"status" =>"success","data"=>"Emails sent Successfully"]);
        return CommonApp::webEncrypt($res);
    }


}
