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
    // ------------------ 装修日记接口 ----------------------------------
    $this->post('diary/add', 'Decorate\Services\DiaryService:add')->add(new ParamConvertMiddleware($container));
    $this->post('diary/get', 'Decorate\Services\DiaryService:getDiaryDetailById');
    $this->post('diary/list', 'Decorate\Services\DiaryService:getDiaryList');
    $this->post('diary/user/list', 'Decorate\Services\DiaryService:getUserDiaryList');
    // ------------------ 日记评论接口 ----------------------------------
    $this->post('diary/comment/add', 'Decorate\Services\DiaryService:commentDiary');
    $this->post('diary/comment/list', 'Decorate\Services\DiaryService:getDiaryCommentList');
    // ------------------ 文件token接口 ---------------------------------
    $this->post('token/get', 'Decorate\Services\TokenService:getUploadFileToken');
    $this->post('label/tree', 'Decorate\Services\DiaryService:getLabelTree');
})->add(new AuthMiddleware($container));
