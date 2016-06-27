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
            'decorate_progress' => v::numeric()->notEmpty(),
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $args = Help::getParams($request, $this->uid);
        exit;
        if (isset($args['fileIdList'])) {
            $fileIdList = json_decode($args['fileIdList']);
            unset($args['fileIdList']);
        }
        DB::beginTransaction();
        try {
            $ret = DiaryModule::getInstance()->add($args);
            if (!empty($fileIdList)) {
                DiaryModule::getInstance()->addFile($ret->id, $fileIdList);
            }
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage(), $e->getCode());
        }
        DB::commit();
        return Help::response($response, $ret);
    }

    /**
     * 添加装修日志.
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
        return DiaryModule::getInstance()->delDiaryById($args['diary_id']);
    }

    public function getDiaryList($request, $response)
    {
        $validation = $this->validation->validate($request, [
            'start' => v::notEmpty()->numeric(),
            'limit' => v::optional(v::numeric())
        ]);

        if ($validation->failed()) {
            return $validation->outputError($response);
        }
        $args = Help::getParams($request, $this->uid);
        $args['limit'] = isset($args['limit']) ? $args['limit'] : 15;
        return DiaryModule::getInstance()->getDiaryList(intval($args['start']), $args['limit']);
    }
}
