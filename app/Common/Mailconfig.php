<?php

namespace App\Common;

use App\Mail\AdminOtpMail;
use Illuminate\Support\Facades\Mail;

class Mailconfig{
   
    /* To Send OTP for admin Login */
    public static function adminOtpSendMail($email,$data,$language){
        Mail::to($email)->send(new AdminOtpMail($data,$language));
    }
}
