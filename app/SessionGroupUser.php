<?php

namespace App;

use App\Filters\SessionGroupFilter;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class SessionGroupUser extends Model
{
    public $timestamps = false;

    protected $fillable = ['group_id','user_id'];

    use Filterable;

    public function modelFilter()
    {
        return $this->provideFilter(SessionGroupFilter::class);
    }

    public function user(){
        return $this->hasOne('App\SystemUser','id','user_id');
    }


}
