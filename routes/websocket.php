<?php

use App\Services\Temp\RecordServices;
use App\Services\Temp\TempUserServices;
use App\Services\Temp\SessionServices;
use Swoole\Http\Request;
use App\Services\WebSocket\WebSocket;
use App\Services\WebSocket\Facades\Websocket as WebsocketProxy;
use Illuminate\Support\Facades\Log;

/*
    |--------------------------------------------------------------------------
    | Websocket Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register websocket events for your application.
    |
    */


WebsocketProxy::on('connect', function (WebSocket $webSocket, Request $request) {
    // 发送欢迎信息
    $webSocket->setSender($request->fd);
    $user = auth('api')->user();
    if (collect($user)->toArray()) {
        $webSocket->loginUsing($user);
    } else {
        Log::debug('unkown_user');
        $webSocket->emit('login', '账户认证失败！');
    }
});

WebsocketProxy::on('disconnect', function (WebSocket $webSocket,  TempUserServices $service) {
    if($webSocket->getUserId()){
        if(isTeacher($webSocket->getUserId())){
            $service->logoutAdminUser($webSocket->getUserId());
        }else{
            $teacher = $service->logoutUser($webSocket->getUserId());
            $webSocket->toUserId($teacher)->emit('systemEvent',[
                'status' => 200,
                'data' => [
                    'event' => 'userLogout',
                    'uuid' => $webSocket->getUserId(),
                    'message' => '临时用户下线！'
                ],
            ]);
        }
    }
    $webSocket->logoutUsingId();
});


//临时用户登录
WebsocketProxy::on('login', function (WebSocket $webSocket, TempUserServices $service, $data) {
    if ($webSocket->getUserId() && isset($data['group_id'])) {
        if (isTeacher($webSocket->getUserId())) {
            //老师端登录
            $service->loginAdminUser($webSocket->getUserId(),$data['group_id']);
            $webSocket->emit('login', [
                'status' => 200,
                'data' => [
                    'time' => time(),
                    'uuid' => $webSocket->getUserId(),
                ],
                'message' => '老师登录成功！'
            ]);
        } else {
            //临时用户
            $user = $service->loginUser($webSocket->getUserId());
            //查看是否有会话，
            list($isNew,$session)  = $service->getTempSession($webSocket->getUserId(),$data['group_id']);
            if ($session) {
                //通知老师刷新会话列表
                $messageData = [
                    'status' => 200,
                    'data' => [
                        'sessionList' => [
                            'ConversationID' => $session->id,
                            'NickName' => $session->student,
                            'IMG' => '',
                            'Online' => $user->online,
                            'ValidStatus' => $session->is_valid,
                            'ToAccount' => $session->student,
                            'Type' => 1,
                            'LastMsg' => [
                                'MsgContent' => '',
                                'MsgType' => ''
                            ],
                            'UserProfile' => [
                                'Name' => $user->name,
                                'Avatar' => $user->avatar,
                                'Status' => $user->status,
                                'Remark' => $user->remark
                            ],
                            'UnreadMsgCount' => $session->unread_msg_count,
                        ]
                    ]
                ];
                if($isNew){
                    $webSocket->toUserId($session->teacher)->emit('newConversation', $messageData);
                }else{
                    
                    $webSocket->toUserId($session->teacher)->emit('systemEvent', [
                        'data' => [
                            'event' => 'userLogin',
                            'uuid' => $webSocket->getUserId(),
                            'message' => '临时用户上线！'
                        ],
                        'status' => 200
                    ]);
                }
                //返回登录成功！
                $webSocket->emit('login', [
                    'status' => 200,
                    'message' => '登录成功！',
                    'data' => [
                        'uuid' => $webSocket->getUserId(),
                        'teacher' => $session->teacher,
                        'time' => time(),
                        'teacher_name' => $session->teacher
                    ]
                ]);
            } else {
                $webSocket->emit('login', [
                    'status' => 401,
                    'message' => '当前老师不在线！'
                ]);
            }
        }
    } else {
         $webSocket->emit('system', [
            'status' => 402,
            'event' => 'AutoConnection',
            'message' => '用户失效请刷新页面'
        ]);
    }
});

