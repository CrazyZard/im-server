<?php

namespace  App\Http\Controllers\External;

use App\Http\Controllers\Controller;
use App\Probe;
use Illuminate\Support\Facades\Cache;

class  ProbeController extends Controller
{
    /**
     * 探头信息
     * @param $uuid
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($uuid)
    {
        $list = Cache::rememberForever(config('system.cache.probe').$uuid,function() use ($uuid){
            return  Probe::select('title','group_id','pc_config','mobile_config')->where('uuid',$uuid)->first();
        });
        if($list){
            return $this->sendData($list);
        }
        return $this->sendData('');
    }
}
