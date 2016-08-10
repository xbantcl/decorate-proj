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
        Help::translateAvatar($shopData);
        $shop = Shop::create($shopData);
        return $shop;
    }

    public function getListByUserId($uid)
    {
        $shops = Shop::where('uid', $uid)
            ->orderBy('id', 'DESC')
            ->get()->toArray();
        /*
        foreach ($shops as &$shop) {
            $shop['address'] = CommonModule::getInstance()->getAddressByArearId($shop['area_id']);
            unset($shop['area_id']);
        }*/
        return $shops;
    }

    public function getList($start = 0, $limit = 15)
    {
        $query = Shop::orderBy('id', 'DESC');
        if ($start > 0) {
            $query = $query->where('id', '<', $start);
        }
        $shops = $query->take($limit + 1)->get()->toArray();
        $more = 0;
        if (!$shops) {
            return ['start' => $start, 'more' => $more, 'list' => (object)[]];
        }
        if (count($shops) > $limit) {
            $more = 1;
            array_pop($shops);
        }
        foreach ($shops as &$shop) {
            $shop['works'] = WorksModule::getInstance()->getList($shop['id'])['data'];
        }
        unset($shop);
        return ['start' => $start, 'more' => $more, 'list' => $shops];
    }
}
