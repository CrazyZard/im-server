<?php namespace App\Filters;

use Dingo\Api\Auth\Auth;
use EloquentFilter\ModelFilter;

class PersonalRecordFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];


}
