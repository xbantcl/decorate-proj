<?php
/**
 * Routing of the application.
 *
 * @author Anxiaobo <xbantcl@gmail.com>
 * @date 2016-06-11
 */

use Decorate\Middleware\AuthMiddleware;
use Decorate\Middleware\ParamConvertMiddleware;

$app->group('/decorate/v1/', function () use ($container) {
    $this->post('diary/add', 'Decorate\Services\DiaryService:add')->add(new ParamConvertMiddleware($container));
    $this->post('diary/get', 'Decorate\Services\DiaryService:getDiaryDetailById');
    $this->post('diary/list', 'Decorate\Services\DiaryService:getDiaryList');
    $this->post('token/get', 'Decorate\Services\TokenService:getUploadFileToken');
})->add(new AuthMiddleware($container));
