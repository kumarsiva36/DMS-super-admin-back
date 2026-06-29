<?php

namespace App\Http\Controllers\WebSite\ChatBox;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Common\CommonApp;
use App\Models\ChatBoxModel;
use Barryvdh\DomPDF\Facade\Pdf;

class ChatBox extends Controller
{
    public function get_chat_list(){
        $result=ChatBoxModel::getChatList();
        $res = json_encode(["status_code"=>200,"status" =>"success","data"=>$result]);
        return CommonApp::webEncrypt($res);
    }

    public function get_chat_detail(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            "user_id" => 'required|integer',
            "staff_id" => 'required|integer',
            "chat_id"=>'required',
        ]);
        if($validated->fails()){
           $res = json_encode((["status_code"=>401,"status" =>"failed","validation_error"=>$validated->errors()]));
           return CommonApp::webEncrypt($res);
        }
        $result=ChatBoxModel::get_chat_detail($request);
        $res = json_encode(["status_code"=>200,"status" =>"success","data"=>$result]);
        return CommonApp::webEncrypt($res);
    }

    public static function reply_user_chat(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validator = Validator::make((array)$request, [
            'chat_id' => 'required',
            'message' => 'required',
            //'sender_name' => 'required',
            'user_id' => 'required',
            'staff_id' => 'required',
            'company_id' => 'required',
            'workspace_id' => 'required'
        ]);
        if ($validator->fails()){
            $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
            return CommonApp::webEncrypt($res);
        }

        $det['chat_id'] = $request->chat_id;
        $det['message'] = $request->message;
        $det['sent_by'] = 2;
        $det['sender_name'] = 'Admin';
        $det['user_id'] = $request->user_id;
        $det['staff_id'] = $request->staff_id;
        $det['company_id'] = $request->company_id;
        $det['workspace_id'] = $request->workspace_id;
        $det['created_at'] = date('Y-m-d H:i:s');

        ChatBoxModel::insert($det);

        $res = json_encode(["status_code"=>200,'status'=>"success","message"=>"Reply sent Successfully"],200);
        return CommonApp::webEncrypt($res);
    }

    public static function get_unread_chat_count(){
        $count = ChatBoxModel::where('sent_by',1)->where('is_sent',0)->count();

        $res = json_encode(["status_code"=>200,'status'=>"success","count"=>$count],200);
        return CommonApp::webEncrypt($res);
    }

    public static function chat_export(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            "user_id" => 'required|integer',
            "staff_id" => 'required|integer',
            "chat_id"=>'required',
        ]);
        if($validated->fails()){
           $res = json_encode((["status_code"=>401,"status" =>"failed","validation_error"=>$validated->errors()]));
           return CommonApp::webEncrypt($res);
        }
        $result=ChatBoxModel::get_chat_detail($request);
        view()->share(["result"=>$result,"request"=>$request]);
        $pdf = Pdf::loadView('chatExportPDF');
        $pdf->getOptions()->setIsFontSubsettingEnabled(true);
        $pdf->setOption(['defaultFont' => 'arialuni','poppins','notoSansJP']);
        return $pdf->download();
    }

    public function chat_status_update(Request $request){
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            "user_id" => 'required|integer',
            "staff_id" => 'required|integer',
            "chat_id"=>'required',
        ]);
        if($validated->fails()){
           $res = json_encode((["status_code"=>401,"status" =>"failed","validation_error"=>$validated->errors()]));
           return CommonApp::webEncrypt($res);
        }
        $update =ChatBoxModel::where('user_id',$request->user_id)->where('staff_id',$request->staff_id)->where('chat_id',$request->chat_id)->update(array('status' => 1));
        $res = json_encode(["status_code"=>200,"status" =>"success","message"=>"Status updated Successfully"]);
        return CommonApp::webEncrypt($res);
    }
}
