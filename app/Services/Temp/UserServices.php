<?php

namespace App\Services\Temp;

use App\Enums\UserType;
use App\Http\Lib\IpLocation;
use App\SystemUserAccid;
use App\UserTemp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class UserServices 
{

    use Format;
    public function __construct(Redis $redis)
    {
        $this->redis = $redis::connection('cache');
    }

    public function createdUser($data)
    {
        try {
           
        } catch (\Exception $exception) {
            log_exception('用户创建失败！', $exception);
            return false;
        }
    }


  

    /**
     * 登录用户并返回对用roomId
     *
     * @param [type] $data
     * @return void
     */
    public function loginUser($data)
    {
        $user =  \App\User::where('uuid', $data['uuid'])->select('id','uuid')->first();
        if($user){
            return $user;
        }
        return false;  
    }
}
