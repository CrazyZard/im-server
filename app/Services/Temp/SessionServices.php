<?php
namespace App\Services\Temp;

use App\Enums\UserTempType;
use App\UserTempFriend;
use App\UserTempSession;

class  SessionServices {

    protected  $friend,$server;

    public function __construct(UserTempSession $server,UserTempFriend $friend)
    {
        $this->server = $server;
        $this->friend = $friend;
    }


    //重置会话
    public function refresh($accid,$faccid)
    {
        try {
            $temp = UserTempSession::where(['student'=>$accid,'session_status'=>UserTempType::Session_Open])
                ->select('id','vcc_id','student','teacher','action_url','land_page','land_page_title','search_term')->first();
            $temp->session_status = UserTempType::Session_Close;
            $temp->update();
            $data = collect($temp)->toArray();
            $data['teacher'] = $faccid;
            $this->destroy($temp->student,$temp->teacher);
            $this->create($data);
        }catch (\Exception $exception){
            log_exception('重置会话失败！',$exception);
        }
    }

    //删除
    public function destroy($data){
        try {
            $session = UserTempSession::find($data['room']);
            if($session){
                $friend =  UserTempFriend::where(['student'=>$session->student,'teacher'=>$session->teacher])->first();
                if($friend){
                    $friend->delete();
                }
                $session->session_status = UserTempType::Session_Close;
                $session->update();
                return [
                    'teacher' =>  $session->teacher,
                    'student' => $session->student
                ];
            }
            return false;
        }catch (\Exception $exception){
            log_exception();
        }


    }

    /**
     * 老师获取会话列表
     * @param $uuid
     * @param $data
     * @return array
     */
    public  function getTeacherConversationList($uuid,$data)
    {
        $sessions =  UserTempSession::select('user_temp_sessions.*','user_temps.online','user_temps.name'
            ,'user_temps.avatar','user_temps.status','user_temps.remark')
            ->join('user_temps','user_temps.uuid','user_temp_sessions.student')
            ->where('user_temp_sessions.teacher',$uuid)->filter($data)
            ->orderByDesc('session_end_at')->get();
        $sessionItem = [];
        foreach ($sessions  as  $key => $item){
            $data  = [
                'ConversationID' => $item->id,
                'NickName' => $item->student,
                'IMG' => '',
                'Online' => $item->online,
                'ValidStatus' => $item->is_valid,
                'ToAccount' => $item->student,
                'Type' => 1,
                'LastMsg' => [
                    'MsgContent' => '',
                    'MsgType' => ''
                ],
                'UserProfile' => [
                    'Name' => $item->name,
                    'Avatar' => $item->avatar,
                    'Status' => $item->status,
                    'Remark' => $item->remark
                ],
                'UnreadMsgCount' => $item->unread_msg_count,
            ];
            $list = $item->getLastMsg()->first();
            if($list){
                $data['LastMsg'] = [
                    'MsgContent' => $list->is_revoke == 0 ? $list->body : '',
                    'MsgType' => $list->is_revoke == 0 ? $list->type : 100
                ];
            }
            $sessionItem[] = $data;
        }
        return $sessionItem;
    }


    /**
     * 老师获取单个会话
     * @param $conversation_id
     * @return array
     */

    public  function getConversationProfile($conversation_id)
    {
        $sessions =  UserTempSession::select('user_temp_sessions.*','user_temps.online','user_temps.name'
            ,'user_temps.avatar','user_temps.status','user_temps.remark')
            ->join('user_temps','user_temps.uuid','user_temp_sessions.student')
            ->where('user_temp_sessions.id',$conversation_id)->first();
        $conversation  = [
            'ConversationID' => $sessions->id,
            'NickName' => $sessions->student,
            'IMG' => '',
            'Online' => $sessions->online,
            'ValidStatus' => $sessions->is_valid,
            'ToAccount' => $sessions->student,
            'Type' => 1,
            'LastMsg' => [
                'MsgContent' => '',
                'MsgType' => ''
            ],
            'UserProfile' => [
                'Name' => $sessions->name,
                'Avatar' => $sessions->avatar,
                'Status' => $sessions->status,
                'Remark' => $sessions->remark
            ],
            'UnreadMsgCount' => $sessions->unread_msg_count,
        ];

        $list = $sessions->getLastMsg()->first();
        if($list){
            $conversation['LastMsg'] = [
                'MsgContent' => $list->is_revoke == 0 ? $list->body : '',
                'MsgType' => $list->is_revoke == 0 ? $list->type : 100
            ];
        }
        return $conversation;
    }

    /**
     * 获取临时聊天列表
     * @param $uuid
     * @return array
     */
    public  function getStudentConversationList($uuid)
    {
        $session =  UserTempSession::select('user_temp_sessions.*','user_temps.online','user_temps.name'
            ,'user_temps.avatar','user_temps.status','user_temps.remark')
            ->join('user_temps','user_temps.uuid','user_temp_sessions.teacher')
            ->where('user_temp_sessions.student',$uuid)->first();
        $data = [];
        if($session){
            $data  = [
                'ConversationID' => $session->id,
                'NickName' => $session->name,
                'IMG' => '',
                'ToAccount' => $session->teacher,
                'Type' => 1,
                'LastMsg' => [
                    'MsgContent' => '',
                    'MsgType' => ''
                ],
                'UserProfile' => [
                    'Name' => $session->name,
                    'Avatar' => $session->avatar,
                    'Status' => $session->status,
                    'Remark' => $session->remark
                ],
            ];
            $list = $session->getLastMsg()->first();
            if($list){
                $data['LastMsg'] = [
                    'MsgBody' => [
                        'MsgContent' => $list->is_revoke == 0 ? $list->body : '',
                        'MsgType' => $list->is_revoke == 0 ? $list->type : 100
                    ]
                ];
            }
        }
        return $data;
    }


    /**
     * 清空未读数
     * @param $id
     * @return bool
     */
    public function resetConversationUnreadNum($id)
    {
        $session =   UserTempSession::find($id);
        if($session){
            $session->unread_msg_count = 0 ;
            $session->save();
        }
        return true;
    }

}
