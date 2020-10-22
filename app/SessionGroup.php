<?php

namespace App;

use App\Filters\SessionGroupFilter;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;

class SessionGroup extends Model
{
    public $timestamps = true;

    protected $guarded = [

    ];

    use Filterable;


    public function modelFilter()
    {
        return $this->provideFilter(SessionGroupFilter::class);
    }

}
