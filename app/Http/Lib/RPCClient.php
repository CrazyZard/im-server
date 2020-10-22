<?php
namespace App\Http\Lib;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use PhpAmqpLib\Message\AMQPMessage;

class RPCClient
{
    private $connection;
    private $channel;
    private $response;
    private $corr_id;
    private $reply_queue;


    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(env('RABBITMQ_HOST'),env('RABBITMQ_PORT'),env('RABBITMQ_LOGIN'),env('RABBITMQ_PASSWORD'));
        $this->channel = $this->connection->channel();
        list($this->reply_queue, ,) = $this->channel->queue_declare(
            "", false, false, true, false);
        $this->channel->basic_consume(
            $this->reply_queue, '', false, false, false, false,
            array($this, 'on_response'));

    }

    public function on_response($rep) {
        if($rep->get('correlation_id') == $this->corr_id) {
            $this->response = $rep->body;
        }
    }


    public function call($list,$routing_key = 'auto_create_queue')
    {
        if(is_array($list)){
            $list = json_encode($list);
        }
        // 建立TCP连接
        $this->corr_id = uniqid();
        try{
            $this->response = null;
            $msg = new AMQPMessage(
                (string) $list,
                [
                    'correlation_id' => $this->corr_id,
                    'reply_to' => $this->reply_queue,
                ]
            );
            $this->channel->basic_publish($msg, '', $routing_key);
            while(!$this->response) {
                $this->channel->wait(null,false,30);
            }
            return $this->response;
        }catch (AMQPTimeoutException $exception) {
            return '网络超时！请联系管理员';
        }catch(\Exception $exception){
            return $exception->getMessage();
        }
        // 断开连接
        $this->connection->disconnect();
        return $this->msg ;
    }
}