<?php namespace Decorate\Middleware;

class AuthMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        //print_r($this->container->get('router'));
        $response = $next($request, $response);
        return $response;
    }
}