//发送消息
WebsocketProxy::on('sendMessage', function (WebSocket $webSocket, RecordServices $recordServices, $data) {
    if ($userId = $webSocket->getUserId()) {
        // 判断是否正确消息
        $bool = $recordServices->validate($data);
        if($bool){
            // 将消息保存到数据库（图片消息除外，因为在上传过程中已保存）
            $record = $recordServices->store($data);
            if($record){
                $webSocket->emit('system', [
                    'status' => 200,
                    'data' => [
                        'event' => 'MessageConfirm',
                        'msgTimeStamp' => $data['MsgTimeStamp'],
                        'conversationID' => $data['ConversationID'],
                        'msgId' => $record->id
                    ]
                ]);
                $webSocket->toUserId($data['To'])->emit('system', [
                    'status' => 200,
                    'data' => [
                        'event' => 'NewMessage',
                        'item' => [
                            'MsgId' => $record->id,
                            'ConversationID' => $record->pid,
                            'MsgContent' => $record->body,
                            'MsgType' => $record->type,
                            'From' => $record->from,
                            'To' => $record->to,
                            'MsgCreatedTime' => $record->created_at,
                        ]
                    ]
                ]);
            }else{
                $webSocket->emit('system', [
                    'status' => 500,
                    'data' => [
                        'event' => 'MessageConfirm',
                        'msgTimeStamp' => $data['MsgTimeStamp'],
                        'conversationID' => $data['conversationID']
                    ]
                ]);
            }
        }else{
            $webSocket->emit('system', [
                'status' => 500,
                'message' => '消息格式错误'
            ]);
        }
    } else {
        $webSocket->emit('system', [
            'status' => 402,
            'event' => 'AutoConnection',
            'message' => '用户失效请刷新页面'
        ]);
    }
});

//取消会话未读数
WebsocketProxy::on('conversationRead', function (WebSocket $webSocket, SessionServices $sessionServices, $data) {
    if ($userId = $webSocket->getUserId()) {
        //重置未读数列表
        $sessionServices->resetConversationUnreadNum($data['ConversationID']);
        $messageData = [
            'status' => 200,
            'data' => $data
        ];
        $webSocket->emit('conversationRead', $messageData);
    } else {
        $webSocket->emit('system', [
            'status' => 402,
            'event' => 'AutoConnection',
            'message' => '用户失效请刷新页面'
        ]);
    }
});

// 会话列表获取
WebsocketProxy::on('conversationList', function (WebSocket $webSocket, SessionServices $sessionServices, $data) {
    if ($webSocket->getUserId()) {
        if (isTeacher($webSocket->getUserId())) {
            $session = $sessionServices->getTeacherConversationList($webSocket->getUserId(), $data);
            $messageData = [
                'status' => 200,
                'data' => [
                    'sessionList' => $session,
                    'status' => $data['state'] ?? 0
                ]
            ];
        } else {
            $session = $sessionServices->getStudentConversationList($webSocket->getUserId());
            $messageData = [
                'status' => 200,
                'data' => [
                    'sessionItem' => $session
                ]
            ];
        }
        $webSocket->emit('conversationList',$messageData);
    } else {
        $webSocket->emit('system', [
            'status' => 402,
            'event' => 'AutoConnection',
            'message' => '用户失效请刷新页面'
        ]);
    }
});

//会话删除 通知
WebsocketProxy::on('conversationDel', function (WebSocket $webSocket, SessionServices $sessionServices, $data) {
    if ($userId = $webSocket->getUserId()) {
        if (isset($data['room'])) {
            $sessionServices->destroy($data);
            $webSocket->to($data['student'])->emit('logout');
            $webSocket->emit('conversationDel', [
                'status' => 200,
                'message' => 'success'
            ]);
        } else {
            return $webSocket->emit('conversationDel', [
                'status' => 0,
                'message' => '找不到对应会话'
            ]);
        }

    } else {
        $webSocket->emit('system', [
            'status' => 402,
            'event' => 'AutoConnection',
            'message' => '用户失效请刷新页面'
        ]);
    }
});

