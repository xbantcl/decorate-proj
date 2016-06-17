<?php namespace Decorate\Middleware;

/**
* Base Middleware.
*/

use Slim\Container;

class Middleware
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }
}
