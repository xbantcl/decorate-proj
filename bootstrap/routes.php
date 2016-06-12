<?php
/**
 * Routing of the application.
 *
 * @author Anxiaobo <xbantcl@gmail.com>
 * @date 2016-06-11
 */

// ******************************** Start Passport Api ************************
$app->group('/decorate/v1/', function () {
    $app->get('users/login', 'Passport\Services\UserService:login');
});
// ******************************** End Passport Api **************************
