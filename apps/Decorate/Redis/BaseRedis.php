<?php namespace Decorate\Redis;

/**
 * Redis
 */

use Predis\Client;
use Slim\Container;

class BaseRedis
{
    protected static $instance = [];
    protected $config = null;
    protected $redisPath = '';

    public function __construct($config = 'default')
    {
        $this->config = $config;
        $this->redisPath = __DIR__ . '/../config.php';
    }

    private function connect()
    {
        return new Client($this->getConfig());
    }

    public function getConfig()
    {
        $options = [];
        if (file_exists($this->redisPath)) {
            $configs = require __DIR__ . '/../config.php';
            if (isset($configs[$this->config])) {
                $options = $configs[$this->config];
            }
        }
        return $options;
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