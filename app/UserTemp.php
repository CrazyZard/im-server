<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class UserTemp extends Authenticatable
{
    protected $fillable = [
        'uuid','name','status','online','ip','opt_platform','province'
        ,'city','teacher','action_url','land_page','land_page_title','search_term',
        'phone','wechat','qq','remark','card_created_at','api_token'
    ];

    public $timestamps = true;
}
