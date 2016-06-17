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
})->add(new AuthMiddleware($container));
