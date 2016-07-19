<?php namespace Decorate\Services;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Facades\Request;
use Decorate\Modules\DiaryModule;
use Decorate\Models\Diary;
use Respect\Validation\Validator as v;
use Decorate\Redis\UserRedis;
use Decorate\Utils\Help;

class DiaryService extends Service
{
    /**
     * 添加装修日志.
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
            'decorate_progress' => v::intVal(),
            'label_id' => v::numeric(),
            'add_time' => v::intVal()->notEmpty()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $args = Help::getParams($request, $this->uid);
        if (isset($args['fileList'])) {
            $args['fileList'] = json_decode($args['fileList'], true);
        }

        return Help::response($response, DiaryModule::getInstance()->add($args));
    }

    /**
     * 获取装修日志详情.
     *
     * @param object $request
     * @param object $response
     *
     * @return json
     */
    public function getDiaryDetailById($request, $response)
    {
        $validation = $this->validation->validate($request, [
            'diary_id' => v::numeric()->notEmpty(),
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $args = Help::getParams($request);
        $ret = DiaryModule::getInstance()->getDiaryDetailById($args['diary_id']);
        return Help::response($response, $ret);
    }

    public function delDiaryById($request, $response)
    {
        $validation = $this->validation->validate($request, [
            'diary_id' => v::numeric()->notEmpty(),
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $args = Help::getParams($request, $this->uid);
        return Help::response($response, DiaryModule::getInstance()->delDiaryById($args['diary_id']));
    }

    public function getDiaryList($request, $response)
    {
        $validation = $this->validation->validate($request, [
            'start' => v::numeric(),
            'limit' => v::optional(v::numeric())
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $args = Help::getParams($request, $this->uid);
        $args['limit'] = isset($args['limit']) ? $args['limit'] : 15;
        return Help::response($response, DiaryModule::getInstance()->getDiaryList($args['uid'], intval($args['start']), $args['limit']));
    }

    public function getLabelTree($request, $response)
    {
        return Help::response($response, DiaryModule::getInstance()->getLabelTree());
    }

    public function commentDiary($request, $response)
    {
        $validation = $this->validation->validate($request, [
            'diary_id' => v::numeric(),
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
        return Help::response($response, DiaryModule::getInstance()->commentDiary($args));
    }

    public function getUserDiaryList($req, $res)
    {
        $validation = $this->validation->validate($req, [
            'uid' => v::intVal()->notEmpty(),
        ]);
        
        if ($validation->failed()) {
            return $validation->outputError($res);
        }
        $args = Help::getParams($req);
        return Help::response($res, DiaryModule::getInstance()->getUserDiaryList($args['uid']));
    }

    public function getDiaryCommentList($req, $res)
    {
        $validation = $this->validation->validate($req, [
            'diary_id' => v::intVal()->notEmpty(),
            'start' => v::optional(v::numeric()),
            'limit' => v::optional(v::numeric())
        ]);
        
        if ($validation->failed()) {
            return $validation->outputError($res);
        }
        $args['start'] = isset($args['start']) ? $args['start'] : 0;
        $args['limit'] = isset($args['limit']) ? $args['limit'] : 15;
        $args = Help::getParams($req);
        return Help::response($res, DiaryModule::getInstance()->getDiaryCommentList($args['diary_id'], $args['start'], $args['limit']));
    }
}
