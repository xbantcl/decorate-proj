<?php namespace Decorate\Middleware;

use Decorate\Auth\Auth;
use Passport\Modules\UserModule;
use Decorate\Utils\Help;

class ParamConvertMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        $args = $request->getParams();
        $response = $next($request, $response);
        return $response;
    }
}
