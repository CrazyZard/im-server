<?php namespace App\Filters;

use Dingo\Api\Auth\Auth;
use EloquentFilter\ModelFilter;

class FriendFilter extends ModelFilter
{
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];

    public function accid($accid){
        return $this->where('accid',$accid);
    }

    public function faccid($accid){
        return $this->where('faccid',$accid);
    }

}
