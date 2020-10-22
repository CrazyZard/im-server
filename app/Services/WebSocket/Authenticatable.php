<?php

namespace App\Services\WebSocket;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

/**
 * Trait Authenticatable
 */
trait Authenticatable
{
    protected $userId;

    /**
     * 登录
     * @param AuthenticatableContract $user
     * @return mixed
     *
     */
    public function loginUsing(AuthenticatableContract $user)
    {
        return $this->loginUsingId($user->uuid);
    }

    /**
     * Login using current userId.
     *
     * @param $userId
     *
     * @return mixed
     */
    public function loginUsingId($userId)
    {
        $this->conversation->login($this->sender,$userId);
        return $this;
    }

    /**
     * 退出
     * @return $this
     */
    public function logoutUsingId()
    {
        $this->conversation->logout($this->sender);
        return $this;
    }

    /**
     *
     *
     * @param $userIds
     *
     * @return Authenticatable
     */
    public function toUserId($userIds)
    {
        $fds =  $this->conversation->getFds($userIds);
        Log::info('toUserId:fds',[
            '$userIds'=>$userIds,
            'fds' => $fds,
            'to'=>$this->to
        ]);
        if($fds){
            $this->to($fds);
            $this->isBroadcast = true;
        }
        return $this;
    }


    /**
     * Get current auth user id by sender's fd.
     */
    public function getUserId()
    {
        if (!is_null($this->userId)) {
            return $this->userId;
        }
        $this->userId = $this->conversation->getUserId($this->sender);
        return $this->userId;
    }


    /**
     * Check if a user is online by given userId.
     *
     * @param $userId
     *
     * @return bool
     */
    public function isUserIdOnline($userId)
    {
        return !empty($this->room->getClients(static::USER_PREFIX . $userId));
    }

    /**
     * Check if user object implements AuthenticatableContract.
     *
     * @param $user
     */
    protected function checkUser($user)
    {
        if (!$user instanceof AuthenticatableContract) {
            throw new InvalidArgumentException('user object must implement ' . AuthenticatableContract::class);
        }
    }


}
