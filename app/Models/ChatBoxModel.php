<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ChatBoxModel extends Model
{
    use HasFactory;
    protected $connection = 'second_mysql';
    protected $table = 'chatbox';

    public static function getChatList(){
        $data=[];$i=0;
        $users = ChatBoxModel::where('chat_id','!=',NULL)->select('user_id','staff_id','sender_name',DB::raw(' MAX(id) as id'))->groupBy('chat_id')->orderBy('id','desc')->get();
        if(!empty($users)){
            foreach($users as $user){
                $res = ChatBoxModel::where('id',$user->id)->select('message','sent_by','is_sent','created_at','status','chat_id','company_id','workspace_id',DB::raw('DATE_FORMAT(chatbox.created_at,"%Y-%m-%d %H:%i") as created_date'))->first();
                $data[$i]['user_id']=$user->user_id;
                $data[$i]['staff_id']=$user->staff_id;
                $data[$i]['sender_name']=$user->sender_name;
                $data[$i]['message']=$res->message;
                $data[$i]['sent_by']=$res->sent_by;
                $data[$i]['is_sent']=$res->is_sent;
                $data[$i]['status']=$res->status;   
                $data[$i]['chat_id']=$res->chat_id;
                $data[$i]['company_id']=$res->company_id;
                $data[$i]['workspace_id']=$res->workspace_id;
                $data[$i]['created_date']=$res->created_date;
                $data[$i]['created_at']=date('Y-m-d H:i:s', strtotime($res->created_at));
                $i++;
            }
        }

        return $data;
    }

    public static function get_chat_detail($request){
        $data = ChatBoxModel::where('user_id',$request->user_id)->where('staff_id',$request->staff_id)->where('chat_id',$request->chat_id)
        ->select('chatbox.*',DB::raw('DATE_FORMAT(chatbox.created_at,"%Y-%m-%d %H:%i") as created_date'))
        ->get();
        $update =ChatBoxModel::where('user_id',$request->user_id)->where('staff_id',$request->staff_id)->where('chat_id',$request->chat_id)->where('sent_by',1)->update(array('is_sent' => 1));
        return $data;
    }
}
