<?php namespace App\Filters;

use Dingo\Api\Auth\Auth;
use EloquentFilter\ModelFilter;

class SystemLogFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];


    protected $drop_id = false;

    public function createdAt($params)
    {
        return $this->whereBetween('created_at',$params);
    }


    public function userId($params)
    {
        return $this->where('user_id',$params);
    }


}
