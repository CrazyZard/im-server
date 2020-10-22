<?php
namespace App\Services\WebSocket\Conversation;

interface ConversationContract
{

    public const CONVERSATION_KEY = 'conversation:id';
    public const USER_KEY = 'uuid';
    public const DESCRIPTORS_KEY = 'fds';

    /**
     * 初始化
     *
     * @return ConversationContract
     */
    public function prepare(): ConversationContract;

    /**
     * 登录
     * @param int $fd
     * @param string $userId
     * @return mixed
     */
    public function login(int $fd, string $userId);

    /**
     * 退出
     * @param int $fd
     * @param $userId
     * @return mixed
     */
    public function logout(int $fd);

    /**
     * 获取用户信息
     * @param int $fd
     * @return mixed
     */
    public function getUserId(int $fd);


    /**
     * 退出
     * @param $userId
     * @return mixed
     */
    public function getFds($userId);
}
