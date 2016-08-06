<?php namespace Decorate\Services;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Facades\Request;
use Decorate\Modules\DiscussModule;
use Decorate\Models\Discuss;
use Respect\Validation\Validator as v;
use Decorate\Redis\UserRedis;
use Decorate\Utils\Help;

class DiscussService extends Service
{

    /**
     * 发布问题.
     *
     * @param object $request
     * @param object $response
     *
     * @return json
     */
    public function add($request, $response)
    {
        $validation = $this->validation->validate($request, [
            'content' => v::noWhitespace()->notEmpty(),
            'label_id' => v::numeric(),
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $args = Help::getParams($request, $this->uid);
        if (isset($args['fileList'])) {
            $args['fileList'] = json_decode($args['fileList'], true);
        }

        return Help::response($response, DiscussModule::getInstance()->add($args));
    }

    /**
     * 获取讨论详情.
     *
     * @param object $request
     * @param object $response
     *
     * @return json
     */
    public function getDiscussDetailById($request, $response)
    {
        $validation = $this->validation->validate($request, [
            'discuss_id' => v::numeric()->notEmpty(),
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $args = Help::getParams($request);
        $ret = DiscussModule::getInstance()->getDiscussDetailById($args['discuss_id']);
        return Help::response($response, $ret);
    }

    public function delDiscussById($request, $response)
    {
        $validation = $this->validation->validate($request, [
            'discuss_id' => v::numeric()->notEmpty(),
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $args = Help::getParams($request, $this->uid);
        return Help::response($response, DiscussModule::getInstance()->delDiaryById($args['discuss_id']));
    }

    public function getDiscussList($request, $response)
    {
        $validation = $this->validation->validate($request, [
            'start' => v::numeric(),
            'limit' => v::optional(v::numeric())
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $args = Help::getParams($request);
        $args['limit'] = isset($args['limit']) ? $args['limit'] : 15;
        return Help::response($response, DiscussModule::getInstance()->getDiscussList(intval($args['start']), $args['limit']));
    }

    public function commentDiscuss($request, $response)
    {
        $validation = $this->validation->validate($request, [
            'discuss_id' => v::numeric(),
            'content' => v::noWhitespace()->notEmpty(),
            'parent_id' => v::optional(v::numeric()),
            'target_uid' => v::optional(v::numeric()),
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }

        $args = Help::getParams($request, $this->uid);
        if (isset($args['fileList'])) {
            $args['fileList'] = json_decode($args['fileList'], true);
        }
        $args['parent_id'] = isset($args['parent_id']) ? $args['parent_id'] : 0;
        $args['target_uid'] = isset($args['target_uid']) ? $args['target_uid'] : 0;
        return Help::response($response, DiscussModule::getInstance()->commentDiscuss($args));
    }

    public function getDiscussCommentList($req, $res)
    {
        $validation = $this->validation->validate($req, [
            'discuss_id' => v::intVal()->notEmpty(),
            'start' => v::optional(v::numeric()),
            'limit' => v::optional(v::numeric())
        ]);
        
        if ($validation->failed()) {
            return $validation->outputError($res);
        }
        $args['start'] = isset($args['start']) ? $args['start'] : 0;
        $args['limit'] = isset($args['limit']) ? $args['limit'] : 15;
        $args = Help::getParams($req);
        return Help::response($res, DiscussModule::getInstance()->getDiscussCommentList($args['discuss_id'], $args['start'], $args['limit']));
    }
}
