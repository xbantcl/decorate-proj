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
        ]);

        if ($validation->failed()) {
            return $validation->outputError($res);
        }
        $args = Help::getParams($req, $this->uid);
        return Help::response($res, ShopModule::getInstance()->add($args));
    }

    /**
     * 获取商铺列表.
     * 
     * @param object $req
     * @param object $res
     */
    public function getList($req, $res)
    {
        return Help::response($res, ShopModule::getInstance()->getList($this->uid));
    }
}
