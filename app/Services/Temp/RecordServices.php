<?php
namespace App\Services\Temp;

use App\UserTempRecord;
use App\UserTempSession;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RecordServices
{
    public  function  validate($data){
        $validator = Validator::make($data,[
            'MsgContent' => 'required',
            'ConversationID' => 'required',
            'MsgType' => 'required|int',
            'From' => 'required',
            'To' => 'required',
            'MsgTimeStamp' => 'required',
        ]);
        if($validator->fails()){
            return false;
        }
        return true;
    }

    /**
     * 保存历史记录
     * @param $data
     * @return bool
     */
    public function store($data)
    {
        try{
            $user =   UserTempRecord::create([
                'from' => $data['From'],
                'to' => $data['To'],
                'pid' => $data['ConversationID'],
                'type' => $data['MsgType'],
                'body' => $data['MsgContent'],
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            if(isTeacher($data['To'])){
                UserTempSession::where('id',$data['ConversationID'])->update([
                    'unread_msg_count' => DB::raw("unread_msg_count + 1"),
                    'session_end_at' => date('Y-m-d H:i:s')
                ]);
            }
            return $user;
        }catch (\Exception $exception){
            log_exception('消息记录失败！',$exception);
            return false;
        }
    }


    /**
     * 获取聊天记录
     * @param $conversation_id
     * @param $nextReqMessageID
     * @return mixed
     */

    public function getMessageList($conversation_id,$nextReqMessageID)
    {
        Log::debug('---',[$conversation_id,$nextReqMessageID]);
        $where[] = array('pid','=',$conversation_id);
        if (is_numeric($nextReqMessageID)) {
            $where[] = ['id','<',$nextReqMessageID];
        }
        $count = UserTempRecord::where($where)->count();
        $list =  UserTempRecord::select('id', 'from', 'to', 'type', 'body','is_revoke', 'created_at','pid')->where($where)->orderByDesc('id')->limit(15)->get();
        $data = [
            'messageList' => [],
            'nextReqMessageID' => 0,
            'isCompleted' => true
        ];
        if ($count > 15) {
            $data['isCompleted'] = false;
        }
        foreach ($list as $key => $val) {
            $data['messageList'][] = [
                'MsgId' => $val->id,
                'ConversationID' => $val->pid,
                'MsgContent' => $val->body,
                'MsgType' => $val->is_revoke == 0 ? $val->type : 100,
                'From' => $val->from,
                'To' => $val->to,
                'MsgCreatedTime' => $val->created_at,
            ];
            $data['nextReqMessageID'] = $val->id;
        }
        return $data;
    }

    /**
     * 回撤消息
     * @param $id
     * @param $uuid
     * @return bool
     */
    public  function revokeMessage($id,$uuid){
        $record =  UserTempRecord::find($id);
        if($record->from == $uuid){
            $record->is_revoke = 1;
            $record->save();
            return $record->to;
        }
        return false;
    }
}
