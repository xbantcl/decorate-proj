<?php
/**
 * Routing of the application.
 *
 * @author Anxiaobo <xbantcl@gmail.com>
 * @date 2016-06-11
 */


// ******************************** Start Passport Api ************************
$app->group('/passport/v1/', function () {
    $this->post('user/login', 'Passport\Services\UserService:login');
});