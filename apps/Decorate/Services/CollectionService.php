<?php namespace Decorate\Services;

use Illuminate\Support\Facades\Request;
use Decorate\Modules\CollectionModule;
use Respect\Validation\Validator as v;
use Decorate\Utils\Help;

class CollectionService extends Service
{
    /**
     * 添加收藏.
     *
     * @return array
     */
    public function add($req, $res)
    {
        $validation = $this->validation->validate($req, [
            'data_id' => v::intVal()->notEmpty(),
            'type' => v::intVal()->notEmpty(),
        ]);
        
        if ($validation->failed()) {
            return $validation->outputError($res);
        }
        $args = Help::getParams($req, $this->uid);
        return Help::response($res, CollectionModule::getInstance()->add($args));
    }

    /**
     * 获取收藏列表.
     * 
     * @param object $req
     * @param object $res
     */
    public function getList($req, $res)
    {
        $validation = $this->validation->validate($req, [
            'type' => v::intVal()->notEmpty(),
            'start' => v::numeric(),
            'limit' => v::optional(v::numeric())
        ]);
        
        if ($validation->failed()) {
            return $validation->outputError($res);
        }
        $args = Help::getParams($req, $this->uid);
        $args['start'] = isset($args['start']) ? $args['start'] : 0;
        $args['limit'] = isset($args['limit']) ? $args['limit'] : 15;
        return Help::response($res, CollectionModule::getInstance()->getList($args['uid'], $args['type'], $args['start'], $args['limit']));
    }
}
