<?php namespace App\Filters;

use EloquentFilter\ModelFilter;

class UserTempSessionFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    protected $drop_id = false;

    protected function teacher($uuid)
    {
        return $this->where('user_temp_sessions.teacher',$uuid);
    }

    //手机
    protected function phone($parms)
    {
        return $this->where('user_temps.phone',$parms);
    }

    //微信
    protected function wechat($wechat)
    {
        return $this->where('user_temps.wechat',$parms);
    }
    //会话开始时间
    protected function session_created_at($params)
    {
        return $this->where('user_temp_sessions.session_created_at',$params);
    }
    //会话结束时间
    protected function session_end_at($params)
    {
        return $this->where('user_temp_sessions.session_end_at',$params);
    }

    //地域
    protected function city($params)
    {
        return $this->where('user_temp_sessions.city',$params);
    }

    //访客消息数
    protected function customer_message_num($params)
    {
        return $this->where('user_temp_sessions.customer_message_num',$params);
    }

    //访客消息数
    protected function message_num($params)
    {
        return $this->where('user_temp_sessions.message_num',$params);
    }


   //名片创建时间
    public function created_at($params)
    {
        return $this->where('user_temps.created_at',$params);
    }

    //落地页
    public function land_page($params)
    {
        return $this->where('user_temp_sessions.land_page',$params);
    }

    //搜索词
    public function search_term($params)
    {
        return $this->where('user_temp_sessions.search_term',$params);
    }


    public function state($params)
    {
       return $this->where('user_temps.online',$params);
    }

}
