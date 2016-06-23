<?php
/**
 * Routing of the application.
 *
 * @author Anxiaobo <xbantcl@gmail.com>
 * @date 2016-06-11
 */

use Decorate\Middleware\AuthMiddleware;

$app->group('/decorate/v1/', function () {
    $this->post('diary/add', 'Decorate\Services\DiaryService:add');
    $this->post('diary/get', 'Decorate\Services\DiaryService:getDiaryDetailById');
    $this->post('token/get', 'Decorate\Services\TokenService:getUploadFileToken');
})->add(new AuthMiddleware($container));
