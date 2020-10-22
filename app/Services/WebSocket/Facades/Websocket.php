<?php
namespace App\Services\WebSocket\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static $this broadcast()
 * @method static $this to($values)
 * @method static boolean emit($event, $data)
 * @method static $this on($event, $callback)
 * @method static boolean eventExists($event)
 * @method static mixed call($event, $data)
 * @method static boolean close($fd)
 * @method static $this setSender($fd)
 * @method static int getSender()
 * @method static array getTo()
 * @method static $this reset()
 * @method static $this middleware($middleware)
 * @method static $this setContainer($container)
 * @method static $this setPipeline($pipeline)
 * @method static \Illuminate\Contracts\Pipeline\Pipeline getPipeline()
 * @method static mixed loginUsing($user)
 * @method static $this loginUsingId($userId)
 * @method static $this toUser($users)
 * @method static $this toUserId($userIds)
 * @method static string getUserId()
 * @method static boolean isUserIdOnline($userId)
 *
 * @see \App\Services\WebSocket\WebSocket
 */
class Websocket extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'swoole.websocket';
    }
}
