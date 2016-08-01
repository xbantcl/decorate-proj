<?php namespace Decorate\Services;

use Illuminate\Support\Facades\Request;
use Respect\Validation\Validator as v;
use Decorate\Utils\Help;
use Decorate\Modules\ShopModule;
use Decorate\Modules\WorksModule;

class WorksService extends Service
{
    /**
     * 添加作品.
     */
    public function add($req, $res)
    {
        $validation = $this->validation->validate($req, [
            'intr' => v::noWhitespace()->notEmpty(),
            'address' => v::noWhitespace()->notEmpty(),
            'bucket' => v::noWhitespace()
        ]);

        if ($validation->failed()) {
            return $validation->outputError($res);
        }
        $args = Help::getParams($req, $this->uid);

        if (isset($args['fileList'])) {
            $args['fileList'] = json_decode($args['fileList'], true);
        }
        return Help::response($res, WorksModule::getInstance()->add($args));
    }

    /**
     * 获取作品列表.
     * 
     * @param object $req
     * @param object $res
     */
    public function getList($req, $res)
    {
        $validation = $this->validation->validate($req, [
            'start' => v::optional(v::numeric()),
            'limit' => v::optional(v::numeric())
        ]);
        
        if ($validation->failed()) {
            return $validation->outputError($res);
        }
        $args = Help::getParams($req, $this->uid);
        $args['start'] = isset($args['start']) ? $args['start'] : 0;
        $args['limit'] = isset($args['limit']) ? $args['limit'] : 15;
        return Help::response($res, WorksModule::getInstance()->getList($args['uid'], $args['start'], $args['limit']));
    }
}
