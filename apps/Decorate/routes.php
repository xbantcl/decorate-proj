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
    $this->post('diary/comment/add', 'Decorate\Services\DiaryService:commentDiary');
    $this->post('diary/comment/list', 'Decorate\Services\DiaryService:getDiaryCommentList');
    // ------------------ 文件token接口 ---------------------------------
    $this->post('token/get', 'Decorate\Services\TokenService:getUploadFileToken');
    $this->post('label/tree', 'Decorate\Services\DiaryService:getLabelTree');
    // ------------------ 建材讨论接口 ----------------------------------
    $this->post('discuss/add', 'Decorate\Services\DiscussService:add')->add(new ParamConvertMiddleware($container));
    $this->post('discuss/get', 'Decorate\Services\DiscussService:getDiscussDetailById');
    $this->post('discuss/list', 'Decorate\Services\DiscussService:getDiscussList');
    $this->post('discuss/comment/add', 'Decorate\Services\DiscussService:commentDiscuss');
    $this->post('discuss/comment/list', 'Decorate\Services\DiscussService:getDiscussCommentList');
    // ------------------ 收藏接口 --------------------------------------
    $this->post('collection/add', 'Decorate\Services\CollectionService:add');
    $this->post('collection/list', 'Decorate\Services\CollectionService:getList');
    $this->post('collection/delete', 'Decorate\Services\CollectionService:delete');
    // ------------------ 同步接口 --------------------------------------
    $this->post('data/sync', 'Decorate\Services\SyncService:getBasicData');
    // ------------------ 添加商铺 --------------------------------------
    $this->post('shop/add', 'Decorate\Services\ShopService::add');
    $this->post('shop/list', 'Decorate\Services\ShopService::getList');
})->add(new AuthMiddleware($container));
