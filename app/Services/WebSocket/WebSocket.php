<?php

namespace App\Services\WebSocket;

use App\Services\WebSocket\Conversation\ConversationContract;
use Illuminate\Support\Facades\App;
use Swoole\WebSocket\Server;

class WebSocket
{
    use Authenticatable;

    const PUSH_ACTION = 'push';
    const EVENT_CONNECT = 'connect';
    const USER_PREFIX = 'uid_';

    /**
     * Websocket Server
     * @var Server
     */
    protected $server;

    /**
     * Determine if to broadcast.
     *
     * @var boolean
     */
    protected $isBroadcast = false;

    /**
     * Scoket sender's fd.
     *
     * @var integer
     */
    protected $sender;

    /**
     * Recepient's fd or room name.
     *
     * @var array
     */
    protected $to = [];

    /**
     * Websocket event callbacks.
     *
     * @var array
     */
    protected $callbacks = [];


    protected $connection;


    protected $conversation;
    /**
     * DI Container.
     *
     * @var \Illuminate\Contracts\Container\Container
     */
    protected $container;

    /**
     * Websocket constructor.
     *
     * @param ConversationContract $conversation
     */
    public function __construct(ConversationContract $conversation)
    {
        $this->conversation = $conversation;
    }

    /**
     * Set broadcast to true.
     */
    public function broadcast(): self
    {
        $this->isBroadcast = true;

        return $this;
    }

    /**
     * Set multiple recipients fd or room names.
     *
     * @param integer, string, array
     *
     * @return $this
     */
    public function to($values): self
    {
        $this->to[] = $values;
        return $this;
    }

    /**
     * 发送信息
     * @param string $event
     * @param $data
     * @return bool
     */
    public function emit(string $event, $data): bool
    {
        $fds = $this->getFds();
        $assigned = !empty($this->to);
        if (empty($fds) && $assigned) {
            return false;
        }
        $payload = [
            'sender'    => $this->sender,
            'fds'       => $fds,
            'broadcast' => $this->isBroadcast,
            'assigned'  => $assigned,
            'event'     => $event,
            'message'   => $data,
        ];
        $server = app('swoole');
        $pusher = Pusher::make($payload, $server);
        $parser = app('swoole.parser');
        $pusher->push($parser->encode($pusher->getEvent(), $pusher->getMessage()));

        $this->reset();

        return true;
    }


    /**
     * Register an event name with a closure binding.
     *
     * @param string
     * @param callback
     *
     * @return $this
     */
    public function on(string $event, $callback)
    {
        if (!is_string($callback) && !is_callable($callback)) {
            throw new \InvalidArgumentException(
                'Invalid websocket callback. Must be a string or callable.'
            );
        }

        $this->callbacks[$event] = $callback;

        return $this;
    }

    /**
     * Check if this event name exists.
     *
     * @param string
     *
     * @return boolean
     */
    public function eventExists(string $event)
    {
        return array_key_exists($event, $this->callbacks);
    }

    /**
     * Execute callback function by its event name.
     *
     * @param string
     * @param mixed
     *
     * @return mixed
     */
    public function call(string $event, $data = null)
    {
        if (!$this->eventExists($event)) {
            return null;
        }

        // inject request param on connect event
        $isConnect = $event === static::EVENT_CONNECT;
        $dataKey = $isConnect ? 'request' : 'data';

        return App::call($this->callbacks[$event], [
            'websocket' => $this,
            $dataKey => $data,
        ]);
    }

    /**
     * Set sender fd.
     *
     * @param integer
     *
     * @return $this
     */
    public function setSender(int $fd)
    {
        $this->sender = $fd;

        return $this;
    }

    /**
     * Get current sender fd.
     */
    public function getSender()
    {
        return $this->sender;
    }



    /**
     * Get push destinations (fd or room name).
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * Get all fds we're going to push data to.
     */
    protected function getFds()
    {
        return array_values($this->to);
    }

    /**
     * Reset some data status.
     *
     * @param bool $force
     *
     * @return $this
     */
    public function reset($force = false)
    {
        $this->isBroadcast = false;
        $this->to = [];

        if ($force) {
            $this->sender = null;
            $this->userId = null;
        }

        return $this;
    }


}
