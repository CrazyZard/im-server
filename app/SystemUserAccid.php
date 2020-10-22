<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SystemUserAccid extends Model
{
    protected $guarded = [];

    public $timestamps = true;

    const USER_GROUP = 'im:system:user:group:';


    public function group(){
        return $this->hasOne('App\SessionGroupUser','user_id','user_id');
    }

}
