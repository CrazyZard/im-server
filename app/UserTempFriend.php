<?php

namespace App;

use App\Filters\FriendFilter;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Model;
class UserTempFriend extends Model
{
    public $timestamps = true;

    protected $fillable = ['student','teacher'];

    use Filterable;
    public function modelFilter()
    {
        return $this->provideFilter(FriendFilter::class);
    }


}
