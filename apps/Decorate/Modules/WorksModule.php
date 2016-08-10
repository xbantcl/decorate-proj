<?php namespace Decorate\Modules;
/**
 * ShopModule class.
 *
 * @author Anxiaobo <xbantcl@gmail.com>
 * @date 2016-06-13
 */

use Decorate\Utils\Help;
use Decorate\Enum\ResCode;
use Illuminate\Database\Capsule\Manager as DB;
use Decorate\Models\Works;
use Decorate\Enum\FileType;
use Decorate\Models\Shop;
use Decorate\Models\ShopWorks;

class WorksModule extends BaseModule
{
    /**
     * 添加作品.
     * 
     * @param array $data
     * @return \Illuminate\Database\Eloquent\static
     */
    public function add(array $data)
    {
        try {
            $worksData = array_intersect_key($data, Works::$rules);
            $shop = Shop::find($data['shop_id']);
            if (!$shop instanceof Shop) {
                return ResCode::formatError(ResCode::SHOP_NOT_EXIST);
            }
            DB::beginTransaction();
            $works = Works::create($worksData);
            if (!empty($data['fileList']) && !empty($data['bucket'])) {
                $ret = FileModule::getInstance()->add($works->id, $data['fileList'], FileType::WORKS_FILE, $data['bucket']);
                if (isset($ret['code'])) {
                    DB::rollback();
                    return $ret;
                }
                $works->fileList = $ret;
            }
            ShopWorks::create(['shop_id' => $data['shop_id'], 'works_id' => $works->id]);
        } catch (\Exception $e) {
            DB::rollback();
            return ResCode::formatError(ResCode::ADD_WORKS_FILE_FAILED, $e->getMessage());
        }
        DB::commit();
        return $works;
    }

    public function formatWorks(array $works)
    {
        $dataList = [];
        if (empty($works)) {
            return $dataList;
        }
        foreach ($works as $item) {
            if (!isset($dataList[$item['id']])) {
                $tmp = $item;
                unset($tmp['file_id']);
                unset($tmp['file_url']);
                $dataList[$item['id']] = $tmp;
            }
            if (!empty($item['file_id']) && !empty($item['file_url'])) {
                $dataList[$item['id']]['fileList'][] = ['id' => $item['file_id'], 'url' => $item['file_url']];
            }
        }
        return $dataList;
    }

    public function getList($shopId, $start = 0, $limit = 15)
    {
        $query = Works::join('shop_works as sw', 'sw.works_id', '=', 'works.id')
            ->leftjoin('works_file as wf', 'wf.works_id', '=', 'works.id')
            ->select('works.id', 'works.intr', 'works.address', 'works.insert_time', 'wf.file_id', 'wf.file_url')
            ->where('sw.shop_id', $shopId)
            ->orderBy('id', 'DESC');
        if ($start > 0) {
            $query = $query->where('works.id', '<', $start);
        }
        $works = $query->take(($limit + 1) * 9)->get()->toArray();
        $more = 0;
        if (!$works) {
            return ['start' => 0, 'more' => $more, 'data' => (object)[]];
        }
        $worksList = array_values($this->formatWorks($works));
        if (count($worksList) > $limit) {
            $more = 1;
            $worksList = array_slice($worksList, 0, $limit);
        }
        $start = end($worksList)['id'];
        return ['start' => $start, 'more' => $more, 'data' => $worksList];
    }
}
