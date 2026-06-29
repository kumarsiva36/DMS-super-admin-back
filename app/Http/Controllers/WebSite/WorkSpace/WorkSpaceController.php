<?php

namespace App\Http\Controllers\WebSite\WorkSpace;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Workspace;
use App\Common\Encryption;
use App\Common\CommonApp;
use App\Models\OrderAddSpec;
use App\Models\TechpackMedia;
use App\Models\POMedia;
use Exception;
use Illuminate\Support\Facades\DB;

class WorkSpaceController extends Controller
{
    public function getWorkSpaceList(Request $request){
        //$request= $request->getContent();
        $request= CommonApp::webDecrypt($request->getContent());
        $validated = Validator::make((array)$request,[
            "companyId" => 'required|integer',
        ]);
        if($validated->fails()){
           // return response()->json(["status_code"=>401,"status" =>"failed","validation_error"=>$validated->errors()]);
           $res = json_encode((["status_code"=>401,"status" =>"failed","validation_error"=>$validated->errors()]));
           return CommonApp::webEncrypt($res);
        }

        $result=WorkSpace::getWorkspaceDetails($request);
        $res = json_encode(["status_code"=>200,"status" =>"success","data"=>$result]);
        return CommonApp::webEncrypt($res);
    }
        public function getDeleteWorkSpaceList(Request $request){
            //$request= $request->getContent();
            $request= CommonApp::webDecrypt($request->getContent());
            $validated = Validator::make((array)$request,[
                "workSpaceId" => 'required|integer',
            ]);
            if($validated->fails()){
               // return response()->json(["status_code"=>401,"status" =>"failed","validation_error"=>$validated->errors()]);
               $res = json_encode((["status_code"=>401,"status" =>"failed","validation_error"=>$validated->errors()]));
               return CommonApp::webEncrypt($res);
            }
            try{
                $result=WorkSpace::destoryWorkSpace($request);
                $res = json_encode(["status_code"=>200,"status" =>"success","message"=>"Workspace deleted successfully"]);
            }catch(Exception $e){
                $res = json_encode(["status_code"=>401,"status" =>"failure","message"=>$e->getMessage()]);
            }
            return CommonApp::webEncrypt($res);
        }

        public function getDownloadWorkSpacefiles(Request $request){
            //$request= $request->getContent();
            $request= CommonApp::webDecrypt($request->getContent());
            $validated = Validator::make((array)$request,[
                "workSpaceId" => 'required|integer',
                "companyId" => 'required|integer',
            ]);
            if($validated->fails()){
               // return response()->json(["status_code"=>401,"status" =>"failed","validation_error"=>$validated->errors()]);
               $res = json_encode((["status_code"=>401,"status" =>"failed","validation_error"=>$validated->errors()]));
               return CommonApp::webEncrypt($res);
            }
            $result=WorkSpace::downloadS3Files($request);
            $res = json_encode(["status_code"=>200,"status" =>"success","message"=>"Files Downloaded successfully"]);
            return CommonApp::webEncrypt($res);
        }

        public static function getWorkspaceFilesList(Request $request){
            $request = CommonApp::webDecrypt($request->getContent());
            $validator = Validator::make((array)$request, [
                "workSpaceId" => 'required|integer',
                        ]);
            if ($validator->fails()){
                $res = json_encode(["status_code"=>401,"error"=>$validator->errors()]);
                return CommonApp::webEncrypt($res);
            }
            $whereConditions = [
                ['workspace_id','=',$request->workSpaceId]
            ];
            $uploadedFiles=array();
            $uploadedFiles[0] = OrderAddSpec::where($whereConditions)
                            ->select('id','filename','orginalfilename','filepath','filesize',DB::raw("'orderfile' as type"))
                            ->get();
            $uploadedFiles[1] = POMedia::where($whereConditions)
                            ->select('id','filename','orginalfilename','filepath','filesize',DB::raw("'po' as type"))
                            ->get();
            $uploadedFiles[2] = TechpackMedia::where($whereConditions)
                            ->select('id','filename','orginalfilename','filepath','filesize',DB::raw("'techpack' as type"))
                            ->get();

            $res = json_encode(["status_code"=>200,'status'=>"success","data"=>$uploadedFiles],200);
            //return $res;
            return CommonApp::webEncrypt($res);
        }


    /* Download a file*/
    public static function downloadS3Files(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $validator =Validator::make((array)$request,[
            "workSpaceId" => 'required|integer',
            "id" => 'required|integer',
            "type" => 'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }

        $result=WorkSpace::downloadS3SelecetdFiles($request);
        $res = json_encode(["status_code"=>200,"status" =>"success","message"=>"Files Downloaded successfully"]);

    }

    /* Delete a Single File */
    public static function deleteSingleFile(Request $request){
        $request = CommonApp::webDecrypt($request->getContent());
        $validator =Validator::make((array)$request,[
            "id" => 'required',
            "type" => 'required',
        ]);
        if ($validator->fails()){
            return response()->json(["status_code"=>401,"error"=>$validator->errors()]);
        }
        try{
            OrderAddSpec::deleteFile($request);
            $res = json_encode(["status_code"=>200,"status" =>"success","message"=>"Files Status Changed Successfully"]);
            return CommonApp::webEncrypt($res);
        }catch(Exception $e){
            $res = json_encode(["status_code"=>400,"status" =>"failure","message"=>$e->getMessage()]);
            return CommonApp::webEncrypt($res);
        }
    }
}
