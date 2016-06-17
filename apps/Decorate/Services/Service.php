<?php namespace Decorate\Services;

use Slim\Container;

class Service
{
    protected $container = null;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function __get($name) {
        return $this->container->get($name);
    }
}
