<?php namespace Decorate\Modules;
/**
 * ShopModule class.
 *
 * @author Anxiaobo <xbantcl@gmail.com>
 * @date 2016-06-13
 */

use Decorate\Utils\Help;
use Decorate\Enum\ResCode;
use Decorate\Models\Area;
use Illuminate\Database\Capsule\Manager as DB;
use Decorate\Models\Shop;

class ShopModule extends BaseModule
{
    /**
     * 添加商铺.
     * 
     * @param array $data
     * @return \Illuminate\Database\Eloquent\static
     */
    public function add(array $data)
    {
        $shopData = array_intersect_key($data, Shop::$rules);
        if (!empty($shopData['avatar'])) {
            $shopData['avatar'] = Help::config('bucket')['avatar'] . $shopData['avatar'];
        }
        Help::translateAvatar($shopData);
        $shop = Shop::create($shopData);
        return $shop;
    }

    public function getList($uid)
    {
        $shops = Shop::where('uid', $uid)
            ->orderBy('id', 'DESC')
            ->get()->toArray();
        foreach ($shops as &$shop) {
            $shop['address'] = CommonModule::getInstance()->getAddressByArearId($shop['area_id']);
            unset($shop['area_id']);
        }
        return $shops;
    }
}
