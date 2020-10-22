<?php

$api = app('Dingo\Api\Routing\Router');

$api->version('v1',function ($api){

    //通知
    $api->group([
        'namespace' => 'App\Http\Controllers\External',
        'middleware' => ['api.throttle'],//限制次数中间件 throttle
        'limit' => 60, 'expires' => 1,
    ],function ($api){
        $api->get('v1/temp/probe/{uuid}','ProbeController@index'); //探头
        $api->post('v1/temp/create','TempUserController@register'); //临时聊天-绑定老师
        $api->post('v1/temp/leave_message','TempBindController@leaveMessage'); //查询状态

    });




});
