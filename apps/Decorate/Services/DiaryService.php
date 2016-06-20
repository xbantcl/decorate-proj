<?php namespace Decorate\Services;

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Facades\Request;
use Decorate\Modules\DiaryModule;
use Decorate\Models\Diary;
use Respect\Validation\Validator as v;
use Decorate\Redis\UserRedis;

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
        $args = $request->getParams();
        $validation = $this->validation->validate($request, [
            'content' => v::noWhitespace()->notEmpty(),
            'decorate_progress' => v::numeric()->notEmpty(),
        ]);
        if ($validation->failed()) {
            return $validation->outputError($response);
        }
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
        return $response->write(json_encode(
            [
                'error_code' => 0,
                'message' => 'success',
                'data' => $ret
            ]
        ));
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
        $args = $request->getParams();
        $ret = DiaryModule::getInstance()->getDiaryDetailById($args['diary_id']);
        return $response->write(json_encode(
            [
                'error_code' => 0,
                'message' => 'success',
                'data' => $ret
            ]
        ));
    }
}
