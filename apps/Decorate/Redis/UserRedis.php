<?php namespace Decorate\Redis;

/**
 * User Redis
 */

class UserRedis extends BaseRedis
{
    public static function getInstance($config = 'default')
    {
        $userRedisInstance = new self();
        $userRedisInstance->setConfig($config);
        return $userRedisInstance;
    }
}