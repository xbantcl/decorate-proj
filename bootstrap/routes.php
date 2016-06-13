<?php
/**
 * Routing of the application.
 *
 * @author Anxiaobo <xbantcl@gmail.com>
 * @date 2016-06-11
 */

// ******************************** Start Passport Api ************************
$app->group('/passport/v1/', function () {
    $this->get('user/login', 'Passport\Services\UserService:login');
});
// ******************************** End Passport Api **************************


// ******************************** Start Decorate Api ************************
$app->group('/decorate/v1/', function () {
    $this->post('diary/add', 'Decorate\Services\DiaryService:add');
});
// ******************************** End Decorate Api **************************