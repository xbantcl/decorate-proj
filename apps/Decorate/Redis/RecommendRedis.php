<?php namespace Decorate\Redis;

/**
 * Diary Redis
 */

class RecommendRedis extends BaseRedis implements CounterInterface
{
    use ItemCounter;

    protected static $instance = NULL;

    const PREFFIX = 'recommend#';

    /**
     * 实例化.
     *
     * @param string $config
     *
     * @return RecommendRedis
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

}