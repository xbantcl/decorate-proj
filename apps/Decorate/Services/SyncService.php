<?php namespace Decorate\Services;

use Illuminate\Support\Facades\Request;
use Decorate\Modules\CollectionModule;
use Respect\Validation\Validator as v;
use Decorate\Utils\Help;
use Decorate\Modules\SyncModule;

class SyncService extends Service
{
    /**
     * 基础数据同步接口.
     * 
     * @param object $req
     * @param object $res
     */
    public function getBasicData($req, $res)
    {
        $data = [];
        $data['areaTree'] = SyncModule::getInstance()->getAreaTree();
        return Help::response($res, $data);
    }

    /**
     * 获取同步数据版本号.
     * 
     * @param object $req
     * @param object $res
     */
    public function getVersion($req, $res)
    {
        return Help::response($res, SyncModule::getInstance()->getVersion());
    }
}
