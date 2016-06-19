<?php namespace Decorate\Redis;

/**
 * Redis
 */

use Predis\Client;
use Slim\Container;

class BaseRedis
{
    private static $instance = [];
    private $container;
    protected $config = null;

    public function __construct1(Container $container, $config = 'default')
    {
        $this->container = $container;
        $this->config = $config;
    }

    private function connect()
    {
        $option = $config = $this->container->get('redis')[$this->config];
        return new Client($option);
    }

    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function __call($method, $args) {
        if (empty(static::$instance[$this->config]) || ! static::$instance[$this->config] instanceof Client) {
            static::$instance[$this->config] = $this->connect();
        }
        return call_user_func_array([static::$instance[$this->config], $method], $args);
    }
}