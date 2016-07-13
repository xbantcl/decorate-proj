<?php namespace Decorate\Redis;

/**
 * Discuss Redis
 */

class DiscussRedis extends BaseRedis implements CounterInterface
{
    use ItemCounter;

    protected static $instance = NULL;

    const PREFFIX = 'discuss#';

    /**
     * 实例化.
     * 
     * @param string $config
     * 
     * @return DiaryRedis
     */
    public static function getInstance($config = 'default')
    {
        if (NULL == static::$instance) {
            static::$instance = new self();
            static::$instance->setConfig($config);
        }
        return static::$instance;
    }

    public function getKey($dataId) {
        return self::PREFFIX . $dataId;
    }

    public function getColKey($dataId)
    {
        return self::PREFFIX . 'col#' . $dataId;
    }
}