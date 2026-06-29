<?php
use App\Http\Controllers\WebSite\Auth\AdminUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebSite\Reports\DailyProdReports;
use App\Http\Controllers\WebSite\Reports\OrderReport;
use App\Http\Controllers\WebSite\Reports\StaffReport;
use App\Http\Controllers\WebSite\Reports\LoginLog;
use App\Http\Controllers\WebSite\Reports\PlanLog;
use App\Http\Controllers\WebSite\Common\Countries;
use App\Http\Controllers\WebSite\Inquiry\InquiryMaster;
use App\Http\Controllers\WebSite\Inquiry\PdfMerge;
use App\Http\Controllers\WebSite\WorkSpace\WorkSpaceController;
use App\Http\Controllers\UserStaffDetails;
use App\Http\Controllers\WebSite\Company\Company;
use App\Http\Controllers\WebSite\Dashboard\DashboardWidgets;
use App\Http\Controllers\WebSite\Fabric\FabricMaster;
use App\Http\Controllers\WebSite\Plan\Plan;
use App\Http\Controllers\WebSite\PurchaseOrder\PO;
use App\Http\Controllers\WebSite\Staff\Staff;
use App\Http\Controllers\WebSite\Common\Sendemails;
use App\Http\Controllers\Export\MysqlUserDB;
use App\Http\Controllers\WebSite\ChatBox\ChatBox;
use App\Http\Controllers\WebSite\Common\FileController;
use App\Http\Controllers\WebSite\Techpack\Techpack;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/** Super User Login Routes */
Route::post('/get-otp',[AdminUsers::class, 'getOtp']);
Route::post('/verify-otp',[AdminUsers::class, 'otpValidate']);
Route::get('/get-languages',[Countries::class,'languages']);


//Route::middleware('auth:user-api')->group(function(){
    Route::post('/logout',[AdminUsers::class,"Logout"]);
    Route::post('/change-password',[AdminUsers::class, 'changePassword']);
    /* Logs */
    Route::post('/get-daily-prod-report-log',[DailyProdReports::class,'dailyProdReportsLogs']);
    Route::post('/get-workspaces',[DailyProdReports::class,'getWorkspace']);
    Route::post('/get-workspaces-list',[WorkSpaceController::class,'getWorkSpaceList']);
    Route::post('/delete-workspaces-list',[WorkSpaceController::class,'getDeleteWorkSpaceList']);
    Route::post('/get-companies',[DailyProdReports::class,'getCompany']);
    Route::post('/get-users',[DailyProdReports::class,'getuser']);
    Route::post('/get-allstaffs',[DailyProdReports::class,'getstaff']);
    Route::post('/get-allorders',[DailyProdReports::class,'getorders']);
    Route::post('/download-daily-prod-report-log',[DailyProdReports::class,'downloaddailyProdReportsLogs']);
    Route::post('/get-orders-log',[OrderReport::class,'OrderReportLogs']);
    Route::post('/download-orders-log',[OrderReport::class,'downloadOrderReportLogs']);
    Route::post('/get-staff-log',[StaffReport::class,'StaffLogs']);
    Route::post('/download-staff-log',[StaffReport::class,'downloadStaffLogs']);
    Route::post('/get-login-log',[LoginLog::class,'LoginLogs']);
    Route::post('/get-lastlogin-log',[LoginLog::class,'LoginLastLogs']);
    Route::post('/get-lastlogin-logout',[LoginLog::class,'LoginLastLogout']);
    Route::post('/download-login-log',[LoginLog::class,'downloadLoginLogs']);
    Route::post('/get-plan-log',[PlanLog::class,'PlanLogs']);
    Route::post('/download-plan-log',[PlanLog::class,'downloadPlanLogs']);
    Route::post('/download-workspace-files',[WorkSpaceController::class,'getDownloadWorkSpacefiles']);
    Route::post('/get-workspace-fileslist',[WorkSpaceController::class,'getWorkspaceFilesList']);
    Route::post('/download-workspace-single-file',[WorkSpaceController::class,'downloadS3Files']);
    Route::post('/get-userstaff-list',[UserStaffDetails::class,'getUserStaffList']);
    Route::post('/update-userstaff-status',[UserStaffDetails::class,'getUserStaffStatus']);
    /* Inquiry Master */
    Route::post('/inquiry-master',[InquiryMaster::class,'inquiryMaster']);
    Route::post('/inquiry-master-add-default',[InquiryMaster::class,'inquiryMasterDefault']);
    Route::post('/inquiry-article',[InquiryMaster::class,'getInquiryArticles']);
    Route::post('/inquiry-article-add-default',[InquiryMaster::class,'addInquiryArticlesDefault']);
    Route::post('/inquiry-fabric',[InquiryMaster::class,'getInquiryFabrics']);
    Route::post('/inquiry-fabric-add-default',[InquiryMaster::class,'addInquiryFabricsDefault']);
    Route::post('/inquiry-log',[InquiryMaster::class,'inquiryLogs']);
    Route::post('/get-inquiry-ids',[InquiryMaster::class,'getInquiryIds']);
    Route::post('/download-inquiry-log',[InquiryMaster::class,'download_inquiryLogs']);
    /* Plans */
    Route::post('/get-plan-companies',[Plan::class,'getAllCompanies']);
    Route::get('/get-companies-filter',[Plan::class,'getCompaniesForFilter']);
    Route::get('/get-plan',[Plan::class, 'index']);
    Route::post('/update-plan',[Plan::class, 'updatePlan']);
    Route::post('/add-new-plan',[Plan::class, 'addNewPlan']);
    Route::post('/edit-plan',[Plan::class, 'editPlanDetails']);
    Route::post('/update-the-plan',[Plan::class, 'updateThePlan']);
    /* Get the Similar */
    Route::post('/get-similar-companies',[Company::class,'getLikeCompanies']);
    Route::post('/get-similar-staffs',[Staff::class,'getLikeStaffs']);
    Route::post('/get-all-company-storage',[Company::class,'getComapnayStorageList']);
    Route::post('/update-company-settings',[Company::class,'updateComapnaySettings']);

    /* Dshboard */
    Route::get('/dashboard-widgets',[DashboardWidgets::class,'dashboardWidgets']);
    Route::get('/company-counts',[DashboardWidgets::class,'getCompanyCounts']);
    Route::get('/company-storage',[DashboardWidgets::class,'getCompanyS3']);
    Route::get('/company-custom',[DashboardWidgets::class,'getCustomPlanCompanies']);
    Route::get('/company-plans',[DashboardWidgets::class,'getCompanyPlanDetailsAndExpiry']);

    /* Fabric Inquiry Master */
    Route::post('/fabric-master',[FabricMaster::class,'inquiryMaster']);
    Route::post('/fabric-master-add-default',[FabricMaster::class,'inquiryMasterDefault']);
    Route::post('/fabric-log',[FabricMaster::class,'inquiryLogs']);
    Route::post('/download-fabric-log',[FabricMaster::class,'download_inquiryLogs']);

    /* Delete File */
    Route::post("/file-delete",[WorkSpaceController::class,'deleteSingleFile']);

    /* PO */
    Route::post("/po-logs",[PO::class,'poLogs']);
    Route::post("/download-po-log",[PO::class,'download_poLogs']);
    Route::post('/po-master',[InquiryMaster::class,'poMaster']);
    Route::post('/po-article',[InquiryMaster::class,'getPOArticles']);
    Route::post('/po-fabric',[InquiryMaster::class,'getPOFabrics']);

    /*Get User & Staff List in WorkSpace */
    Route::post('/get-company-userstafflist',[Staff::class,'getWorkSpaceUserandStaffList']);
    /* EMAIL */
    Route::post("/send-email",[Sendemails::class,'send_email']);

    /*Export User DataBase */
    Route::post("/export-user-database",[MysqlUserDB::class,'exportTables']);

    /* Techpack */
    Route::post("/techpack-logs",[Techpack::class,'techpackLogs']);
    Route::post("/download-techpack-log",[Techpack::class,'download_techpackLogs']);


