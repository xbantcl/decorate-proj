<?php namespace Passport\Middleware;

use Passport\Auth\Auth;
use Passport\Modules\UserModule;
use Passport\Utils\Help;

class AuthMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        $args = $request->getParams();
        $sess = isset($args['sess']) ? $args['sess'] : '';
        $platform = isset($args['cli_p']) ? $args['cli_p'] : 'app';
        $data = UserModule::getInstance()->checkLogin($sess, $platform);
        if (isset($data['code'])) {
            return Help::response($response, null, $data['code'], $data['message']);
        }
        $this->container['uid'] = intval($data);
        $response = $next($request, $response);
        return $response;
    }
}
