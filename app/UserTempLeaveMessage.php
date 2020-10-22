<?php

namespace App;

use App\Filters\LeaveMessageFilter;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class UserTempLeaveMessage extends Model
{
    protected $guarded = [];
    public $timestamps = true;

    // use Filterable;

    // public function modelFilter()
    // {
    //     return $this->provideFilter(LeaveMessageFilter::class);
    // }
}
