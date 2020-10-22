<?php
namespace App\Events;

use App\UserTempRecord;
use Carbon\Carbon;
use Hhxsv5\LaravelS\Swoole\Task\Event;

class MessageReceived extends Event
{
    private $message;
    private $userId;

    /**
     * Create a new event instance.
     */
    public function __construct($message, $userId = 0)
    {
        $this->message = $message;
        $this->userId = $userId;
    }

    /**
     * Get the message data
     *
     * return App\Message
     */
    public function getData()
    {
        $model = new UserTempRecord();
        $model->pid = $this->message->pid;
        $model->from = $this->message->from ;
        $model->to = $this->message->to;
        $model->context = $this->message->context;
        $model->created_at = Carbon::now();
        return $model;
    }
}
