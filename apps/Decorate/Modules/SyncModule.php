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

class SyncModule extends BaseModule
{
    /**
     * 格式化地区列表.
     * 
     * @param array $areaList
     * @param integer $parentId
     * 
     * @return array
     */
    public function formatAreas($areaList, $parentId = 0) {
        $areaTree = [];
        $parentIds = [];
        if (empty($areaList) && 0 == $parentId) {
            return $areaTree;
        }
        foreach ($areaList as $index => $area) {
            if ($parentId == $area['parent_id']) {
                $areaId = $area['id'];
                unset($area['parent_id']);
                $areaTree[$areaId] = $area;
                $parentIds[] = $areaId;
                unset($areaList[$index]);
            }
        }
        if (empty($areaTree) || empty($areaList)) {
            return array_values($areaTree);
        }
        foreach ($parentIds as $parentId) {
            $value = $this->formatAreas($areaList, $parentId);
            if ($value) {
                $areaTree[$parentId]['childList'] = $value;
            }
        }
        return array_values($areaTree);
    }

    public function getAreaTree()
    {
        $areaList = Area::select('id', 'name', 'parent_id')->get()->toArray();
        return $this->formatAreas($areaList);
    }

    public function getVersion()
    {
        return 1.1;
    }
}
