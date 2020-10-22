<?php


/**
 * 记录错误日志
 *
 * @param [type] $message
 * @param [object] $exception
 * @param array $stack
 * @return void
 */
function log_exception($message, $exception = array() ,$stack = ['daily'])
{
    if ($exception) {
        $file = $exception->getMessage();
        $line = $exception->getLine();
        $trace = $exception->getTraceAsString();
        $context['message'] = $file;
        $context['line'] = $line;
        $context['trace'] = $trace;
    }
    \Illuminate\Support\Facades\Log::stack($stack)->error($message, $context ?? $exception);
}


function isTeacher($uuid){
    if(strpos($uuid,App\Enums\UserType::PrefixTemp) === false){
        return true;
    }else{
        return false;
    }
}


//通过id获取用户信息
function get_user_parameter($id,$parameter='user_name'){
    $list = \Illuminate\Support\Facades\Cache::remember(config('system.cache.user').$id,60,function () use($id){
        return \App\SystemUser::find($id);
    });
    if($list){
        return $list->$parameter;
    }
    return null;
}

//得到部门基本字段
function get_depart_parameter($id,$parameter = 'name'){
    $list = \Illuminate\Support\Facades\Cache::remember(config('system.cache.dept').$id,15,function () use ($id){
        return  \App\SystemDepartment::find($id);
    });
    if($list){
        return $list->$parameter;
    }
    return null;
}

//新增日志
function action_log($model ='',$action_id ='',$type='',$user_id ='',$remark=""){
    try{
        \App\SystemLog::insert([
            'model'=>$model,
            'model_id'=>$action_id,
            'type'=>$type,
            'remark'=> is_array($remark) || is_object($remark) ? json_encode($remark) : $remark,
            'user_id'=> $user_id ?? request()->get('user_id',0),
            'created_at'=> date('Y-m-d H:i:s')
        ]);
    }catch (\Exception $exception){
        log_exception('日志添加失败',$exception);
    }
}
