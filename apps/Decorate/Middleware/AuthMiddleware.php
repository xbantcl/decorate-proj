<?php namespace Decorate\Middleware;

use Decorate\Auth\Auth;
use Passport\Modules\UserModule;
use Decorate\Utils\Help;
class AuthMiddleware extends Middleware
{
    public function __invoke($request, $response, $next)
    {
        // $auth = new Auth();
        // $auth->check();
        $args = $request->getParams();
        $sess = isset($args['sess']) ? $args['sess'] : '';
        $data = UserModule::getInstance()->checkLogin($sess, $args['sys_p']);
        if (isset($data['code'])) {
            return Help::response($response, null, $data['code'], $data['message']);
        }
        $this->container['uid'] = intval($data);
        $response = $next($request, $response);
        return $response;
    }
}
