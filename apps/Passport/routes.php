<?php
/**
 * Routing of the application.
 *
 * @author Anxiaobo <xbantcl@gmail.com>
 * @date 2016-06-11
 */
 
use Passport\Middleware\AuthMiddleware;

// ******************************** Start Passport Api ************************
$app->group('/passport/v1/', function () {
    $this->post('user/login', 'Passport\Services\UserService:login');
    $this->post('user/register', 'Passport\Services\UserService:register');
});

$app->group('/passport/v1/', function () {
    $this->post('user/get', 'Passport\Services\UserService:getUserInfo');
    $this->post('user/update', 'Passport\Services\UserService:updateUserInfo');
    $this->post('user/password/update', 'Passport\Services\UserService:updatePassword');
})->add(new AuthMiddleware($container));