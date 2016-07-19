<?php namespace Decorate\Modules;
/**
 * SyncModule class.
 *
 * @author Anxiaobo <xbantcl@gmail.com>
 * @date 2016-06-13
 */

use Decorate\Utils\Help;
use Decorate\Enum\ResCode;
use Decorate\Models\Area;

class CommonModule extends BaseModule
{
    /**
     * 通过地区id获取具体地址.
     * 
     * @param integer $areaId
     * 
     * @return string
     */
    public function getAddressByArearId($areaId) {
        $address = Area::leftjoin('area as a', 'a.id', '=', 'area.parent_id')
            ->leftjoin('area as ar', 'ar.id', '=', 'a.parent_id')
            ->select('ar.name as first', 'a.name as second', 'area.name as three')
            ->where('area.id', $areaId)
            ->get()->toArray();
        if (!$address) {
            return false;
        }
        $addressName = '';
        foreach ($address as $name) {
            if ($name) {
                $addressName .= $name . '-';
            }
        }
        return substr($addressName, 0, -1);
    }
}
