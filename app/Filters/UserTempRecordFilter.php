<?php namespace App\Filters;

use EloquentFilter\ModelFilter;

class UserTempRecordFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    protected $drop_id = false;

    protected function pid($pid){
        return $this->where('pid',$pid);
    }


}
