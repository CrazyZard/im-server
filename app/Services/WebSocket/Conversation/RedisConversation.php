<?php
namespace App\Services\WebSocket\Conversation;


use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Predis\Client as RedisClient;
use Predis\Pipeline\Pipeline;


class RedisConversation implements ConversationContract
{

    protected $redis;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var string
     */
    protected $prefix = 'conversion:';

    /**
     * RedisRoom constructor.
     *
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }


    /**
     * @param RedisClient|null $redis
     * @return $this|ConversationContract
     */
    public function prepare(RedisClient $redis = null): ConversationContract
    {
        $this->setRedis($redis);
        $this->setPrefix();
        $this->cleanConversations();
        return $this;
    }


    /**
     * @param RedisClient|null $redis
     */
    public function setRedis(?RedisClient $redis = null)
    {
        if (!$redis) {
            $server = Arr::get($this->config, 'server', []);
            $options = Arr::get($this->config, 'options', []);
            if (Arr::has($options, 'prefix')) {
                $options = Arr::except($options, 'prefix');
            }
            $redis = new RedisClient($server, $options);
        }
        $this->redis = $redis;
    }

    /**
     * 设置前置
     */

    protected function setPrefix()
    {
        if ($prefix = Arr::get($this->config, 'prefix')) {
            $this->prefix = $prefix;
        }
    }

    /**
     * 描述符添加至会话组中
     * @param int $fd
     * @param $conversations
     * @return mixed|void
     */
    public function add(int $fd, $conversations)
    {
        $conversations = is_array($conversations) ? $conversations : [$conversations];

        $this->addValue($fd, $conversations, ConversationContract::DESCRIPTORS_KEY);

        foreach ($conversations as $conversation) {
            $this->addValue($conversation, [$fd], ConversationContract::CONVERSATION_KEY);
        }
    }


    /**
     * 批量删除会话内用户
     * @param int $fd
     * @param $conversationId
     * @return mixed|void
     */
    public function delete($fd, $conversationId)
    {
//        $conversions = is_array($conversationId) ? $conversationId : [$conversationId];
//        $conversions = count($conversions) ? $conversions : $this->getConversations($fd);
//
//        $this->removeValue($fd, $conversions, ConversationContract::DESCRIPTORS_KEY);
//
//        foreach ($conversions as $conversion) {
//            $this->removeValue($conversion, [$fd], ConversationContract::CONVERSATION_KEY);
//        }
    }


    /**
     * 添加值
     * @param $key
     * @param  $values
     * @param string $table
     * @return $this
     */
    public function addValue($key, $values, string $table)
    {
        $this->checkTable($table);
        $redisKey = $this->getKey($key, $table);
        Log::debug('---addValue---'.$redisKey);
        if(!is_array($values)){
            $this->redis->set($redisKey, $values);
        }else {
            $this->redis->pipeline(function ($pipe) use ($redisKey, $values) {
                foreach ($values as $value) {
                    $pipe->sadd($redisKey, $value);
                }
            });
        }
        return $this;
    }

    /**
     * @param $key
     * @param string $values
     * @param string $table
     * @return $this
     */

    public function addStringValue($key, string $values, string $table)
    {
        $this->checkTable($table);
        $redisKey = $this->getKey($key, $table);
        $this->redis->set($redisKey, $values);
        return $this;
    }

    /**
     * 删除值
     * @param $key
     * @param $values
     * @param string $table
     * @return $this
     */
    public function removeValue($key, $values, string $table)
    {
        $this->checkTable($table);
        $redisKey = $this->getKey($key, $table);
        $this->redis->pipeline(function (Pipeline $pipe) use ($redisKey, $values) {
            foreach ($values as $value) {
                $pipe->srem($redisKey, $value);
            }
        });
        return $this;
    }


    /**
     * 删除值
     * @param $key
     * @param $values
     * @param string $table
     * @return $this
     */
    public function removeStringValue($key, $values, string $table)
    {
        $this->checkTable($table);
        $redisKey = $this->getKey($key, $table);
        $this->redis->del($redisKey, $values);
        return $this;
    }

    /**
     * 通过会话获取所有fd
     * @param string $conversation
     * @return array|mixed
     */
    public function getClients(string $conversation)
    {
        return $this->getValue($conversation, ConversationContract::CONVERSATION_KEY) ?? [];
    }

    /**
     * 通过fd获取所有的会话列表
     * @param int $fd
     * @return array
     */

    public function getConversations(int $fd)
    {
        return $this->getValue($fd, ConversationContract::DESCRIPTORS_KEY) ?? [];
    }

    /**
     * 检查会话和文件描述符。
     * @param string $table
     */

    protected function checkTable(string $table)
    {
        if (!in_array($table, [
            ConversationContract::CONVERSATION_KEY,
            ConversationContract::DESCRIPTORS_KEY,
            ConversationContract::USER_KEY
        ])) {
            throw new \InvalidArgumentException("Invalid table name: `{$table}`.");
        }
    }


    /**
     * 取值
     * @param string $key
     * @param string $table
     * @return array
     */
    public function getValue(string $key, string $table)
    {
        $this->checkTable($table);

        $result = $this->redis->smembers($this->getKey($key, $table));

        return is_array($result) ? $result : [];
    }

    /**
     * 取值
     * @param string $key
     * @param string $table
     * @return array
     */
    public function getStringValue(string $key, string $table)
    {
        $this->checkTable($table);
        Log::debug('table'."{$this->prefix}{$table}:{$key}");
        Log::debug('value'.$this->redis->get($this->getKey($key, $table)));
        return  $this->redis->get($this->getKey($key, $table));
    }


    /**
     * 拼凑值
     * @param string $key
     * @param string $table
     * @return string
     */

    public function getKey(string $key, string $table)
    {
        return "{$this->prefix}{$table}:{$key}";
    }


    /**
     * 清楚所有的会话相关缓存
     */

    protected function cleanConversations(): void
    {
        if (count($keys = $this->redis->keys("{$this->prefix}*"))) {
            $this->redis->del($keys);
        }
    }

    public function login(int $fd, $userId)
    {
        $this->addStringValue($fd, $userId, ConversationContract::DESCRIPTORS_KEY);
        $this->addStringValue($userId, $fd, ConversationContract::USER_KEY);
    }


    public function logout(int $fd)
    {
        //删除fd对应的用户
        $userId  = $this->getStringValue($fd, ConversationContract::DESCRIPTORS_KEY);
        if($userId){
            $this->removeStringValue($fd, $userId, ConversationContract::DESCRIPTORS_KEY);
            $this->removeStringValue($userId, $fd, ConversationContract::CONVERSATION_KEY);
        }
    }

    public function getUserId(int $fd)
    {
        return $this->getStringValue($fd, ConversationContract::DESCRIPTORS_KEY) ?? '';
    }

    public function getFds($userId)
    {
        return $this->getStringValue($userId,ConversationContract::USER_KEY) ?? '';
    }
}
