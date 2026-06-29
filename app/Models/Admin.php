<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens,HasFactory,Notifiable;

    protected $table = 'adminusers';
    protected $fillable =[
       'id','first_name','last_name','mobile','email','address1','address2','city','state','zipcode','country','status','language','timezone','created_at','updated_at'
    ];
    protected $hidden=[
        'otp',
        //'remember_token',
     ];
     //
     protected $casts=[
        'email_verified_at' => 'datetime',
     ];

     public static function logout($request){
      $token = $request->user()->token();
      $token->revoke();
  }


}
