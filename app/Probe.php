<?php

namespace App;

use App\Filters\ProbeFilter;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class Probe extends Model
{
    protected $guarded = [];

    use Filterable;

    public function modelFilter()
    {
        return $this->provideFilter(ProbeFilter::class);
    }

    public function group()
    {
        return $this->hasOne('App\SessionGroup','id','group_id');
    }
}