//});

Route::post("/import-user-database",[MysqlUserDB::class,'importTables']);
/*PDF Merge */
Route::get('/pdfmerge',[PdfMerge::class, 'pdfmerge']);
Route::post('/pdfmergesubmit',[PdfMerge::class, 'pdfmergesubmit']);

/*Image To PDF*/
Route::get('/image-to-pdf',[PdfMerge::class, 'image_to_pdf']);
Route::post('/image-to-pdf-submit',[PdfMerge::class, 'image_to_pdf_submit']);


/*Import Colors in DataBase */
Route::post("/import-colors",[MysqlUserDB::class,'importColors']);
/*Import Size in DataBase */
Route::post("/import-size",[MysqlUserDB::class,'importSize']);
/*Import Staff in DataBase */
Route::post("/import-staff",[MysqlUserDB::class,'importStaff']);
/*Import orders in DataBase */
Route::post("/import-orders",[MysqlUserDB::class,'importOrders']);
/*Import orders SKU in DataBase */
Route::post("/import-orders-sku",[MysqlUserDB::class,'importOrdersSKU']);
/*Import orders Contact in DataBase */
Route::post("/import-orders-contacts",[MysqlUserDB::class,'importOrdersContacts']);
/*Import orders Template in DataBase */
Route::post("/import-orders-template",[MysqlUserDB::class,'importOrdersTemplate']);
/*Import Template Data in DataBase */
Route::post("/import-template-data",[MysqlUserDB::class,'importTemplateData']);


Route::get('pdftotext', [FileController::class, 'index'])->name('file');
Route::post('pdftotext_submit', [FileController::class, 'store']);

/*Chat box */
Route::get('/get-chat-list',[ChatBox::class, 'get_chat_list']);
Route::post('/get-chat-detail',[ChatBox::class, 'get_chat_detail']);
Route::post('/reply-user-chat',[ChatBox::class,'reply_user_chat']);
Route::get('/get-unread-chat-count',[ChatBox::class,'get_unread_chat_count']);
Route::post('/chat-export',[ChatBox::class,'chat_export']);
Route::post('/chat-status-update',[ChatBox::class,'chat_status_update']);
