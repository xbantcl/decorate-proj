<?php namespace Decorate\Middleware;

use Decorate\Auth\Auth;
use Passport\Modules\UserModule;
use Decorate\Utils\Help;
class AuthMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        $args = $request->getParams();
        $sess = isset($args['sess']) ? $args['sess'] : '';
        $platform = isset($args['sys_p']) ? $args['sys_p'] : 'app';
        $data = UserModule::getInstance()->checkLogin($sess, $platform);
        if (isset($data['code'])) {
            return Help::response($response, null, $data['code'], $data['message']);
        }
        $this->container['uid'] = intval($data);
        $response = $next($request, $response);
        return $response;
    }
}