//获取历史记录
WebsocketProxy::on('getMessageList', function (WebSocket $webSocket, SessionServices $sessionServices, $data, RecordServices $recordServices) {
    if ($userId = $webSocket->getUserId()) {
        if (isset($data['conversation_id']) && isset($data['nextReqMessageID'])) {
            //获取列表数
            $message = $recordServices->getMessageList($data['conversation_id'], $data['nextReqMessageID']);
            return $webSocket->emit('getMessageList', $message);
        } else {
            return $webSocket->emit('getMessageList', 'conversation_id丢失！');
        }
    } else {
        $webSocket->emit('system', [
            'status' => 402,
            'event' => 'AutoConnection',
            'message' => '用户失效请刷新页面'
        ]);
    }
});

//获取会话属性
WebsocketProxy::on('getConversationProfile', function (WebSocket $webSocket, SessionServices $sessionServices, $data) {
    if ($userId = $webSocket->getUserId()) {
        if (isTeacher($userId) && isset($data['conversation_id'])) {
            $session = $sessionServices->getConversationProfile($data['conversation_id']);
            $messageData = [
                'status' => 200,
                'data' => [
                    'sessionList' => $session
                ]
            ];
            $webSocket->emit('getConversationProfile', $messageData);
        } else {
            return $webSocket->emit('getMessageList', '参数丢失！');
        }
    } else {
        $webSocket->emit('system', [
            'status' => 402,
            'event' => 'AutoConnection',
            'message' => '用户失效请刷新页面'
        ]);
    }
});

//消息回撤
WebsocketProxy::on('revokeMessage', function (WebSocket $webSocket, RecordServices $recordServices, $data) {
    if ($userId = $webSocket->getUserId()) {
        if(isset($data['FromAccount']) && $data['ToAccount'] && isset($data['MsgKey'])){
            $bool =  $recordServices->revokeMessage($data['MsgKey'],$userId);
            if($bool){
                $webSocket->emit('revokeMessage',[
                    'status' => 200,
                    'msgKey' => $data['MsgKey'],
                    'event' => 'revoke'
                ]);
                $webSocket->toUserId($data['ToAccount'])->emit('revokeMessage',[
                    'status' => 200,
                    'msgKey' => $data['MsgKey'],
                    'EVENT' => 'revoke'
                ]);

            }
        }

    } else {
         $webSocket->emit('system', [
            'status' => 402,
            'event' => 'AutoConnection',
            'message' => '用户失效请刷新页面'
        ]);
    }
});

//系统通知
WebsocketProxy::on('systemEvent', function (WebSocket $webSocket, RecordServices $recordServices, $data) {
    if ($userId = $webSocket->getUserId()) {

    } else {
        $webSocket->emit('system', [
            'status' => 402,
            'event' => 'AutoConnection',
            'message' => '用户失效请刷新页面'
        ]);
    }
});


//学员分配老师
WebsocketProxy::on('changeTeacher', function (WebSocket $webSocket, TempUserServices $services, $data) {
    if ($webSocket->getUserId() && isset($data['group_id']) ) {
           $session =  $services->changeTeacher($webSocket->getUserId(),$data['group_id']);
            //返回登录成功！
           $webSocket->emit('changeTeacher', [
                'status' => 200,
                'message' => '换老师成功！',
                'data' => [
                    'uuid' => $webSocket->getUserId(),
                    'teacher' => $session->teacher,
                    'time' => time(),
                    'teacher_name' => $session->teacher
                ]
           ]);
    } else {
        $webSocket->emit('system', [
            'status' => 402,
            'event' => 'AutoConnection',
            'message' => '用户失效请刷新页面'
        ]);
    }
});


