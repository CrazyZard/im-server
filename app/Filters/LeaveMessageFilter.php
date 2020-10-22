<?php namespace App\Filters;

use Dingo\Api\Auth\Auth;
use EloquentFilter\ModelFilter;

class LeaveMessageFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function phone($phone)
    {
        return $this->where('phone',$phone);
    }

    public function  createdAt($params)
    {
        return $this->whereBetween('created_at',$params);
    }

}
