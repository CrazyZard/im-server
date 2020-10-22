<?php
namespace App\Services\WebSocket\Facades;

use App\Services\WebSocket\Conversation\ConversationContract;
use Illuminate\Support\Facades\Facade;

/**
 * @method static $this prepare()
 * @method static $this add($fd, $conversations)
 * @method static $this delete($fd, $conversations)
 * @method static array getClients($conversations)
 * @method static array getConversations($fd)
 * @method static array login($fd, $userId)
 * @method static array logout($fd, $conversationIds)
 * @method static array getUserId($fd)
 * @method static array getFds($userId)
 *
 * @see ConversationContract
 */
class Conversation extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'swoole.conversation';
    }
}
