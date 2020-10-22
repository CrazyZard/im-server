<?php namespace App\Filters;

use Dingo\Api\Auth\Auth;
use EloquentFilter\ModelFilter;

class SystemUserFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    protected $blacklist = ['secretMethod'];
    protected $drop_id = false;

    public function projectId($project_id)
    {
        return $this->where('project_id',$project_id);
    }


   public function deptId($dept_id)
   {
       return $this->where('dept_id',$dept_id);
   }

    //工号
   protected function userNum($params)
   {
       return $this->where('user_num',$params);
   }

   protected function userName($params)
   {
       return $this->where('user_name',$params);
   }

   protected function status($params)
   {
       return $this->where('status',$params);
   }


}
