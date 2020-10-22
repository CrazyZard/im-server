<?php

namespace App;

use App\Filters\UserTempRecordFilter;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class UserTempRecord extends Model
{
    protected $guarded = [];

    CONST TYPE_TEXT = 0;
    CONST TYPE_PICTURE = 1;
    CONST TYPE_AUDIO = 2;
    CONST TYPE_FILE = 6;
    public $timestamps = false;


    // use  Filterable;

    // public function modelFilter()
    // {
    //     return $this->provideFilter(UserTempRecordFilter::class);
    // }


    public function user(){
        return $this->hasOne('App\UserTemp','uuid','student');
    }


}
