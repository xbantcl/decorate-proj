<?php namespace Decorate\Modules;
/**
 * CollectionModule class.
 * 
 * @author Anxiaobo <xbantcl@gmail.com>
 * @date 2016-06-13
 */

use Decorate\Models\Collection;
use Decorate\Enum\ResCode;
use Decorate\Enum\CollectionType;
use Decorate\Redis\DiaryRedis;
use Decorate\Redis\DiscussRedis;

class CollectionModule extends BaseModule
{
    /**
     * 添加收藏.
     * 
     * @param array $data
     * 
     * @return \Illuminate\Database\Eloquent\static
     */
    public function add(array $data) {
        $collection = Collection::select('id')->where('data_id', $data['data_id'])->where('type', $data['type'])->where('uid', $data['uid'])->first();
        if ($collection instanceof Collection) {
            return ResCode::formatError(ResCode::COLLECTION_EXIST);
        }
        $collectionData = array_intersect_key($data, Collection::$rules);
        try {
            Collection::create($collectionData);
        } catch (\Exception $e) {
            return ResCode::formatError(ResCode::COLLECTION_FAILED);
        }
        if (CollectionType::DIARY_TYPE ==  $data['type']) {
            DiaryRedis::getInstance()->collection($data['data_id'], $data['uid']);
        } elseif (CollectionType::DISCUSS_TYPE == $args['type']) {
            DiscussRedis::getInstance()->collection($data['data_id'], $data['uid']);
        }
        return true;
    }

    /**
     * 获取收藏列表.
     * 
     * @param integer $uid
     * @param integer $type
     * @param integer $start
     * @param integer $limit
     * 
     * @return array
     */
    public function getList($uid, $type, $start = 0, $limit = 15) {
        $query = Collection::select('id', 'data_id')
            ->where('uid', $uid)
            ->where('type', $type)
            ->orderBy('id', 'desc');
        if ($start > 0) {
            $query = $query->where('id', '<', $start);
        }
        $collections = $query->take(($limit + 1))->get()->toArray();
        $more = 0;
        if (!$collections) {
            return ['start' => $start, 'more' => $more, 'data' => (object)[]];
        }
        if (count($collections) > $limit) {
            $more = 1;
            array_pop($collections);
            $start = end($collections)['id'];
        }
        $colIds = [];
        foreach ($collections as $collection) {
            $colIds[] = $collection['data_id'];
        }
        if (CollectionType::DIARY_TYPE == $type) {
            $data = DiaryModule::getInstance()->getDiaryByIds($colIds);
            $dataList = array_map(function($diary) {
                $diary['isCollected'] = DiaryRedis::getInstance()->isCollection($diary['id'], $uid);
                return $diary;
            }, $data);
        } elseif (CollectionType::DISCUSS_TYPE == $type) {
            $data = DiscussModule::getInstance()->getDiscussByIds($colIds);
            $dataList = array_map(function ($discuss) {
                $discuss['isCollected'] = DiscussRedis::getInstance()->isCollection($discuss['id'], $uid);
                return $discuss;
            }, $data);
        } else {
            $dataList = [];
        }
        return ['start' => $start, 'more' => $more, 'data' => $dataList];
    }

    public function delete($uid, $type, $dataId)
    {
        $ret = Collection::where('uid', $uid)->where('type', $type)->where('data_id', $dataId)->delete();
        if ($ret) {
            if (CollectionType::DIARY_TYPE ==  $type) {
                DiaryRedis::getInstance()->uncollection($dataId, $data['uid']);
            } elseif (CollectionType::DISCUSS_TYPE == $type) {
                DiscussRedis::getInstance()->uncollection($dataId, $data['uid']);
            }
        }
    }
}