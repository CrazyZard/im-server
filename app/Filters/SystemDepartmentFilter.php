<?php namespace App\Filters;

use Dingo\Api\Auth\Auth;
use EloquentFilter\ModelFilter;

class SystemDepartmentFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = ['parameter'];

    protected $blacklist = [];

    public function fitter($column = ['id','pid','name']){
        return $this->select($column);
    }

    public function setup()
    {
        $this->fitter();
    }
}
