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
        } catch (\Exception $e) {
            DB::rollback();
            return ResCode::formatError(ResCode::ADD_WORKS_FILE_FAILED);
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

    public function getList($uid, $start, $limit)
    {
        $query = Works::join('works_file as wf', 'wf.works_id', '=', 'works.id')
            ->select('works.id', 'works.intr', 'works.address', 'works.insert_time', 'wf.file_id', 'wf.file_url')
            ->where('works.uid', $uid)
            ->orderBy('id', 'DESC');
        if ($start > 0) {
            $query = $query->where('works.id', '<', $start);
        }
        $works = $query->take(($limit + 1) * 9)->get()->toArray();
        $worksList = array_values($this->formatWorks($works));
        $more = 0;
        if (count($worksList) > $limit) {
            $more = 1;
            $worksList = array_slice($worksList, 0, $limit);
        }
        $start = end($worksList)['id'];
        return ['start' => $start, 'more' => $more, 'data' => $worksList];
    }
}
