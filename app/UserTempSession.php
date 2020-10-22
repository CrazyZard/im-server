<?php

namespace App;

use App\Filters\UserTempSessionFilter;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class UserTempSession extends Model
{
    protected $fillable = ['student','teacher','message_num',
        'customer_message_num','session_created_at','session_end_at',
        'session_status','is_valid','session_time','first_response_time',
        'avg_response_time','max_response_time','action_url','land_page',
        'land_page_title','search_term'
    ];

    public $timestamps = false;

     use Filterable;

     public function modelFilter()
     {
         return $this->provideFilter(UserTempSessionFilter::class);
     }

    public function user(){
        return $this->hasOne('App\UserTemp','uuid','student');
    }

    public function getLastMsg(){
        return $this->belongsTo('App\UserTempRecord','id','pid')->orderByDesc('id');
    }
}
