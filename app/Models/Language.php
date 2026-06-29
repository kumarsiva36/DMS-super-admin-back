<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $table = 'language';

    public static function getLanguages(){
        $languages = Language::where('status','1')->select('id','name','lang_code')->get();
        return $languages;
    }
}
