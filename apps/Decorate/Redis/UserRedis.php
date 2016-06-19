<?php namespace Decorate\Redis;

/**
 * User Redis
 */

class UserRedis extends BaseRedis
{
    public static function getInstance($config = 'default')
    {
        $instance = new self();
        $instance->setConfig($config);
        return $instance;
    }
}