<?php

namespace App\Http\Controllers\WebSite\Auth;

use App\Common\CommonApp;
use App\Common\GetUserLanguage;
//use App\Common\Logs;
use App\Common\Mailconfig;
use App\Http\Controllers\Controller;
use App\Jobs\UserLoginConfirmationJob;
use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Common\Encryption;
use Illuminate\Support\Facades\Hash;

class AdminUsers extends Controller
{
    /*
        This function is to get the otp and send it through mail
    */
    public function getOtp(Request $request){
        //$request= $request->getContent();
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            "email" => 'required|email',
        ]);
        if($validated->fails()){
           // return response()->json(["status_code"=>401,"status" =>"failed","validation_error"=>$validated->errors()]);
           $res = json_encode((["status_code"=>401,"status" =>"failed","validation_error"=>$validated->errors()]));
           return CommonApp::webEncrypt($res);
        }
        $staff_email = Admin::where('email',$request->email)->where('status',"1")->first();
        if(!is_null($staff_email)){
            $language = "en";//GetUserLanguage::getUserLanguageWithEmail($request->email,"Staff");
            App::setLocale($language);
            $otp = CommonApp::generate_Login_OTP();
            // $otp = 123456;
            $staff_email->otp = $otp;
            $staff_email->otp_generated_time = Carbon::now();
            $staff_email->save();
            Mailconfig::adminOtpSendMail($request->email,$staff_email,$language);
            //return response()->json(["status_code"=>200 ,"message"=>"OTP sent to your email"]);
            $res = json_encode(["status_code"=>200 ,"message"=>"OTP sent to your email"]);
            return CommonApp::webEncrypt($res);
        }
        else{
            $res = json_encode(["status_code"=>400, "message"=>"Contact your DMS ADMIN for Login"]);
            return CommonApp::webEncrypt($res);
        }
    }

    /* This function validates the OTP and gives access token */
    public function otpValidate(Request $request){
        $header = $request;
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            "email" => 'required|email',
            "otp" => 'required_without:password|numeric',
            "password" => 'nullable|string'
        ]);
        if($validated->fails()){
            //return response()->json(["status_code"=>401,"status" =>"failed","validation_error"=>$validated->errors()]);
            $res = json_encode(["status_code"=>401,"status" =>"failed","validation_error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $staff_email = $request->email;
        $staff = Admin::where('email',$staff_email)->first();
if($request->password!='' || $request->password!=null){
      
        if($staff){
            if(Hash::check($request->password, $staff['password'])){
                $token=$staff->createToken('StaffAPIToken')->accessToken;
              
                $res = json_encode(["status_code"=>200,"user_id"=>0,"email"=>$staff_email,
                "admin_id"=>$staff->id,"user_name"=>$staff->first_name." ".$staff->last_name,
                "company_id"=>0,"workspace_id"=>0,"workspaceName"=>"",
                "workspaceType"=>"","role"=>"","language"=>"",
                "roleId"=>"","workspacesList"=>"","permissions"=>"","module"=>"","dateformat"=>"",
                "token"=>$token,"message"=>"Login Successfully"],200);
                return CommonApp::webEncrypt($res);
            
                
        }else{
            $res = json_encode(["status_code"=>201,"status" =>"failed","message"=>"Invalid Password"]);
            return CommonApp::webEncrypt($res);
        }
    }else{
        $res = json_encode(["status_code"=>201,"status" =>"failed","message"=>"User Not Exists"]);
        return CommonApp::webEncrypt($res);
    }
    }else{

      
     
        $language = "en";//GetUserLanguage::getUserLanguageWithEmail($request->email,"Staff");
        App::setLocale($language);
        $otp = $staff->otp;
        $user_entered_otp = $request->otp;
        $browserDetails = $header->header('User-Agent');
        $ipAddress = $header->ip();
        $logType = "Login";
        $logUserType = "Admin";
        $userArr = [];
        $userArr['userID'] = $staff->id;
        $userArr['userName'] = $staff->first_name." ".$staff->last_name;
        $userArr['companyID'] = 0;
        $userArr['browserDetails'] = $browserDetails;
        $userArr['ipAddress'] = $ipAddress;
        $userArr['logType'] = $logType;
        $userArr['logUserType'] = $logUserType;
        if($otp == $user_entered_otp){
            $staffPresentList = Admin::where('email',$staff_email)->where('status','1')->first();
            $token = $staff->createToken('StaffAPIToken')->accessToken;
            $loginStatus = "Success";
            $userArr['loginStatus'] = $loginStatus;
            $details=[];
            $details['to']=$request->email;
            $details['userName']=$staff->first_name." ".$staff->last_name;
            $details['language']=$language;
            // UserLoginConfirmationJob::dispatch($details);
            //Logs::userLogs($userArr);

            $res = json_encode(["status_code"=>200,"user_id"=>0,"email"=>$staff_email,
            "admin_id"=>$staffPresentList->id,"user_name"=>$staff->first_name." ".$staff->last_name,
            "company_id"=>0,"workspace_id"=>0,"workspaceName"=>"",
            "workspaceType"=>"","role"=>"","language"=>"",
            "roleId"=>"","workspacesList"=>"","permissions"=>"","module"=>"","dateformat"=>"",
            "token"=>$token,"message"=>"OTP Verified Successfully"],200);
            return CommonApp::webEncrypt($res);
        }
        else{
            $loginStatus = "Failure";
            $userArr['loginStatus'] = $loginStatus;
           // Logs::userLogs($userArr);
            // return response()->json(["status_code"=>400,"message"=>"Incorrect OTP, Please Enter Correctly"]);
            $res = json_encode(["status_code"=>400,"message"=>"Incorrect OTP, Please Enter Correctly"]);
            return CommonApp::webEncrypt($res);
        }
    }
    }

    /* User Logout function*/
    public function Logout(Request $request){
        Admin::logout($request);
        return response()->json(["status_code"=>200,"message"=>"User Logged Out Successfully"]);
    }
    public function changePassword(Request $request){
        $header = $request;
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
       // $validated = Validator::make($request->all(), [
            'id' => 'required:min:1',
            'old_password' => 'required',
            'new_password' => 'required|string|min:8',
             ]);
          
        if($validated->fails()){
            $res = json_encode(["status_code"=>401,"status" =>"failed","validation_error"=>$validated->errors()]);
            return CommonApp::webEncrypt($res);
        }
        $staff = Admin::select("password")->where('id',$request->id)->first();
        if(!empty($staff)){
        if(!Hash::check($request->old_password, $staff['password'])){
            $res=json_encode(["status_code"=>201,"status" =>"failed",'message' => 'Incorrect Old Password']);
            return CommonApp::webEncrypt($res);
        }
        $hashedPassword = Hash::make($request->new_password);
        Admin::where("id",$request->id)->update(array("password" => $hashedPassword));
        $res=json_encode(["status_code"=>200,"status" =>"success",'message' => 'Password Changed  Successfully']);
         return CommonApp::webEncrypt($res);
    }else{
        $res=json_encode(["status_code"=>201,"status" =>"failed",'message' => 'Invalid Details']);
        return CommonApp::webEncrypt($res);
    }
    }

}
