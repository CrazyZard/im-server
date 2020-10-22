<?php

namespace App\Services\Temp;

use App\Enums\UserTempType;
use App\Enums\UserType;
use App\Http\Lib\IpLocation;
use App\SystemUserAccid;
use App\UserTemp;
use App\UserTempSession;
use Illuminate\Support\Facades\Redis;

class TempUserServices
{
    protected $redis;
    use Format;

    public function __construct(Redis $redis)
    {
        $this->redis = $redis::connection('cache');
    }

    /**
     * 创建临时用户
     * @param $data
     * @return bool
     */
    public function createdUser($data)
    {
        try {
            $data['teacher'] = '';
            //重置数据
            $data['uuid'] = $data['api_token'] = uniqid(UserType::PrefixTemp);
            $list = IpLocation::getLocation($data['ip']);
            $data['name'] = '未知';
            $data['token'] = uniqid();
            $data['province'] = $list['province'] ?? '';
            $data['city'] = $list['city'] ?? '';
            $data['name'] = $list['province'] ? $data['province'] . '-' . $data['city'] : '';
            $data['ip'] = ip2long($data['ip']);
            //临时会员信息创建
            $list = UserTemp::create($data);
            return $list;
        } catch (\Exception $exception) {
            log_exception('用户创建失败！', $exception);
            return false;
        }
    }


    /**
     * 判断是否有会话
     * 没有则创建会话
     * 有则返回老会话
     * @param $userId
     * @param $group_id
     * @return mixed
     */
    public function getTempSession($userId, $group_id)
    {
        $temp = UserTempSession::where(['student' => $userId, 'session_status' => UserTempType::Session_Open])->first();
        if ($temp) {
            return [0, $temp];
        } else {
            $group = $this->redis->llen(SystemUserAccid::USER_GROUP . $group_id);
            if ($group) {
                $temp_data['student'] = $userId;
                $temp_data['teacher'] = $this->getGroupTeacher($group_id);
                UserTemp::where('uuid', "{$userId}")->update(['teacher' => $temp_data['teacher']]);
                $temp_data['session_created_at'] = $temp_data['session_end_at'] = date('Y-m-d H:i:s');
                $temp_data['session_status'] = 1;
                //临时会话表创建
                $session = UserTempSession::create($temp_data);
                return [1, $session];
            } else {
                return [0, false];
            }
        }
    }

    /**
     * 从轮询组里得到对应的其他在线老师
     * @param $gid
     * @return string
     */

    private function getGroupTeacher($gid)
    {
        return $this->redis->rpoplpush(
            SystemUserAccid::USER_GROUP . $gid,
            SystemUserAccid::USER_GROUP . $gid
        );
    }


    /**
     * 用户登录时操作
     * @param $uuid
     * @param $group_id
     * @return array
     */
    public function loginAdminUser($uuid, $group_id)
    {
        return $this->redis->lpush(SystemUserAccid::USER_GROUP . $group_id,
            $uuid);
    }


    public function loginUser($uuid)
    {
        $user = UserTemp::where('uuid', $uuid)->first();
        $user->online = 1;
        $user->save();
        return $user;
    }

    public function logoutAdminUser($uuid)
    {
        return $this->redis->lrem(SystemUserAccid::USER_GROUP . '1', 0,
            $uuid);
    }

    public function logoutUser($uuid)
    {
        $user_temp = UserTemp::query()->select('id','online','teacher')->where('uuid', $uuid)->first();
        $user_temp->online = 0;
        $user_temp->save();
        $session = UserTempSession::query()->select('id','offline_time')->where('student',$uuid)->first();
        $session->offline_time = date('Y-m-d H:i:s');
        $session->save();
        return $user_temp->teacher;
    }


    public function changeTeacher($userId, $groupId)
    {
        //判断老会话是否存在
        $temp = UserTempSession::where(['student' => $userId, 'session_status' => UserTempType::Session_Open])->first();
        $temp->session_status = UserTempType::Session_Close;
        $temp->save();
        $group = $this->redis->llen(SystemUserAccid::USER_GROUP . $groupId);
        if ($group) {
            $temp_data['student'] = $userId;
            $temp_data['teacher'] = $this->getGroupTeacher($groupId);
            UserTemp::where('uuid', "{$userId}")->update(['teacher' => $temp_data['teacher']]);
            $temp_data['session_created_at'] = $temp_data['session_end_at'] = date('Y-m-d H:i:s');
            $temp_data['session_status'] = 1;
            //临时会话表创建
            return UserTempSession::create($temp_data);
        } else {
            return false;
        }
    }
}
