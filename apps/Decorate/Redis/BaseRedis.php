<?php namespace Decorate\Redis;

/**
 * Redis
 */

use Predis\Client;
use Slim\Container;

class BaseRedis
{
    private static $instance = null;
    private $container;

    public function __construct(Container $container, $config = 'default')
    {
        $this->container = $container;
        $config = $this->container->get();
        if (null == static::$instance) {
            static::$instance = new Client();
        }
    }
}