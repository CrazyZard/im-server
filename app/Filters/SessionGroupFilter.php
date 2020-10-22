<?php namespace App\Filters;

use EloquentFilter\ModelFilter;

class SessionGroupFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    protected $drop_id = false;

    public function name($name){
        return $this->where('name',$name);
    }

    public function groupId($group)
    {
        return $this->where('group_id',$group);
    }
}
