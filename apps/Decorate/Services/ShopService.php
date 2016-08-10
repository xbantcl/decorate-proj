<?php namespace Decorate\Services;

use Illuminate\Support\Facades\Request;
use Respect\Validation\Validator as v;
use Decorate\Utils\Help;
use Decorate\Modules\ShopModule;

class ShopService extends Service
{
    /**
     * 添加商铺.
     */
    public function add($req, $res)
    {
        $validation = $this->validation->validate($req, [
            'name' => v::noWhitespace()->notEmpty(),
            'avatar' => v::noWhitespace(),
            'area_id' => v::intVal()->notEmpty(),
            'longitude' => v::floatVal()->notEmpty(),
            'latitude' => v::floatVal()->notEmpty(),
            'manager' => v::noWhitespace()->notEmpty(),
            'business' => v::noWhitespace()->notEmpty(),
            'address' => v::noWhitespace()->notEmpty(),
            'region' => v::noWhitespace()->notEmpty(),
        ]);

        if ($validation->failed()) {
            return $validation->outputError($res);
        }
        $args = Help::getParams($req, $this->uid);
        return Help::response($res, ShopModule::getInstance()->add($args));
    }

    /**
     * 获取用户商铺列表.
     * 
     * @param object $req
     * @param object $res
     */
    public function getListByUserId($req, $res)
    {
        return Help::response($res, ShopModule::getInstance()->getListByUserId($this->uid));
    }

    /**
     * 获取商铺列表.
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
        $args = Help::getParams($req);
        $args['start'] = isset($args['start']) ? $args['start'] : 0;
        $args['limit'] = isset($args['limit']) ? $args['limit'] : 15;
        return Help::response($res, ShopModule::getInstance()->getList($args['start'], $args['limit']));
    }
}
