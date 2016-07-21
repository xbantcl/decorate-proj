<?php namespace Decorate\Services;

use Illuminate\Support\Facades\Request;
use Respect\Validation\Validator as v;
use Decorate\Utils\Help;
use Decorate\Modules\ShopModule;
use Decorate\Modules\WorksModule;
use Decorate\Modules\HomeModule;

class HomeService extends Service
{
    /**
     * 获取推荐作品列表.
     * 
     * @param object $req
     * @param object $res
     */
    public function getRecommendList($req, $res)
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
        return Help::response($res, HomeModule::getInstance()->getRecommendList($args['start'], $args['limit']));
    }

    public function read($req, $res)
    {
        $validation = $this->validation->validate($req, [
            'data_id' => v::intVal()->notEmpty(),
        ]);
        
        if ($validation->failed()) {
            return $validation->outputError($res);
        }
        $args = Help::getParams($req, $this->uid);
        return Help::response($res, HomeModule::getInstance()->read($args['data_id']));
    }
}
